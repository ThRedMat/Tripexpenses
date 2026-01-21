<?php
session_start();

// --- 1. CONFIG ET INCLUDES ---
require '../../includes/PHPMailer/src/PHPMailer.php';
require '../../includes/PHPMailer/src/SMTP.php';
require '../../includes/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$config = include('../../includes/config.php');
// require_once '../../includes/db_connect.php'; 

// --- 2. SÉCURITÉ ---
if (!isset($_SESSION['pseudo'])) {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
} else {
    $pseudo = $_SESSION['pseudo'];
    $stmt = $conn->prepare("SELECT id FROM users WHERE pseudo = ?");
    $stmt->bind_param("s", $pseudo);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $userId = $result['id'] ?? null;
    if (!$userId) {
        echo "Utilisateur introuvable.";
        exit();
    }
}

// Récupération Ville User
$userStmt = $conn->prepare("SELECT ville FROM users WHERE id = ?");
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$userData = $userStmt->get_result()->fetch_assoc();
$userVille = $userData['ville'] ?? 'Non défini';

// --- 3. INFOS VOYAGE ---
if (!isset($_GET['id'])) {
    echo "Aucun voyage spécifié.";
    exit();
}
$trip_id = $_GET['id'];

$sql = "SELECT * FROM trip WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $trip_id, $userId);
$stmt->execute();
$trip = $stmt->get_result()->fetch_assoc();

if (!$trip) {
    echo "Voyage non trouvé.";
    exit();
}

$devise = $trip['devise'];

// --- GESTION ÉTAT DU VOYAGE (Ta logique existante) ---
$today = new DateTime();
$start_date = new DateTime($trip['start_date']);
$end_date = new DateTime($trip['end_date']);
$grace_period_days = 30;
$grace_period_end = (clone $end_date)->modify("+{$grace_period_days} days");

$trip_state = '';
$can_modify = true;
$status_message = '';
$days_remaining = 0;

if ($today < $start_date) {
    $trip_state = 'a-venir';
} elseif ($today >= $start_date && $today <= $end_date) {
    $trip_state = 'en-cours';
} elseif ($today > $end_date && $today <= $grace_period_end) {
    $trip_state = 'termine';
    $days_remaining = $today->diff($grace_period_end)->days + 1;
    $status_message = "Voyage terminé - Modifiable jusqu'au " . $grace_period_end->format('d/m/Y');
} else {
    $trip_state = 'archive';
    $can_modify = false;
    $status_message = "Voyage archivé - Lecture seule";
}

// --- 4. RÉCUPÉRATION DÉPENSES ---
$sql = "SELECT e.id, e.category_id, e.amount, e.expense_date, e.description, e.lieu, c.name as category
        FROM expenses e 
        JOIN categories c ON e.category_id = c.id 
        WHERE e.trip_id = ? 
        ORDER BY e.expense_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $trip_id);
$stmt->execute();
$expenses_result = $stmt->get_result();

$expenses = [];
$total_depenses_quotidiennes = 0;
$supplements_transport = 0;
$supplements_hebergement = 0;
$total_expenses = 0; // Total global pour info

// Pour le Graphique
$data_graphique = [];

while ($row = $expenses_result->fetch_assoc()) {
    $expenses[] = $row;
    $total_expenses += $row['amount'];

    // TRI LOGIQUE
    if ($row['category'] === 'Transport') {
        $supplements_transport += $row['amount'];
    } elseif ($row['category'] === 'Hébergement') {
        $supplements_hebergement += $row['amount'];
    } else {
        // Budget quotidien
        $total_depenses_quotidiennes += $row['amount'];

        // Données Graphique (Uniquement vie quotidienne)
        $cat = $row['category'];
        if (!isset($data_graphique[$cat])) $data_graphique[$cat] = 0;
        $data_graphique[$cat] += $row['amount'];
    }
}

// Données Graphique JSON
$json_labels = json_encode(array_keys($data_graphique));
$json_values = json_encode(array_values($data_graphique));


// --- 5. CALCULS FINANCIERS ---
$budget_total = floatval($trip['budget_total']);

$transport_initial = $trip['transport_reserved'] ? floatval($trip['transport_cost']) : 0;
$accommodation_initial = $trip['accommodation_reserved'] ? floatval($trip['accommodation_cost']) : 0;

$transport_final = $transport_initial + $supplements_transport;
$accommodation_final = $accommodation_initial + $supplements_hebergement;

$budget_restant = $budget_total - $total_depenses_quotidiennes;
$percentage_used = $budget_total > 0 ? round(($total_depenses_quotidiennes / $budget_total) * 100, 2) : 0;
$total_trip_cost = $transport_final + $accommodation_final + $total_depenses_quotidiennes;

// --- 6. CATÉGORIES POUR MODALE ---
$sql_categories = "SELECT id, name FROM categories ORDER BY name ASC";
$result = $conn->query($sql_categories);
$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}
$stmt->close();

// Fonctions utilitaires
function format_date($date)
{
    if (empty($date)) return '';
    return (new DateTime($date))->format('d/m/Y');
}
function format_money($amount, $currency = 'EUR')
{
    return number_format($amount, 2, ',', ' ') . ' ' . htmlspecialchars($currency);
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <title>Détails du Voyage - <?= htmlspecialchars($trip['destination']) ?></title>

    <link rel="stylesheet" href="css/trip_details_finish.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>
    <?php if (isset($_GET['msg'])): ?>
        <?php
        $msg = $_GET['msg'];
        $text = "";
        $icon = "✅";
        if ($msg == 'added') {
            $text = "Dépense ajoutée !";
        } elseif ($msg == 'updated') {
            $text = "Dépense modifiée !";
            $icon = "✏️";
        } elseif ($msg == 'deleted') {
            $text = "Dépense supprimée.";
            $icon = "🗑️";
        }
        ?>
        <div id="toast" class="toast-notification"><span class="toast-icon"><?= $icon ?></span><span><?= htmlspecialchars($text) ?></span></div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toast = document.getElementById('toast');
                setTimeout(() => {
                    toast.classList.add('show');
                }, 100);
                setTimeout(() => {
                    toast.classList.remove('show');
                }, 3500);
                window.history.replaceState({}, document.title, window.location.pathname + window.location.search.replace(/[\?&]msg=[^&]+/, '').replace(/^&/, '?'));
            });
        </script>
    <?php endif; ?>

    <button onclick="topFunction()" id="myBtn" title="Go to top"><i class="fas fa-chevron-up"></i></button>

    <div class="container">

        <div class="header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:15px;">
            <div style="display:flex; align-items:center; gap:15px; flex:1;">
                <a href="finish_trip.php" class="btn-back-circle" title="Retour">←</a>
                <div>
                    <h1 style="margin:0; font-size:24px; text-align:left;">
                        <?= htmlspecialchars($trip['destination']) ?>
                        <span class="badge <?= $trip_state ?>" style="font-size:0.5em; vertical-align:middle;">
                            <?= htmlspecialchars(str_replace('-', ' ', ucfirst($trip_state))) ?>
                        </span>
                    </h1>
                    <p class="dateVoyage" style="color:#666; margin:0; text-align:left; background:none; padding:0;">
                        📅 Du <?= format_date($trip['start_date']) ?> au <?= format_date($trip['end_date']) ?>
                    </p>
                </div>
            </div>

            <form action="excel.php" method="post" class="download-form">
                <input type="hidden" name="trip_id" value="<?php echo htmlspecialchars($trip_id); ?>">
                <button type="submit" class="btn btn-excel" style="background:#217346; color:white; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:600;"><i class="fas fa-file-excel"></i> Excel</button>
            </form>
        </div>

        <?php if (!empty($status_message)): ?>
            <div class="status-alert <?= $trip_state === 'termine' ? 'warning' : 'danger' ?> info-alert" style="background: <?= $trip_state === 'termine' ? '#fff3cd' : '#f8d7da' ?>; padding:15px; border-radius:10px; margin-bottom:20px; display:flex; gap:10px; align-items:center;">
                <span style="font-size: 24px;"><?= $trip_state === 'termine' ? '⏰' : '🔒' ?></span>
                <div>
                    <strong style="color: <?= $trip_state === 'termine' ? '#856404' : '#721c24' ?>;">
                        <?= htmlspecialchars($status_message) ?>
                    </strong>
                </div>
            </div>
        <?php endif; ?>

        <div class="budget-section" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap:20px; margin-bottom:30px;">
            <div class="budget-card main" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color:white; padding:20px; border-radius:15px;">
                <h3 style="margin-top:0;">💰 Budget Argent de Poche</h3>
                <div class="amount" style="font-size:2em; font-weight:bold;"><?= format_money($budget_total, $devise) ?></div>
                <div class="detail" style="opacity:0.9;">Dépensé : <?= format_money($total_depenses_quotidiennes, $devise) ?> (<?= $percentage_used ?>%)</div>
                <div class="progress-bar" style="background:rgba(255,255,255,0.3); height:8px; border-radius:4px; margin-top:10px;">
                    <div class="progress-fill" style="width: <?= min($percentage_used, 100) ?>%; background:white; height:100%; border-radius:4px;"></div>
                </div>
            </div>

            <div class="budget-card" style="background:white; padding:20px; border-radius:15px; box-shadow:0 4px 15px rgba(0,0,0,0.05); border:1px solid #eee;">
                <h3 style="color:#333; margin-top:0;">💵 Argent Restant</h3>
                <div class="amount" style="font-size:2em; font-weight:bold; color: <?= $budget_restant < 0 ? '#e74c3c' : '#27ae60' ?>;">
                    <?= format_money($budget_restant, $devise) ?>
                </div>
                <?php if ($budget_restant < 0): ?>
                    <div class="detail" style="color:#e74c3c;">⚠️ Dépassement de <?= format_money(abs($budget_restant), $devise) ?></div>
                <?php else: ?>
                    <div class="detail" style="color:#27ae60;">✅ Bilan final du voyage</div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (count($data_graphique) > 0): ?>
            <div style="background:white; padding:20px; border-radius:15px; box-shadow:0 4px 15px rgba(0,0,0,0.05); margin-bottom:30px; text-align:center;">
                <h3 style="margin-top:0; color:#333; margin-bottom:15px;">🍰 Répartition Dépenses</h3>
                <div style="max-width: 350px; margin: 0 auto; position: relative; height:250px;">
                    <canvas id="budgetChart"></canvas>
                </div>
            </div>
            <script>
                const ctx = document.getElementById('budgetChart').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: <?= $json_labels ?>,
                        datasets: [{
                            data: <?= $json_values ?>,
                            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#8e44ad', '#2ecc71'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            </script>
        <?php endif; ?>

        <?php if ($transport_final > 0 || $accommodation_final > 0): ?>
            <h3 style="border-bottom: 2px solid #eee; padding-bottom: 10px;">📋 Réservations & Coûts Fixes</h3>
            <p style="color: #666; font-size: 14px; margin-bottom: 15px;">ℹ️ Ces montants ne sont pas déduits de votre budget argent de poche.</p>
            <div class="info-boxes" style="display:flex; gap:15px; flex-wrap:wrap; margin-bottom:30px;">
                <?php if ($transport_final > 0): ?>
                    <div class="info-box" style="flex:1; background:white; padding:15px; border-radius:8px; border-left:4px solid #e74c3c; box-shadow:0 2px 5px rgba(0,0,0,0.05);">
                        <h4 style="color:#e74c3c; margin:0;">🚗 Transport</h4>
                        <p class="price" style="font-size:1.4em; font-weight:bold; margin:5px 0;"><?= format_money($transport_final, $devise) ?></p>
                        <?php if ($supplements_transport > 0): ?><small>(Dont +<?= format_money($supplements_transport, $devise) ?> sur place)</small><?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($accommodation_final > 0): ?>
                    <div class="info-box" style="flex:1; background:white; padding:15px; border-radius:8px; border-left:4px solid #f1c40f; box-shadow:0 2px 5px rgba(0,0,0,0.05);">
                        <h4 style="color:#f1c40f; margin:0;">🏨 Hébergement</h4>
                        <p class="price" style="font-size:1.4em; font-weight:bold; margin:5px 0;"><?= format_money($accommodation_final, $devise) ?></p>
                        <?php if ($supplements_hebergement > 0): ?><small>(Dont +<?= format_money($supplements_hebergement, $devise) ?> sur place)</small><?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="info-box" style="flex:1; background:#f8faff; padding:15px; border-radius:8px; border-left:4px solid #3498db; box-shadow:0 2px 5px rgba(0,0,0,0.05);">
                    <h4 style="color:#3498db; margin:0;">📊 Coût Total Voyage</h4>
                    <p class="price" style="font-size:1.4em; font-weight:bold; margin:5px 0;"><?= format_money($total_trip_cost, $devise) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <div class="expense-header" style="display:flex; justify-content:space-between; align-items:center; margin-top:40px; border-bottom:2px solid #eee; padding-bottom:10px;">
            <h3 style="margin:0;">🧾 Dépenses sur place</h3>
            <?php if ($can_modify): ?>
                <button onclick="openModal()" class="btn" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; padding:10px 20px; border:none; border-radius:8px; cursor:pointer;">+ Ajouter</button>
            <?php else: ?>
                <span class="badge archive" style="background:#ccc; padding:8px 15px; border-radius:20px;">🔒 Archivé</span>
            <?php endif; ?>
        </div>

        <table class="expenses-table" style="width:100%; border-collapse:separate; border-spacing:0 10px; margin-top:10px;">
            <thead>
                <tr>
                    <th style="text-align:left; padding:10px; color:#666;">Date</th>
                    <th style="text-align:left; padding:10px; color:#666;">Catégorie</th>
                    <th style="text-align:left; padding:10px; color:#666;">Description</th>
                    <th style="text-align:left; padding:10px; color:#666;">Lieu</th>
                    <th style="text-align:right; padding:10px; color:#666;">Montant</th>
                    <?php if ($can_modify): ?><th style="text-align:right; padding:10px; color:#666;">Actions</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (count($expenses) > 0): ?>
                    <?php foreach ($expenses as $expense): ?>
                        <tr style="background:white; box-shadow:0 2px 5px rgba(0,0,0,0.05);">
                            <td data-label="Date" style="padding:15px; border-radius:10px 0 0 10px;"><?= format_date($expense['expense_date']) ?></td>
                            <td data-label="Catégorie" style="padding:15px;">
                                <?php if ($expense['category'] === 'Transport' || $expense['category'] === 'Hébergement'): ?>
                                    <span class="badge-fixed"><?= htmlspecialchars($expense['category']) ?> (Hors budget)</span>
                                <?php else: ?>
                                    <?= htmlspecialchars($expense['category']) ?>
                                <?php endif; ?>
                            </td>
                            <td data-label="Description" style="padding:15px;"><?= htmlspecialchars($expense['description']) ?></td>
                            <td data-label="Lieu" style="padding:15px;"><?= htmlspecialchars($expense['lieu']) ?></td>
                            <td data-label="Montant" style="padding:15px; text-align:right;"><strong><?= format_money($expense['amount'], $devise) ?></strong></td>

                            <?php if ($can_modify): ?>
                                <td data-label="Actions" style="padding:15px; text-align:right; border-radius:0 10px 10px 0;">
                                    <div style="display:flex; justify-content:flex-end; gap:8px;">
                                        <button class="btn-icon edit" onclick='openEditModal(
                                        <?= $expense['id'] ?>, 
                                        "<?= $expense['category_id'] ?>", 
                                        "<?= $expense['amount'] ?>", 
                                        "<?= $expense['expense_date'] ?>", 
                                        "<?= addslashes($expense['lieu']) ?>", 
                                        "<?= addslashes(str_replace(array("\r", "\n"), " ", $expense['description'])) ?>"
                                    )'>✏️</button>
                                        <a href="delete_expense_finish.php?id=<?= $expense['id'] ?>&trip_id=<?= $trip_id ?>" class="btn-icon delete" onclick="return confirm('Supprimer ?');">🗑️</a>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="padding:20px; text-align:center; color:#999;">Aucune dépense.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php $existingRating = $trip['feedback_rating'] ?? 0;
        $existingComment = $trip['feedback_comment'] ?? ''; ?>
        <div class="feedback-section" style="margin-top:40px; background:white; padding:25px; border-radius:15px; box-shadow:0 4px 15px rgba(0,0,0,0.05);">
            <h3 style="margin-top:0;">✍️ Votre ressenti sur ce voyage</h3>
            <form id="feedbackForm" action="save_feedback.php" method="post">
                <input type="hidden" name="trip_id" value="<?= htmlspecialchars($trip_id) ?>">
                <div class="star-rating" style="font-size:30px; margin-bottom:15px;">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" <?= (isset($existingRating) && $i == $existingRating) ? 'checked' : '' ?> <?= (isset($existingRating) && $existingRating) ? 'disabled' : '' ?> style="display:none;">
                        <label for="star<?= $i ?>" style="cursor:pointer; color:#ccc;">★</label>
                    <?php endfor; ?>
                </div>
                <div class="form-group" style="margin-bottom:15px;">
                    <textarea name="comment" id="comment" rows="4" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;" placeholder="Votre ressenti..." <?= $existingComment ? 'readonly' : '' ?>><?= htmlspecialchars($existingComment) ?></textarea>
                </div>
                <div class="form-actions">
                    <?php if ($existingRating || $existingComment): ?>
                        <button type="button" id="editFeedbackBtn" class="btn-submit" style="background:#f39c12; color:white; border:none; padding:10px 20px; border-radius:5px; cursor:pointer;">✏️ Modifier</button>
                    <?php else: ?>
                        <button type="submit" class="btn-submit" style="background:#667eea; color:white; border:none; padding:10px 20px; border-radius:5px; cursor:pointer;">💾 Enregistrer</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

    </div>

    <div id="addExpenseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">💸 Ajouter une dépense</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="expenseForm" action="add_expense_finish.php" method="post">
                    <input type="hidden" name="trip_id" value="<?= htmlspecialchars($trip_id) ?>">
                    <input type="hidden" id="expense_id" name="expense_id" value="">

                    <div class="form-group">
                        <label>Catégorie *</label>
                        <select id="cat_select" name="category_id" required>
                            <option value="">Sélectionnez</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Montant (<?= htmlspecialchars($devise) ?>) *</label>
                        <input type="number" id="form_amount" name="amount" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Date *</label>
                        <input type="date" id="form_date" name="expense_date" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label>Lieu *</label>
                        <input type="text" id="form_lieu" name="lieu" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea id="form_desc" name="description"></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-submit" id="btnSubmit">✓ Ajouter</button>
                        <button type="button" class="btn-cancel" onclick="closeModal()">✕ Annuler</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // --- MODAL LOGIC ---
        const modal = document.getElementById('addExpenseModal');
        const form = document.getElementById('expenseForm');
        const title = document.getElementById('modalTitle');
        const btnSubmit = document.getElementById('btnSubmit');

        function openModal() {
            modal.style.display = 'block';
            form.action = "add_expense_finish.php";
            form.reset();
            document.getElementById('expense_id').value = "";
            title.innerText = "💸 Ajouter une dépense";
            btnSubmit.innerText = "✓ Ajouter";
        }

        function openEditModal(id, catId, amount, date, lieu, desc) {
            modal.style.display = 'block';
            form.action = "edit_expense_finish.php";
            title.innerText = "✏️ Modifier la dépense";
            btnSubmit.innerText = "✓ Modifier";

            document.getElementById('expense_id').value = id;
            document.getElementById('cat_select').value = catId;
            document.getElementById('form_amount').value = amount;
            document.getElementById('form_date').value = date;
            document.getElementById('form_lieu').value = lieu;
            document.getElementById('form_desc').value = desc;
        }

        function closeModal() {
            modal.style.display = 'none';
        }
        window.onclick = function(e) {
            if (e.target == modal) closeModal();
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
        });

        // --- FEEDBACK STARS LOGIC ---
        const feedbackForm = document.getElementById('feedbackForm');
        const stars = feedbackForm.querySelectorAll('.star-rating input');
        const labels = feedbackForm.querySelectorAll('.star-rating label');
        const textarea = feedbackForm.querySelector('textarea');
        const editBtn = document.getElementById('editFeedbackBtn');

        function updateStars(rating) {
            labels.forEach((label, index) => {
                label.style.color = (index < rating) ? '#ffc107' : '#ccc';
            });
        }

        const checkedStar = feedbackForm.querySelector('.star-rating input:checked');
        updateStars(checkedStar ? parseInt(checkedStar.value) : 0);

        stars.forEach(star => {
            star.addEventListener('change', () => updateStars(parseInt(star.value)));
        });

        if (editBtn) {
            editBtn.addEventListener('click', () => {
                stars.forEach(star => {
                    star.disabled = false;
                    star.style.display = 'inline-block';
                }); // Fix display
                textarea.readOnly = false;
                editBtn.outerHTML = '<button type="submit" class="btn-submit" style="background:#667eea; color:white; border:none; padding:10px 20px; border-radius:5px; cursor:pointer;">💾 Enregistrer</button>';
            });
        }

        // --- SCROLL TO TOP ---
        const mybutton = document.getElementById("myBtn");
        window.onscroll = function() {
            scrollFunction();
        };

        function scrollFunction() {
            if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
                mybutton.style.display = "block";
            } else {
                mybutton.style.display = "none";
            }
        }

        function topFunction() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    </script>
</body>

</html>
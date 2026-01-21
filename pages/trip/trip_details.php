<?php
session_start();

// --- 1. CONFIGURATION & INCLUDES ---
require '../../includes/PHPMailer/src/PHPMailer.php';
require '../../includes/PHPMailer/src/SMTP.php';
require '../../includes/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$config = include('../../includes/config.php');
// require_once '../../includes/db_connect.php'; 

// --- 2. SÉCURITÉ & USER ---
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

// --- 3. INFOS VOYAGE (VERSION SIMPLE) ---
if (!isset($_GET['id'])) {
    echo "Aucun voyage spécifié.";
    exit();
}
$trip_id = $_GET['id'];

// Requête simple sans jointure d'image
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

while ($row = $expenses_result->fetch_assoc()) {
    $expenses[] = $row;

    // LOGIQUE DE TRI
    if ($row['category'] === 'Transport') {
        $supplements_transport += $row['amount'];
    } elseif ($row['category'] === 'Hébergement') {
        $supplements_hebergement += $row['amount'];
    } else {
        $total_depenses_quotidiennes += $row['amount'];
    }
}

// --- PRÉPARATION DONNÉES GRAPHIQUE ---
$data_graphique = [];
foreach ($expenses as $ex) {
    if ($ex['category'] !== 'Transport' && $ex['category'] !== 'Hébergement') {
        $cat = $ex['category'];
        if (!isset($data_graphique[$cat])) {
            $data_graphique[$cat] = 0;
        }
        $data_graphique[$cat] += $ex['amount'];
    }
}

$json_labels = json_encode(array_keys($data_graphique));
$json_values = json_encode(array_values($data_graphique));

// --- 5. CALCULS ---
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
    <title>Détails - <?= htmlspecialchars($trip['destination']) ?></title>
    <link rel="stylesheet" href="css/trip_details.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php if (isset($_GET['msg'])): ?>
        <?php
        $msg = $_GET['msg'];
        $text = "";
        $icon = "✅";
        $alert_type = "success";

        if ($msg == 'added') {
            $text = "Dépense ajoutée avec succès !";
        } elseif ($msg == 'updated') {
            $text = "Dépense modifiée avec succès !";
            $icon = "✏️";
        } elseif ($msg == 'trip_updated') {
            $text = "Paramètres du voyage mis à jour !";
            $icon = "⚙️";
        } elseif ($msg == 'deleted') {
            $text = "Dépense supprimée.";
            $icon = "🗑️";
            $alert_type = "danger";
        }
        ?>

        <div id="toast" class="toast-notification <?= $alert_type ?>">
            <span class="toast-icon"><?= $icon ?></span>
            <span><?= htmlspecialchars($text) ?></span>
        </div>

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

    <div class="container">

        <div class="header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <div style="display:flex; align-items:center; gap:15px;">
                <a href="trip.php" class="btn-back-circle" title="Retour">←</a>
                <div>
                    <h1 style="margin:0; font-size:24px; text-align:center;">
                        <?= htmlspecialchars($trip['destination']) ?>
                        <span class="badge <?= strtolower(str_replace(' ', '-', $trip['status'])) ?>" style="font-size:0.5em; vertical-align:middle;">
                            <?= htmlspecialchars($trip['status']) ?>
                        </span>
                    </h1>
                    <p class="dateVoyage" style="color:#666; margin:0; text-align:center; background:none; padding:0;">
                        📅 Du <?= format_date($trip['start_date']) ?> au <?= format_date($trip['end_date']) ?>
                    </p>
                </div>
            </div>

            <div class="header-actions">
                <button class="btn-settings" onclick="openTripSettings()" title="Modifier le voyage">
                    ⚙️ <span class="btn-text">Paramètres</span>
                </button>
                <button class="btn-add-main" onclick="openExpenseModal()">
                    <span class="plus-icon">+</span> <span class="btn-text">Ajouter une dépense</span>
                </button>
            </div>
        </div>

        <div class="budget-section" style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:30px;">
            <div class="budget-card main" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color:white; padding:20px; border-radius:15px;">
                <h3 style="margin-top:0;">💰 Budget Argent de Poche</h3>
                <div class="amount" style="font-size:2em; font-weight:bold;"><?= format_money($budget_total, $devise) ?></div>
                <div class="detail" style="opacity:0.9;">Dépensé : <?= format_money($total_depenses_quotidiennes, $devise) ?> (<?= $percentage_used ?>%)</div>
                <div class="progress-bar" style="background:rgba(255,255,255,0.3); height:8px; border-radius:4px; margin-top:10px;">
                    <div class="progress-fill" style="width: <?= min($percentage_used, 100) ?>%; background:white; height:100%; border-radius:4px;"></div>
                </div>
            </div>

            <div class="budget-card" style="background:white; padding:20px; border-radius:15px; box-shadow:0 4px 15px rgba(0,0,0,0.05);">
                <h3 style="color:#333; margin-top:0;">💵 Argent Restant</h3>
                <div class="amount" style="font-size:2em; font-weight:bold; color: <?= $budget_restant < 0 ? '#e74c3c' : '#27ae60' ?>;">
                    <?= format_money($budget_restant, $devise) ?>
                </div>
                <?php if ($budget_restant < 0): ?>
                    <div class="detail" style="color:#e74c3c;">⚠️ Dépassement de <?= format_money(abs($budget_restant), $devise) ?></div>
                <?php elseif ($budget_restant < ($budget_total * 0.2)): ?>
                    <div class="detail" style="color:#e67e22;">⚠️ Attention ! Budget faible.</div>
                <?php else: ?>
                    <div class="detail" style="color:#27ae60;">✅ Profitez de votre voyage !</div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (count($data_graphique) > 0): ?>
            <div style="background:white; padding:20px; border-radius:15px; box-shadow:0 4px 15px rgba(0,0,0,0.05); margin-bottom:30px; text-align:center;">
                <h3 style="margin-top:0; color:#333; margin-bottom:15px;">🍰 Dépenses par Catégorie</h3>
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
                            borderWidth: 0,
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true
                                }
                            }
                        }
                    }
                });
            </script>
        <?php endif; ?>

        <?php if ($transport_final > 0 || $accommodation_final > 0): ?>
            <h3 style="border-bottom: 2px solid #eee; padding-bottom: 10px;">📋 Réservations & Coûts Fixes</h3>
            <p style="color: #666; font-size: 14px; margin-bottom: 15px;">ℹ️ Ces montants ne sont pas déduits de votre budget argent de poche.</p>

            <div class="info-boxes">
                <?php if ($transport_final > 0): ?>
                    <div class="info-box" style="border-left-color: #e74c3c;">
                        <h4 style="color: #e74c3c;">🚗 Transport</h4>
                        <p class="price"><?= format_money($transport_final, $devise) ?></p>
                        <?php if ($supplements_transport > 0): ?>
                            <small>(Dont +<?= format_money($supplements_transport, $devise) ?> sur place)</small>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($accommodation_final > 0): ?>
                    <div class="info-box" style="border-left-color: #f1c40f;">
                        <h4 style="color: #f1c40f;">🏨 Hébergement</h4>
                        <p class="price"><?= format_money($accommodation_final, $devise) ?></p>
                        <?php if ($supplements_hebergement > 0): ?>
                            <small>(Dont +<?= format_money($supplements_hebergement, $devise) ?> sur place)</small>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="info-box" style="border-left-color: #3498db; background:#f8faff;">
                    <h4 style="color: #3498db;">📊 Coût Total Voyage</h4>
                    <p class="price"><?= format_money($total_trip_cost, $devise) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <h3 style="margin-top:40px; border-bottom: 2px solid #eee; padding-bottom: 10px;">🧾 Historique des dépenses</h3>

        <table class="expenses-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Catégorie</th>
                    <th>Description</th>
                    <th>Lieu</th>
                    <th style="text-align:center;">Montant</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($expenses) > 0): ?>
                    <?php foreach ($expenses as $expense): ?>
                        <tr>
                            <td><?= format_date($expense['expense_date']) ?></td>
                            <td>
                                <?php if ($expense['category'] === 'Transport' || $expense['category'] === 'Hébergement'): ?>
                                    <span class="badge-fixed"><?= htmlspecialchars($expense['category']) ?> (Hors budget)</span>
                                <?php else: ?>
                                    <?= htmlspecialchars($expense['category']) ?>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($expense['description']) ?></td>
                            <td><?= htmlspecialchars($expense['lieu']) ?></td>
                            <td style="text-align:center;"><strong><?= format_money($expense['amount'], $devise) ?></strong></td>

                            <td style="text-align:center;">
                                <div style="display:flex; justify-content:flex-end; gap:8px;">
                                    <button class="btn-icon edit" onclick='openExpenseModal(
                                        <?= $expense['id'] ?>, 
                                        "<?= $expense['category_id'] ?>", 
                                        "<?= $expense['amount'] ?>", 
                                        "<?= $expense['expense_date'] ?>", 
                                        "<?= addslashes($expense['lieu']) ?>", 
                                        "<?= addslashes(str_replace(array("\r", "\n"), " ", $expense['description'])) ?>"
                                    )'>✏️</button>

                                    <a href="delete_expense.php?id=<?= $expense['id'] ?>&trip_id=<?= $trip_id ?>"
                                        class="btn-icon delete"
                                        onclick="return confirm('Supprimer cette dépense ?');">🗑️</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="empty-message">Aucune dépense enregistrée</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div id="expenseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">💸 Ajouter une dépense</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="expenseForm" action="add_expense.php" method="post">
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
                        <input type="date" id="form_date" name="expense_date" required min="<?= htmlspecialchars($trip['start_date']) ?>" max="<?= htmlspecialchars($trip['end_date']) ?>" value="<?= htmlspecialchars($trip['start_date']) ?>">
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

    <div id="editTripModal" class="modal">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);">
                <h2>⚙️ Paramètres du voyage</h2>
                <span class="close" onclick="closeTripSettings()">&times;</span>
            </div>

            <div class="modal-body">
                <form action="edit_trip_info.php" method="post">
                    <input type="hidden" name="trip_id" value="<?php echo $trip_id; ?>">

                    <div class="form-group">
                        <label>Destination</label>
                        <input type="text" value="<?php echo htmlspecialchars($trip['destination']); ?>" disabled style="background: #eee; cursor: not-allowed;">
                        <small style="color:#888">La destination ne peut pas être modifiée.</small>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_start">Début</label>
                            <input type="date" name="start_date" id="edit_start" value="<?php echo $trip['start_date']; ?>" min="<?php echo $trip['start_date']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_end">Fin</label>
                            <input type="date" name="end_date" id="edit_end" value="<?php echo $trip['end_date']; ?>" min="<?php echo $trip['start_date']; ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_budget">Budget Argent de poche (<?php echo $trip['devise']; ?>)</label>
                        <input type="number" name="budget_total" id="edit_budget" step="0.01" min="0" value="<?php echo $trip['budget_total']; ?>" required>
                    </div>

                    <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
                    <h4>Coûts Fixes (Déjà payés)</h4>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Transport (<?php echo htmlspecialchars($trip['devise']); ?>)</label>
                            <input type="number" name="transport_cost" step="0.01" min="0" value="<?php echo $trip['transport_cost']; ?>">
                        </div>
                        <div class="form-group">
                            <label>Hébergement (<?php echo htmlspecialchars($trip['devise']); ?>)</label>
                            <input type="number" name="accommodation_cost" step="0.01" min="0" value="<?php echo (float)$trip['accommodation_cost']; ?>" placeholder="0.00">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit" style="background: #4a5568;">💾 Mettre à jour</button>
                        <button type="button" class="btn-cancel" onclick="closeTripSettings()">Annuler</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('expenseModal');
        const form = document.getElementById('expenseForm');
        const title = document.getElementById('modalTitle');
        const btnSubmit = document.getElementById('btnSubmit');

        function openExpenseModal(id = null, catId = null, amount = null, date = null, lieu = null, desc = null) {
            modal.style.display = 'block';

            if (id) {
                form.action = "edit_expense_action.php";
                title.innerText = "✏️ Modifier la dépense";
                btnSubmit.innerText = "✓ Modifier";

                document.getElementById('expense_id').value = id;
                document.getElementById('cat_select').value = catId;
                document.getElementById('form_amount').value = amount;
                document.getElementById('form_date').value = date;
                document.getElementById('form_lieu').value = lieu;
                document.getElementById('form_desc').value = desc;
            } else {
                form.action = "add_expense.php";
                form.reset();
                document.getElementById('expense_id').value = "";

                const startDate = "<?= htmlspecialchars($trip['start_date']) ?>";
                document.getElementById('form_date').value = startDate;

                title.innerText = "💸 Ajouter une dépense";
                btnSubmit.innerText = "✓ Ajouter";
            }
        }

        function closeModal() {
            modal.style.display = 'none';
        }
        window.onclick = function(e) {
            if (e.target == modal) closeModal();
        }

        function openTripSettings() {
            document.getElementById('editTripModal').style.display = 'block';
        }

        function closeTripSettings() {
            document.getElementById('editTripModal').style.display = 'none';
        }
    </script>
</body>

</html>
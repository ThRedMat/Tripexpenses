<?php
session_start();
// Debug (à retirer plus tard)
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/header.php';

// Vérification de connexion
if (!isset($_SESSION['pseudo'])) {
    header("Location: " . BASE_URL . "pages/login.php");
    exit();
}
$pseudo = $_SESSION['pseudo'];

// 1. RÉCUPÉRATION DE L'ID USER
$stmt = $conn->prepare("SELECT id FROM users WHERE pseudo = ?");
$stmt->bind_param("s", $pseudo);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
if (!$user) {
    die("Erreur : Utilisateur introuvable");
}
$user_id = $user['id'];
$stmt->close();

// 2. RÉCUPÉRATION DES ANNÉES (Pour le filtre)
$years = [];
$sql_years = "SELECT DISTINCT YEAR(end_date) as yr FROM trip WHERE user_id = ? AND status = 'Terminé' ORDER BY yr DESC";
$stmt = $conn->prepare($sql_years);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res_years = $stmt->get_result();
while ($row = $res_years->fetch_assoc()) {
    $years[] = $row['yr'];
}
$stmt->close();

// 3. REQUÊTE PRINCIPALE
$sql = "SELECT 
            t.*, 
            d.image_url as auto_bg,
            (IFNULL(t.transport_cost, 0) + IFNULL(t.accommodation_cost, 0) + IFNULL(SUM(e.amount), 0)) as total_spent
        FROM trip t
        LEFT JOIN destinations d ON t.destination = d.search_term
        LEFT JOIN expenses e ON t.id = e.trip_id
        WHERE t.user_id = ? AND t.status = 'Terminé'";

// Gestion des paramètres SQL
$params = [$user_id];
$types = "i";

// --- FILTRE ANNÉE ---
$selected_year = $_GET['year'] ?? '';
if (!empty($selected_year)) {
    $sql .= " AND YEAR(t.end_date) = ?";
    $types .= "s";
    $params[] = $selected_year;
}

// --- FILTRE RATING ---
$selected_rating = $_GET['rating'] ?? '';
if (!empty($selected_rating)) {
    $sql .= " AND feedback_rating = ?";
    $types .= "i";
    $params[] = $selected_rating;
}

$sql .= " GROUP BY t.id ORDER BY t.end_date DESC";

// Exécution
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Erreur SQL : " . $conn->error);
}

// Liaison dynamique
$bind_names[] = $types;
for ($i = 0; $i < count($params); $i++) {
    $bind_name = 'bind' . $i;
    $$bind_name = $params[$i];
    $bind_names[] = &$$bind_name;
}
call_user_func_array(array($stmt, 'bind_param'), $bind_names);

$stmt->execute();
$result = $stmt->get_result();

$other_trips = [];
while ($row = $result->fetch_assoc()) {
    $other_trips[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Mes voyages terminés</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/finish_trip.css">

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/navbar.css">
</head>

<body>
    <div class="slideshow-container">
        <div class="slide active"></div>
        <div class="slide"></div>
        <div class="slide"></div>
        <div class="slide"></div>
        <div class="slide"></div>
    </div>

    <div class="container" style="margin-top: 40px;">

        <div class="page-header" style="text-align:center; margin-bottom:30px; color:white; text-shadow: 0 2px 5px rgba(0,0,0,0.5);">
            <h1>📸 Mes Souvenirs de Voyage</h1>
            <p>Revivez vos aventures passées</p>
        </div>

        <div class="trips-container">

            <form method="GET" action="" id="filterForm">
                <div class="filter-bar">
                    <label for="sort-year">📅 Année :</label>
                    <select id="sort-year" name="year" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Toutes</option>
                        <?php
                        foreach ($years as $year) {
                            $selected = ($selected_year == $year) ? 'selected' : '';
                            echo "<option value='$year' $selected>$year</option>";
                        }
                        ?>
                    </select>

                    <label for="sort-rating">⭐ Note :</label>
                    <select id="sort-rating" name="rating" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Toutes</option>
                        <?php
                        for ($i = 5; $i >= 1; $i--) {
                            $selected = ($selected_rating == $i) ? 'selected' : '';
                            echo "<option value='$i' $selected>$i étoile(s)</option>";
                        }
                        ?>
                    </select>
                </div>
            </form>

            <div class="trips-grid">
                <?php if (count($other_trips) > 0): ?>
                    <?php foreach ($other_trips as $trip): ?>
                        <?php
                        // --- IMAGE ---
                        $bg_image = "https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=1600&q=80";
                        $source = !empty($trip['bg_image']) ? $trip['bg_image'] : ($trip['auto_bg'] ?? '');
                        if (!empty($source)) {
                            $bg_image = $source;
                        }
                        ?>

                        <div class='trip-card finished-card' style='background-image: url("<?= htmlspecialchars($bg_image) ?>");'>
                            <div class='trip-overlay-gradient'>

                                <div class='card-header-top'>
                                    <div class='trip-destination-large'><?= htmlspecialchars($trip['destination']) ?></div>
                                    <div class='badge-finished'>✓ Terminé</div>
                                </div>

                                <div class='card-bottom-info'>
                                    <div class='trip-dates'>
                                        📅 Du <?= (new DateTime($trip['start_date']))->format('d/m/Y') ?> au <?= (new DateTime($trip['end_date']))->format('d/m/Y') ?>
                                    </div>

                                    <div class='trip-stats-row'>
                                        <div class='trip-rating'>
                                            <?php
                                            $rating = $trip['feedback_rating'] ?? 0;
                                            echo "<div class='trip-rating'>";
                                            for ($i = 1; $i <= 5; $i++) {
                                                if ($i <= $rating) {
                                                    echo "<span style='color: #ffd700; text-shadow: 0 1px 3px rgba(0,0,0,0.8); font-size:16px;'>★</span>";
                                                } else {
                                                    echo "<span style='color: rgba(255,255,255,0.3); font-size:16px;'>★</span>";
                                                }
                                            }
                                            echo "</div>";
                                            ?>
                                        </div>

                                        <div class='trip-cost'>
                                            💰 <?= number_format($trip['total_spent'] ?? 0, 2, ',', ' ') ?> €
                                        </div>
                                    </div>

                                    <a href='trip_details_finish.php?id=<?= htmlspecialchars($trip['id']) ?>' class='btn-revivre'>
                                        Revivre ce voyage
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column: 1/-1; text-align: center; color: white; background: rgba(0,0,0,0.3); padding: 20px; border-radius: 10px;">
                        <h3>Aucun voyage trouvé avec ces filtres 🕵️‍♂️</h3>
                        <a href="?" style="color: #81ecec;">Réinitialiser les filtres</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="js/finish_trip.js"></script>
</body>

</html>
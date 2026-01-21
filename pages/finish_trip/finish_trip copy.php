<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/header.php';

// Vérification de connexion
if (!isset($_SESSION['pseudo'])) {
      header("Location: " . BASE_URL . "pages/login.php");
      exit();
}

$pseudo = $_SESSION['pseudo'];

// Récupération de l'année sélectionnée et du rating
$selected_year = isset($_GET['year']) ? $_GET['year'] : '';
$selected_rating = isset($_GET['rating']) ? $_GET['rating'] : '';

$sql = "SELECT * 
        FROM trip 
        WHERE user_id = (
            SELECT id FROM users WHERE pseudo = ?
        ) 
        AND status = 'Terminé'";

$params = [$pseudo];
$types = "s";

if (!empty($selected_year)) {
      $sql .= " AND YEAR(end_date) = ?";
      $types .= "i";
      $params[] = $selected_year;
}

if (!empty($selected_rating)) {
      $sql .= " AND feedback_rating = ?";
      $types .= "i";
      $params[] = $selected_rating;
}

$sql .= " ORDER BY end_date DESC";

$stmt = $conn->prepare($sql);

// Liaison dynamique des paramètres
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();



$other_trips = [];
while ($row = $result->fetch_assoc()) {
      if ($row['status'] == 'Terminé') {
            $other_trips[] = $row;
      }
}

$stmt->close();
$conn->close();

function format_date($date)
{
      $dateObj = new DateTime($date);
      return $dateObj->format('d/m/Y');
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Mes voyages terminés - TripExpenses</title>
      <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
      <link rel="stylesheet" href="css/finish_trip.css">
      <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/navbar.css">
</head>

<body>
      <!-- Slideshow Background -->
      <div class="slideshow-container">
            <div class="slide active"></div>
            <div class="slide"></div>
            <div class="slide"></div>
            <div class="slide"></div>
            <div class="slide"></div>
      </div>

      <div class="container">
            <div class="page-header">
                  <h1>📸 Mes Souvenirs de Voyage</h1>
                  <p>Revivez vos aventures passées</p>
            </div>

            <div class="trips-container">
                  <div class="section-title">
                        <span>🏖️</span>
                        <span>Voyages Terminés</span>
                  </div>

                  <!-- Filtre par année -->
                  <div class="filter-bar">
                        <label for="sort-year">Trier par année :</label>
                        <select id="sort-year" name="year">
                              <option value="">Toutes</option>
                              <?php
                              foreach ($years as $year) {
                                    $selected = ($selected_year == $year) ? 'selected' : '';
                                    echo "<option value='$year' $selected>$year</option>";
                              }
                              ?>
                        </select>

                        <label for="sort-rating">Filtrer par rating :</label>
                        <select id="sort-rating" name="rating">
                              <option value="">Toutes</option>
                              <?php
                              for ($i = 5; $i >= 1; $i--) {
                                    $selected = (isset($_GET['rating']) && $_GET['rating'] == $i) ? 'selected' : '';
                                    echo "<option value='$i' $selected>$i étoile(s)</option>";
                              }
                              ?>
                        </select>
                  </div>

                  <div class="trips-grid">
                        <?php
                        foreach ($other_trips as $trip) {
                              echo "<div class='trip-card'>";
                              echo "<div class='trip-icon'></div>"; // Icône via JS
                              echo "<div class='trip-info'>";
                              echo "<div class='trip-destination'>" . htmlspecialchars($trip['destination']) . "</div>";

                              // Dates du voyage
                              echo "<div class='trip-dates'>📅 Du " . format_date($trip['start_date']) . " au " . format_date($trip['end_date']) . "</div>";

                              // ⭐️ Affichage du rating
                              $rating = $trip['feedback_rating'] ?? 0;
                              echo "<div class='trip-rating'>";
                              for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $rating) {
                                          echo "<span style='color: gold; font-size:18px;'>★</span>";
                                    } else {
                                          echo "<span style='color: rgba(255,255,255,0.5); font-size:18px;'>★</span>";
                                    }
                              }
                              echo "</div>";

                              echo "<div class='trip-status'>✓ Terminé</div>";
                              echo "</div>";
                              echo "<a href='trip_details_finish.php?id=" . htmlspecialchars($trip['id']) . "' class='btn-details'>Détails</a>";
                              echo "</div>";
                        }

                        ?>
                  </div>
            </div>
      </div>

      <a href="../home.php" class="back-button">← Retour</a>

      <script src="js/finish_trip.js"></script>
</body>

</html>
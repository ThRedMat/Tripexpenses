<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

// Vérification de la session ou du cookie
if (!isset($_SESSION['pseudo'])) {
      if (isset($_COOKIE['remember_token'])) {
            $token = $_COOKIE['remember_token'];
            $stmt = $conn->prepare("SELECT id, pseudo FROM users WHERE remember_token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($user = $result->fetch_assoc()) {
                  $_SESSION['user_id'] = $user['id'];
                  $_SESSION['pseudo'] = $user['pseudo'];
            } else {
                  header("Location: " . BASE_URL . "pages/login/login.php");
                  exit();
            }
      } else {
            header("Location: " . BASE_URL . "pages/login/login.php");
            exit();
      }
}

// 👉 Maintenant qu’on est sûr que l’utilisateur est connecté,
// on peut afficher du HTML
require_once __DIR__ . '/../includes/header.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>TravelBudget - Tableau de bord</title>

      <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/home.css">
</head>

<body>
      <!-- SLIDESHOW -->
      <div class="slideshow-container">
            <div class="slide active"></div>
            <div class="slide"></div>
            <div class="slide"></div>
            <div class="slide"></div>
      </div>

      <div class="hero-container">
            <div class="welcome-card">
                  <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['pseudo']); ?> 👋</h1>
                  <p>Retrouvez ici une vue d’ensemble de vos voyages et dépenses. Continuez votre aventure et suivez vos budgets !</p>
                  <a href="trip/trip.php" class="cta-button">✈️ Commencer votre voyage ici</a>
            </div>

            <div class="features-row">
                  <div class="feature-item">
                        <h3>🧾 Suivi des dépenses</h3>
                        <p>Notez chaque dépense par catégorie et visualisez la répartition de votre budget.</p>
                  </div>
                  <div class="feature-item">
                        <h3>📊 Rapports intelligents</h3>
                        <p>Analysez vos statistiques et découvrez vos tendances de dépenses par voyage.</p>
                  </div>
                  <div class="feature-item">
                        <h3>💼 Multi-voyages</h3>
                        <p>Organisez plusieurs voyages à la fois et comparez vos budgets.</p>
                  </div>
                  <div class="feature-item">
                        <h3>🌍 Conversion automatique</h3>
                        <p>Convertissez facilement vos dépenses en devise locale pour un suivi précis.</p>
                  </div>
            </div>
      </div>


      <script>
            let currentSlide = 0;
            const slides = document.querySelectorAll('.slide');

            function showSlide(n) {
                  slides[currentSlide].classList.remove('active');
                  currentSlide = (n + slides.length) % slides.length;
                  slides[currentSlide].classList.add('active');
            }
            setInterval(() => showSlide(currentSlide + 1), 6000);
      </script>
</body>

</html>
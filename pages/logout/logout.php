<?php
session_start();
include '../../includes/config.php';

// Supprimer toutes les variables de session
$_SESSION = [];

// Supprimer le cookie de session
if (ini_get("session.use_cookies")) {
  $params = session_get_cookie_params();
  setcookie(
    session_name(),
    '',
    time() - 42000,
    $params["path"],
    $params["domain"],
    $params["secure"],
    $params["httponly"]
  );
}

// Détruire la session
session_destroy();

// Supprimer aussi le cookie "remember_token"
setcookie("remember_token", "", time() - 3600, "/");

// Optionnel : enlever le token de la BDD
if (isset($_SESSION['user_id'])) {
  $stmt = $conn->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
  $stmt->bind_param("i", $_SESSION['user_id']);
  $stmt->execute();
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Déconnexion - À bientôt | TripExpenses</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="logout.css">
</head>

<body>

  <div class="background-container">
    <div class="bg-overlay"></div>
    <div class="bg-slide"></div>
  </div>
  <div id="particles-js"></div>

  <div class="logout-card">
    <div class="icon-wrapper">
      <i class="fas fa-hand-spock main-icon"></i>
    </div>

    <h1>Déconnexion réussie</h1>
    <p>Merci de votre visite ! Vos données sont en sécurité.<br>À très bientôt pour de nouvelles aventures.</p>

    <a href="../login/login.php" class="btn-login">
      <i class="fas fa-sign-in-alt"></i> Se reconnecter
    </a>
  </div>

  <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
  <script>
    particlesJS("particles-js", {
      "particles": {
        "number": {
          "value": 30
        },
        "color": {
          "value": "#ffffff"
        },
        "opacity": {
          "value": 0.2,
          "random": true
        },
        "size": {
          "value": 3,
          "random": true
        },
        "move": {
          "enable": true,
          "speed": 0.5,
          "direction": "top"
        }
      },
      "interactivity": {
        "enable": false
      }
    });
  </script>

</body>

</html>
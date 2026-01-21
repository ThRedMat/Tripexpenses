<?php
// On indique au navigateur que la page n'existe pas
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Destination Inconnue | TripExpenses</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="../assets/css/404.css">
</head>

<body>
    <div class="background-container">
        <div class="bg-overlay"></div>
        <div class="bg-slide"></div>
    </div>
    <div id="particles-js"></div>

    <div class="error-card">
        <div class="icon-wrapper">
            <i class="fas fa-suitcase-rolling luggage-icon"></i>
        </div>

        <div class="error-code">404</div>
        <h2>Destination introuvable</h2>

        <p class="description">
            Il semblerait que vous ayez pris un chemin de traverse. <br>
            Cette page a peut-être pris des vacances prolongées ! 🏖️
        </p>

        <a href="../pages/home.php" class="btn-home">
            <i class="fas fa-compass"></i> Retourner en lieu sûr
        </a>

        <div class="suggestions">
            <h3>Autres destinations possibles</h3>
            <div class="suggestion-links">
                <a href="../pages/home.php" class="suggestion-link">
                    <i class="fas fa-chart-line"></i> Tableau de bord
                </a>
                <a href="../pages/trip/trip.php" class="suggestion-link">
                    <i class="fas fa-suitcase"></i> Mes voyages
                </a>
                <a href="../pages/infos/infos.php" class="suggestion-link">
                    <i class="fas fa-user"></i> Mon profil
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS("particles-js", {
            "particles": {
                "number": {
                    "value": 40
                },
                "color": {
                    "value": "#ffffff"
                },
                "opacity": {
                    "value": 0.3,
                    "random": true
                },
                "size": {
                    "value": 3,
                    "random": true
                },
                "line_linked": {
                    "enable": true,
                    "distance": 150,
                    "color": "#ffffff",
                    "opacity": 0.1,
                    "width": 1
                },
                "move": {
                    "enable": true,
                    "speed": 1,
                    "direction": "none",
                    "random": false,
                    "out_mode": "out"
                }
            },
            "interactivity": {
                "enable": true,
                "mode": "repulse"
            }
        });
    </script>
</body>

</html>
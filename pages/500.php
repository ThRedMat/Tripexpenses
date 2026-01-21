<?php
// On indique aux robots et au navigateur que c'est bien une erreur serveur
http_response_code(500);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Oups ! Turbulences techniques | TripExpenses</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/500.css">
</head>

<body>

    <div class="background-container">
        <div class="bg-overlay"></div>
        <div class="bg-slide"></div>
    </div>
    <div id="particles-js"></div>


    <div class="error-card">
        <div class="main-icon-wrapper">
            <i class="fas fa-car-crash main-icon"></i>
        </div>
        <h1>500</h1>
        <h2>Oups ! Turbulences techniques.</h2>
        <p>
            Notre serveur traverse une zone de turbulences inattendues. Nos ingénieurs sont déjà sur le pont pour réparer la situation.
            <br><br>
            Merci de réessayer dans quelques instants.
        </p>
        <div class="btn-group">
            <button onclick="location.reload()" class="btn btn-primary">
                <i class="fas fa-sync-alt"></i> Réessayer
            </button>
            <a href="/" class="btn btn-secondary">
                <i class="fas fa-home"></i> Retour à l'accueil
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        // Configuration simple des particules
        particlesJS("particles-js", {
            "particles": {
                "number": { "value": 60 },
                "color": { "value": "#ffffff" },
                "opacity": { "value": 0.3, "random": true },
                "size": { "value": 3, "random": true },
                "line_linked": { "enable": false },
                "move": { "enable": true, "speed": 1, "direction": "top", "random": true, "out_mode": "out" }
            },
            "interactivity": { "enable": false }
        });
    </script>
</body>
</html>
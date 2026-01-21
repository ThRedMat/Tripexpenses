<?php
// Indique au navigateur que l'accès est refusé
http_response_code(403);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Zone Restreinte | TripExpenses</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/403.css">
</head>

<body>
    <div class="background-container">
        <div class="bg-overlay"></div>
        <div class="bg-slide"></div>
    </div>
    <div id="particles-js"></div>

    <div class="error-card">
        <div class="icon-wrapper">
            <i class="fas fa-user-lock main-icon"></i>
        </div>
        
        <h1>403</h1>
        <h2>Accès Interdit</h2>
        
        <p>
            Désolé, cette zone est réservée. <br>
            Il semble que vous n'ayez pas les documents nécessaires pour franchir cette frontière.
        </p>
        
        <a href="/monsite/pages/home.php" class="btn">
            <i class="fas fa-passport"></i> Retour à l'accueil
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS("particles-js", {
            "particles": {
                "number": { "value": 50 },
                "color": { "value": "#ffffff" },
                "opacity": { "value": 0.2, "random": true },
                "size": { "value": 3, "random": true },
                "line_linked": { "enable": false },
                "move": { "enable": true, "speed": 0.5, "direction": "none", "random": true, "out_mode": "out" }
            },
            "interactivity": { "enable": false }
        });
    </script>
</body>
</html>
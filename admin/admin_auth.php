<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

function require_role($roles)
{
    if (!in_array($_SESSION['role'], (array)$roles)) {
        // Message HTML
        echo "<h3>🚫 Accès refusé</h3>";
        echo "<p>Vous n'avez pas le rôle requis pour accéder à cette page.</p>";
        echo "<p><a href='javascript:history.back()'>⬅ Revenir à la page précédente</a></p>";

        // Redirection automatique après 10 secondes
        echo "<script>
                setTimeout(function() {
                    history.back();
                }, 10000); // 10000ms = 10 secondes
              </script>";

        // Arrêter l’exécution du script
        exit();
    }
}

<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';
// require_once __DIR__ . '/../../includes/db_connect.php'; // Décommente si besoin

if (!isset($_SESSION['pseudo'])) {
    die("Accès refusé.");
}

if (isset($_GET['id']) && isset($_GET['trip_id'])) {
    $id = intval($_GET['id']);
    $trip_id = intval($_GET['trip_id']);

    // On vérifie que la connexion existe
    if (!isset($conn)) { die("Erreur connexion BDD"); }

    // Suppression sécurisée
    $sql = "DELETE FROM expenses WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Succès : retour au tableau de bord
        header("Location: trip_details_finish.php?id=" . $trip_id . "&msg=deleted");
        exit();
    } else {
        echo "Erreur lors de la suppression.";
    }
} else {
    echo "ID manquant.";
}
?>
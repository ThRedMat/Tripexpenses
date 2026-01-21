<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';
// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['pseudo'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérez l'identifiant du voyage à clôturer
    $trip_id = $_POST['trip_id'];
    $closed_date = date('Y-m-d');

    // Mettez à jour le statut et la date de clôture du voyage dans la base de données
    $sql = "UPDATE trip SET status = 'Terminé', closed_date = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $closed_date, $trip_id);

    if ($stmt->execute()) {
        // Redirigez vers la page d'accueil après la mise à jour
        header("Location: ../finish_trip/finish_trip.php");
        exit();
    } else {
        echo "Erreur lors de la clôture du voyage.";
    }

    $stmt->close();
}

$conn->close();

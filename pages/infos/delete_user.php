<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    echo "error:not_logged_in";
    exit();
}

$id = $_SESSION['user_id'];

// Requête préparée pour supprimer l'utilisateur
$sql = "delete FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Détruire la session après suppression
    session_unset();
    session_destroy();
    echo "success";
} else {
    echo "error:delete_failed";
}

$stmt->close();
$conn->close();

<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit("Non autorisé");
}

$userId = (int) $_SESSION['user_id'];

// Récupérer l'avatar actuel
$stmt = $conn->prepare("SELECT avatar FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($avatar);
$stmt->fetch();
$stmt->close();

if (!empty($avatar)) {
    $filePath = __DIR__ . "/../../uploads/avatars/" . $avatar;

    // Supprimer le fichier s'il existe
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Supprimer l'info en BDD
    $stmt = $conn->prepare("UPDATE users SET avatar = NULL WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
}

echo "Avatar supprimé";

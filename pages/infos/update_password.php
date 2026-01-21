<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo "Non autorisé";
    exit();
}

$id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword     = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Vérification basique
    if ($newPassword !== $confirmPassword) {
        echo "Les mots de passe ne correspondent pas.";
        exit();
    }
    if (strlen($newPassword) < 8) {
        echo "Le mot de passe doit contenir au moins 8 caractères.";
        exit();
    }

    // Récupérer le mot de passe haché en base
    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();
    $stmt->close();

    // Vérifier l'ancien mot de passe
    if (!password_verify($currentPassword, $hashedPassword)) {
        echo "Mot de passe actuel incorrect.";
        exit();
    }

    // Hacher le nouveau mot de passe
    $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Mettre à jour en BDD
    $updateSql = "UPDATE users SET password = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("si", $newHashedPassword, $id);

    if ($updateStmt->execute()) {
        echo "Succès";
    } else {
        echo "Erreur lors de la mise à jour du mot de passe.";
    }

    $updateStmt->close();
    $conn->close();
}

<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit("Non autorisé");
}

if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $userId = (int) $_SESSION['user_id'];
    $uploadDir = __DIR__ . "/../../uploads/avatars/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Vérifier type MIME
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($_FILES['avatar']['tmp_name']);
    if (!in_array($fileType, $allowedTypes)) {
        exit("Format non autorisé");
    }

    // Vérifier la taille
    $maxSize = 2 * 1024 * 1024; // 2Mo
    if ($_FILES['avatar']['size'] > $maxSize) {
        exit("Fichier trop volumineux");
    }

    // Sécuriser l’extension
    $extension = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        exit("Extension interdite");
    }

    // Renommer le fichier
    $fileName = "avatar_" . $userId . "." . $extension;
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $filePath)) {
        // Enregistrer en BDD
        $stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
        $stmt->bind_param("si", $fileName, $userId);
        $stmt->execute();
        echo "Succès";
    } else {
        echo "Erreur upload";
    }
} else {
    echo "Aucun fichier reçu";
}

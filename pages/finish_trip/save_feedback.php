<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$trip_id = $_POST['trip_id'] ?? null;
$rating = $_POST['rating'] ?? null;
$comment = $_POST['comment'] ?? '';

if (!$trip_id || !$rating) {
    die("Données manquantes.");
}

// Vérifie que le voyage appartient à l'utilisateur
$stmt = $conn->prepare("SELECT id FROM trip WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $trip_id, $userId);
$stmt->execute();
$result = $stmt->get_result();
$tripExists = $result->fetch_assoc();

if (!$tripExists) {
    die("Voyage introuvable ou non autorisé.");
}

// Met à jour feedback dans trip
$updateStmt = $conn->prepare("UPDATE trip SET feedback_rating = ?, feedback_comment = ?, updated_at = NOW() WHERE id = ?");
$updateStmt->bind_param("isi", $rating, $comment, $trip_id);
$updateStmt->execute();

header("Location: trip_details_finish.php?id=$trip_id");
exit();

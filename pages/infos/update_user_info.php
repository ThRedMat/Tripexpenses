<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    echo "Erreur: Non authentifié";
    exit();
}

$userId = $_SESSION['user_id'];

// Récupérer les données POST
$firstName = $_POST['firstName'] ?? '';
$lastName = $_POST['lastName'] ?? '';
$pseudo = $_POST['pseudo'] ?? '';
$email = $_POST['email'] ?? '';
$country = $_POST['country'] ?? '';
$currency = $_POST['currency'] ?? '';
$ville = $_POST['ville'] ?? '';
$pays = $_POST['pays'] ?? '';

// Validation basique
if (empty($firstName) || empty($lastName) || empty($pseudo) || empty($email) || empty($pays) || empty($ville)) {
    echo "Erreur: Tous les champs sont requis";
    exit();
}

// Vérifier si le pseudo existe déjà (sauf pour l'utilisateur actuel)
$checkPseudoSql = "SELECT id FROM users WHERE pseudo = ? AND id != ?";
$checkStmt = $conn->prepare($checkPseudoSql);
$checkStmt->bind_param("si", $pseudo, $userId);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    echo "Erreur: Ce pseudo est déjà utilisé";
    exit();
}
$checkStmt->close();

// Vérifier si l'email existe déjà (sauf pour l'utilisateur actuel)
$checkEmailSql = "SELECT id FROM users WHERE mail = ? AND id != ?";
$checkEmailStmt = $conn->prepare($checkEmailSql);
$checkEmailStmt->bind_param("si", $email, $userId);
$checkEmailStmt->execute();
$checkEmailResult = $checkEmailStmt->get_result();

if ($checkEmailResult->num_rows > 0) {
    echo "Erreur: Cet email est déjà utilisé";
    exit();
}
$checkEmailStmt->close();

// Mise à jour des informations
$updateSql = "UPDATE users SET username = ?, lastname = ?, pseudo = ?, mail = ?, preferred_currency = ?, ville = ?, pays = ?  WHERE id = ?";
$updateStmt = $conn->prepare($updateSql);
$updateStmt->bind_param(
    "sssssssi", // 6 strings + 1 int à la fin (id)
    $firstName,
    $lastName,
    $pseudo,
    $email,
    $currency,
    $ville,
    $pays,
    $userId
);

if ($updateStmt->execute()) {
    // Mettre à jour la session
    $_SESSION['pseudo'] = $pseudo;
    echo "Succès";
} else {
    echo "Erreur: " . $updateStmt->error;
}

$updateStmt->close();
$conn->close();

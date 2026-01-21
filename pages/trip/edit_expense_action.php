<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';
// require_once __DIR__ . '/../../includes/db_connect.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $expense_id = intval($_POST['expense_id']); // L'ID de la dépense à modifier
    $trip_id = intval($_POST['trip_id']);
    
    $category_id = intval($_POST['category_id']);
    $amount = floatval($_POST['amount']);
    $expense_date = $_POST['expense_date'];
    $description = $_POST['description'];
    $lieu = $_POST['lieu'];

    // Mise à jour SQL
    $sql = "UPDATE expenses SET category_id=?, amount=?, expense_date=?, description=?, lieu=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("idsssi", $category_id, $amount, $expense_date, $description, $lieu, $expense_id);

    if ($stmt->execute()) {
        header("Location: trip_details.php?id=" . $trip_id . "&msg=updated");
        exit();
    } else {
        die("Erreur modification : " . $conn->error);
    }
}
?>
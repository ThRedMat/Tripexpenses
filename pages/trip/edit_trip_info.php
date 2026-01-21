<?php
// edit_trip_info.php
session_start();
require_once __DIR__ . '/../../includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $trip_id = intval($_POST['trip_id']);
    
    // Récupération des données
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $budget = floatval($_POST['budget_total']);
    
    // Gestion des coûts fixes (peuvent être vides)
    $transport_cost = !empty($_POST['transport_cost']) ? floatval($_POST['transport_cost']) : 0.00;
    $accommodation_cost = !empty($_POST['accommodation_cost']) ? floatval($_POST['accommodation_cost']) : 0.00;

    // Mise à jour SQL
    $sql = "UPDATE trip 
            SET start_date = ?, 
                end_date = ?, 
                budget_total = ?, 
                transport_cost = ?, 
                accommodation_cost = ? 
            WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        // "ssdddi" -> String, String, Double, Double, Double, Int
        $stmt->bind_param("ssdddi", $start_date, $end_date, $budget, $transport_cost, $accommodation_cost, $trip_id);
        
        if ($stmt->execute()) {
            // Succès : on recharge la page
            header("Location: trip_details.php?id=" . $trip_id . "&msg=trip_updated");
        } else {
            echo "Erreur update : " . $conn->error;
        }
        $stmt->close();
    }
}
?>
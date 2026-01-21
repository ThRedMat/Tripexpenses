<?php
// includes/search_city.php

// Affichage des erreurs pour le débogage
ini_set('display_errors', 0); // On cache les erreurs brutes pour ne pas casser le JSON
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once __DIR__ . '/config.php';

try {
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';

    if (strlen($q) < 2) {
        echo json_encode([]);
        exit;
    }

    // --- ASTUCE INTELLIGENTE ---
    // 1. On prend ce que l'utilisateur a tapé et on remplace ses tirets par des espaces
    // Ex: s'il tape "Mont-de", ça devient "Mont de"
    $cleanInput = str_replace('-', ' ', $q);
    
    // 2. On prépare le terme de recherche avec des jokers %
    $search = "%" . $cleanInput . "%";

    // 3. LA REQUÊTE SQL MODIFIÉE
    // REPLACE(search_term, '-', ' ') : On dit à SQL de transformer temporairement les tirets de la BDD en espaces
    // Ainsi "Mont-de-Marsan" devient "Mont de Marsan" le temps de la comparaison
    $sql = "SELECT search_term, image_url, country 
            FROM destinations 
            WHERE REPLACE(search_term, '-', ' ') LIKE ? 
            ORDER BY population DESC 
            LIMIT 5"; // J'ai augmenté la limite à 5 pour que tu la trouves plus facilement

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();

    $suggestions = [];
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = [
            'label' => $row['search_term'], // Ça affichera quand même le vrai nom "Mont-de-Marsan"
            'image' => $row['image_url']
        ];
    }

    echo json_encode($suggestions);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
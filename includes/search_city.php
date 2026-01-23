<?php
// includes/search_city.php

// 1. Configuration silencieuse pour ne pas casser le JSON
ini_set('display_errors', 0);
error_reporting(0); // On coupe tout affichage d'erreur brut
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/config.php';

try {
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';

    if (strlen($q) < 2) {
        echo json_encode([]);
        exit;
    }

    // 2. Nettoyage de la recherche (tirets -> espaces)
    $cleanInput = str_replace('-', ' ', $q);
    $search = "%" . $cleanInput . "%";

    // 3. LA REQUÊTE BLINDÉE
    // On sélectionne aussi 'city' et 'country' pour reconstruire le label si besoin
    $sql = "SELECT city, country, search_term, image_url 
            FROM destinations 
            WHERE city LIKE ? OR search_term LIKE ? 
            ORDER BY population DESC 
            LIMIT 6";

    $stmt = $conn->prepare($sql);
    // On cherche si le mot tapé est dans la VILLE ou dans le TERME COMPLET
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();

    $suggestions = [];

    // Chemin de l'image par défaut (au cas où)
    $defaultImage = '/TripExpenses/images/villes/default.jpg';

    while ($row = $result->fetch_assoc()) {

        // SÉCURITÉ : Si search_term est vide ou cassé, on le recrée proprement
        // Cela évite l'erreur JS "Format Ville, Pays incorrect"
        if (!empty($row['search_term']) && strpos($row['search_term'], ',') !== false) {
            $label = $row['search_term'];
        } else {
            $label = $row['city'] . ', ' . $row['country'];
        }

        // SÉCURITÉ IMAGE : Si vide, on met l'image par défaut
        $image = !empty($row['image_url']) ? $row['image_url'] : $defaultImage;

        $suggestions[] = [
            'label' => $label,
            'image' => $image
        ];
    }

    echo json_encode($suggestions);
} catch (Exception $e) {
    // En cas d'erreur grave, on renvoie un tableau vide pour ne pas planter le site
    echo json_encode([]);
}

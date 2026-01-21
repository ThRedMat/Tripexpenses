<?php
// proxy.php
header('Content-Type: application/json');
$q = urlencode($_GET['q']);
$url = "https://photon.komoot.io/api/?q=$q&limit=10&lang=fr";

// On récupère le contenu de l'API
$response = file_get_contents($url);
echo $response;
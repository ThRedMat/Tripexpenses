<?php
// admin_import.php (Version Correction Manuelle)
require_once 'includes/config.php'; 

// On augmente le temps max d'exécution car il y a 25 000 lignes
set_time_limit(300); 

echo "<h1>🚀 Importation des villes (Mode Manuel)...</h1>";

$txtFile = "cities15000.txt";

// 1. Vérification que tu as bien mis le fichier
if (!file_exists($txtFile)) {
    die("❌ <b>Erreur :</b> Je ne trouve pas le fichier <code>cities15000.txt</code> !<br>
    1. Télécharge-le ici : <a href='http://download.geonames.org/export/dump/cities15000.zip'>Lien GeoNames</a><br>
    2. Dézippe-le.<br>
    3. Place le fichier .txt à la racine de ton site.");
}

echo "Fichier trouvé ! Démarrage de l'insertion...<br>";

// 2. Préparation de l'insertion
$stmt = $conn->prepare("INSERT IGNORE INTO destinations (city, country, search_term, population) VALUES (?, ?, ?, ?)");

if (!$stmt) {
    die("Erreur SQL : " . $conn->error . " (Vérifie que tu as bien ajouté la colonne 'population' dans ta table !)");
}

$handle = fopen($txtFile, "r");
$count = 0;

// 3. Lecture ligne par ligne
while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
    // Les colonnes dans le fichier GeoNames :
    // [1] = Nom de la ville
    // [8] = Code Pays (FR, US, etc.)
    // [14] = Population
    
    $city = $data[1]; 
    $countryCode = $data[8]; 
    $population = (int)$data[14];
    
    // On crée le terme de recherche "Paris, FR"
    $searchTerm = $city . ", " . $countryCode; 

    $stmt->bind_param("sssi", $city, $countryCode, $searchTerm, $population);
    $stmt->execute();
    
    $count++;
    
    // Petite barre de chargement visuelle (un point toutes les 1000 villes)
    if ($count % 1000 == 0) {
        echo ". "; 
        flush(); // Force l'affichage immédiat
    }
}

fclose($handle);

// Optionnel : On supprime le fichier texte pour faire propre à la fin
// unlink($txtFile); 

echo "<h2>✅ Succès ! $count villes ont été importées dans ta base.</h2>";
echo "<a href='index.php'>Retour à l'accueil</a>";
?>
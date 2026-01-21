<?php
// Connexion à la base de données
$servername = "localhost";
$user = "root";
$password = "";
$dbname = "Tripexpenses";

$conn = new mysqli($servername, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
};

// Mail config
$mail_config = [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_user' => 'contact.tripexpenses@gmail.com',
    'smtp_pass' => 'xbzhhlflklfmsjja', // mot de passe application Gmail
    'smtp_port' => 587,
    'smtp_from_email' => 'no-reply@tripexpense.com',
    'smtp_from_name' => 'Tripexpenses'
];



// Détection automatique du chemin racine
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$scriptName = $_SERVER['SCRIPT_NAME'];
$projectFolder = '/Tripexpenses/';

// Constantes pour chemins absolus
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . $projectFolder);
define('BASE_URL', $protocol . $host . $projectFolder);

// Constantes pour dossiers spécifiques
define('INCLUDES_PATH', ROOT_PATH . 'includes/');
define('PAGES_PATH', ROOT_PATH . 'pages/');
define('IMAGES_PATH', ROOT_PATH . 'images/');
define('CSS_PATH', ROOT_PATH . 'css/');


// URLs pour les liens
define('IMAGES_URL', BASE_URL . 'images/');
define('CSS_URL', BASE_URL . 'css/');
define('JS_URL', BASE_URL . 'js/');
define('UPLOADS_URL', BASE_URL . 'uploads/');
define('AVATARS_URL', UPLOADS_URL . 'avatars/');


define('CURRENCY_API_KEY', '5b8162da81a344ea45d31de0');
define('CURRENCY_API_URL', 'https://v6.exchangerate-api.com/v6/');

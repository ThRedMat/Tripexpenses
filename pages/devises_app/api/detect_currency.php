<?php
header('Content-Type: application/json');

// --- 1️⃣ Déterminer l’IP du visiteur ---
$ip = $_SERVER['REMOTE_ADDR'] ?? '';

// --- 2️⃣ (Optionnel) Gestion XAMPP : IP locale -> pays par défaut
if ($ip === '127.0.0.1' || $ip === '::1') {
    echo json_encode(['country' => 'France', 'currency' => 'EUR']);
    exit;
}

// --- 3️⃣ Utiliser un service externe gratuit pour détecter le pays ---
$geoUrl = "https://ipapi.co/{$ip}/json/";

$response = @file_get_contents($geoUrl);
if (!$response) {
    echo json_encode(['country' => 'France', 'currency' => 'EUR']);
    exit;
}

$data = json_decode($response, true);
$countryCode = $data['country_code'] ?? 'FR';

// --- 4️⃣ Tableau de correspondance Pays → Devise ---
$countryToCurrency = [
    'FR' => 'EUR',
    'DE' => 'EUR',
    'ES' => 'EUR',
    'IT' => 'EUR',
    'PT' => 'EUR',
    'BE' => 'EUR',
    'NL' => 'EUR',
    'IE' => 'EUR',
    'FI' => 'EUR',
    'US' => 'USD',
    'CA' => 'CAD',
    'GB' => 'GBP',
    'AU' => 'AUD',
    'CH' => 'CHF',
    'JP' => 'JPY',
    'CN' => 'CNY',
    'IN' => 'INR',
    'BR' => 'BRL',
    'MX' => 'MXN',
    'RU' => 'RUB',
    'ZA' => 'ZAR',
    'SG' => 'SGD',
    'HK' => 'HKD'
];

$currency = $countryToCurrency[$countryCode] ?? 'USD';

echo json_encode([
    'country' => $data['country_name'] ?? 'Unknown',
    'currency' => $currency
]);

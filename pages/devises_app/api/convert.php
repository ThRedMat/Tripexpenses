<?php
require_once __DIR__ . '/../../../includes/config.php';

function getRates($baseCurrency)
{
    $apiUrl = CURRENCY_API_URL . CURRENCY_API_KEY . '/latest/' . urlencode($baseCurrency);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        error_log("CURL Error: " . $curlError);
        return null;
    }

    if ($httpCode === 200 && $response) {
        $data = json_decode($response, true);
        if (isset($data['conversion_rates'])) {
            return [
                'rates' => $data['conversion_rates'],
                'base' => $data['base_code']
            ];
        }
    }
    return null;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'convert') {
    $from = filter_input(INPUT_POST, 'from', FILTER_SANITIZE_STRING);
    $to = filter_input(INPUT_POST, 'to', FILTER_SANITIZE_STRING);
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);

    if (!$from || !$to || $amount === false || $amount < 0) {
        echo json_encode(['error' => 'Paramètres invalides']);
        exit;
    }

    $data = getRates($from);
    if ($data && isset($data['rates'][$to])) {
        $rate = $data['rates'][$to];
        $result = $amount * $rate;

        echo json_encode([
            'success' => true,
            'result' => round($result, 2),
            'rate' => round($rate, 4),
            'from' => $from,
            'to' => $to
        ]);
    } else {
        echo json_encode(['error' => 'Impossible de récupérer les taux. Vérifiez votre clé API.']);
    }
    exit;
}

echo json_encode(['error' => 'Requête invalide']);

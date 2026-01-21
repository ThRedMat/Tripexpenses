<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/header.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convertisseur de Devises - TripExpenses</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/navbar.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="slideshow-container">
        <div class="slide active"></div>
        <div class="slide"></div>
        <div class="slide"></div>
        <div class="slide"></div>
        <div class="slide"></div>
    </div>

    <div class="main-container">
        
        <div class="converter-card">
            
            <div class="card-header">
                <div class="icon-wrapper">
                    <i class="fas fa-coins"></i>
                </div>
                <h1>Convertisseur</h1>
                <div id="detectedCurrency" class="location-badge" style="display:none;">
                    <i class="fas fa-map-marker-alt"></i> Détecté : <span>-</span>
                </div>
            </div>

            <div class="conversion-body">
                
                <div class="currency-group">
                    <label>Je possède :</label>
                    <div class="input-row">
                        <input type="number" id="amount" value="1" min="0" step="0.01" placeholder="Montant">
                        <select id="fromCurrency">
                            <option value="EUR" selected>EUR - Euro</option>
                            <option value="USD">USD - Dollar</option>
                            <option value="GBP">GBP - Livre</option>
                            <option value="JPY">JPY - Yen</option>
                            <option value="CHF">CHF - Franc Suisse</option>
                            <option value="CAD">CAD - Dollar Can.</option>
                            <option value="AUD">AUD - Dollar Aus.</option>
                        </select>
                    </div>
                </div>

                <div class="swap-container">
                    <button class="btn-swap" id="swapBtn" onclick="swapCurrencies()">
                        <i class="fas fa-exchange-alt"></i>
                    </button>
                </div>

                <div class="currency-group">
                    <label>J'obtiens :</label>
                    <div class="input-row">
                        <input type="number" id="result" readonly placeholder="..." class="result-input">
                        <select id="toCurrency">
                            <option value="USD" selected>USD - Dollar</option>
                            <option value="EUR">EUR - Euro</option>
                            <option value="GBP">GBP - Livre</option>
                            <option value="JPY">JPY - Yen</option>
                            <option value="CHF">CHF - Franc Suisse</option>
                            <option value="CAD">CAD - Dollar Can.</option>
                            <option value="AUD">AUD - Dollar Aus.</option>
                        </select>
                    </div>
                </div>

                <div class="loading" id="loading"><i class="fas fa-spinner fa-spin"></i> Actualisation des taux...</div>
                <div class="error" id="error"></div>

            </div>

            <div class="result-footer success" id="resultBox" style="display:none;">
                <div class="big-result">
                    <span id="resultAmount">0.00</span> <span id="resultCurrencyCode">USD</span>
                </div>
                <div class="rate-info" id="rateInfo">1 EUR = 1.08 USD</div>
            </div>

        </div>
    </div>

    <script src="app.js"></script>
</body>
</html>
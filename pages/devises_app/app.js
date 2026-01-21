document.addEventListener("DOMContentLoaded", () => {
    
    /* =========================================
       1. GESTION DU DIAPORAMA DE FOND
       ========================================= */
    const slides = document.querySelectorAll(".slide");
    let currentSlide = 0;

    if (slides.length > 0) {
        setInterval(() => {
            slides[currentSlide].classList.remove("active");
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add("active");
        }, 5000);
    }

    /* =========================================
       2. LOGIQUE MÉTIER (TA CONFIGURATION API)
       ========================================= */
    
    // Sélection des éléments HTML
    const amountInput = document.getElementById("amount");
    const fromSelect = document.getElementById("fromCurrency");
    const toSelect = document.getElementById("toCurrency");
    const resultInput = document.getElementById("result");
    const swapBtn = document.getElementById("swapBtn");
    
    // Éléments de résultat et chargement
    const loadingDiv = document.getElementById("loading");
    const errorDiv = document.getElementById("error");
    const resultBox = document.getElementById("resultBox");
    const resultAmountSpan = document.getElementById("resultAmount");
    const resultCurrencySpan = document.getElementById("resultCurrencyCode");
    const rateInfoSpan = document.getElementById("rateInfo");

    let debounceTimer;

    // --- A. DÉTECTION AUTOMATIQUE DE LA DEVISE ---
    async function detectCurrency() {
        try {
            const response = await fetch("api/detect_currency.php");
            const data = await response.json();

            if (data.currency) {
                // Vérifie si l'option existe dans le select
                const optionExists = Array.from(fromSelect.options).some(
                    (opt) => opt.value === data.currency
                );

                if (optionExists) {
                    fromSelect.value = data.currency;
                    
                    // Mise à jour visuelle du badge "Localisation"
                    const detectedDiv = document.getElementById("detectedCurrency");
                    const detectedSpan = detectedDiv.querySelector("span"); // Le span à l'intérieur
                    if(detectedDiv && detectedSpan) {
                        detectedDiv.style.display = "inline-block";
                        detectedSpan.textContent = `${data.currency} (${data.country})`;
                    }

                    // Lance une première conversion
                    convertCurrency();
                }
            }
        } catch (error) {
            console.warn("Impossible de détecter la devise :", error);
        }
    }

    // --- B. FONCTION DE CONVERSION (VIA TON API PHP) ---
    function convertCurrency() {
        clearTimeout(debounceTimer);
        
        // Anti-rebond (attend 500ms après la dernière frappe)
        debounceTimer = setTimeout(() => {
            const amount = parseFloat(amountInput.value);
            const from = fromSelect.value;
            const to = toSelect.value;

            // Gestion cas vide ou négatif
            if (isNaN(amount) || amount < 0) {
                resultInput.value = "";
                resultBox.style.display = "none";
                return;
            }

            // Affichage état chargement
            loadingDiv.style.display = "block";
            errorDiv.style.display = "none";
            // resultBox.style.display = "none"; // Optionnel : laisser affiché l'ancien résultat ou non
            swapBtn.disabled = true;

            // Préparation des données pour ton PHP
            const formData = new FormData();
            formData.append("action", "convert");
            formData.append("from", from);
            formData.append("to", to);
            formData.append("amount", amount);

            // Appel à TON fichier PHP (qui contient la clé API)
            fetch("api/convert.php", {
                method: "POST",
                body: formData,
            })
            .then((response) => response.json())
            .then((data) => {
                loadingDiv.style.display = "none";
                swapBtn.disabled = false;

                if (data.error) {
                    errorDiv.style.display = "block";
                    errorDiv.textContent = data.error;
                    resultBox.style.display = "none";
                } else {
                    // 1. Remplir l'input readonly
                    resultInput.value = data.result;

                    // 2. Remplir le gros affichage en bas (Nouveau Design)
                    // On formate le chiffre proprement (ex: 1 250,50)
                    const formattedResult = parseFloat(data.result).toLocaleString("fr-FR", {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                    if(resultAmountSpan) resultAmountSpan.textContent = formattedResult;
                    if(resultCurrencySpan) resultCurrencySpan.textContent = data.to;
                    
                    if(rateInfoSpan) {
                        rateInfoSpan.textContent = `1 ${data.from} = ${data.rate} ${data.to}`;
                    }
                    
                    // Afficher la boîte de résultat
                    resultBox.style.display = "block";
                }
            })
            .catch((error) => {
                loadingDiv.style.display = "none";
                swapBtn.disabled = false;
                errorDiv.style.display = "block";
                errorDiv.textContent = "Erreur de connexion : " + error.message;
            });
        }, 500);
    }

    // --- C. FONCTION SWAP (INVERSER) ---
    window.swapCurrencies = function() {
        const temp = fromSelect.value;
        fromSelect.value = toSelect.value;
        toSelect.value = temp;
        
        // Petite animation de rotation CSS
        swapBtn.style.transform = "rotate(180deg)";
        setTimeout(() => { swapBtn.style.transform = "rotate(0deg)"; }, 300);

        convertCurrency();
    };

    // --- D. ÉCOUTEURS D'ÉVÉNEMENTS ---
    amountInput.addEventListener("input", convertCurrency);
    fromSelect.addEventListener("change", convertCurrency);
    toSelect.addEventListener("change", convertCurrency);

    // Lancement au démarrage
    detectCurrency();
});
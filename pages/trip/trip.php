<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/header.php';

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['pseudo'])) {
    header("Location: " . BASE_URL . "pages/login.php");
    exit();
}

$pseudo = $_SESSION['pseudo'];
$current_date = date('Y-m-d');

try {
    // 🔹 Récupère l'ID de l'utilisateur
    $user_sql = "SELECT id FROM users WHERE pseudo = ?";
    $user_stmt = $conn->prepare($user_sql);
    $user_stmt->bind_param("s", $pseudo);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $user = $user_result->fetch_assoc();
    $user_stmt->close();

    if (!$user) {
        throw new Exception("Utilisateur non trouvé");
    }

    $user_id = $user['id'];

    // 🔹 Met à jour automatiquement le statut des voyages terminés
    $update_sql = "UPDATE trip
                   SET status = 'Terminé' 
                   WHERE end_date < ? 
                   AND status != 'Terminé' 
                   AND user_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $current_date, $user_id);
    $update_stmt->execute();
    $update_stmt->close();

    // 🔹 Met à jour automatiquement le statut des voyages en cours
    $update_ongoing_sql = "UPDATE trip 
                           SET status = 'En cours' 
                           WHERE start_date <= ? 
                           AND end_date >= ? 
                           AND status = 'À venir' 
                           AND user_id = ?";
    $update_ongoing_stmt = $conn->prepare($update_ongoing_sql);
    $update_ongoing_stmt->bind_param("ssi", $current_date, $current_date, $user_id);
    $update_ongoing_stmt->execute();
    $update_ongoing_stmt->close();

    // 🔹 Récupère tous les voyages AVEC l'image de la ville (Jointure)
    // MODIFICATION ICI : Ajout du LEFT JOIN pour l'image
    $sql = "SELECT t.*, d.image_url as auto_bg 
            FROM trip t
            LEFT JOIN destinations d ON t.destination = d.search_term
            WHERE t.user_id = ? 
            ORDER BY 
                CASE 
                    WHEN t.status = 'En cours' THEN 1
                    WHEN t.status = 'À venir' THEN 2
                    WHEN t.status = 'Terminé' THEN 3
                END ASC,
                CASE 
                    WHEN t.status != 'Terminé' THEN t.start_date 
                END ASC,
                CASE 
                    WHEN t.status = 'Terminé' THEN t.start_date 
                END DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // 🔹 Catégorise les voyages
    $ongoing_trips = [];   // Voyages en cours
    $upcoming_trips = [];  // Voyages à venir
    $past_trips = [];      // Voyages terminés

    while ($row = $result->fetch_assoc()) {
        switch ($row['status']) {
            case 'En cours':
                $ongoing_trips[] = $row;
                break;
            case 'À venir':
                $upcoming_trips[] = $row;
                break;
            case 'Terminé':
                $past_trips[] = $row;
                break;
        }
    }

    $stmt->close();
} catch (Exception $e) {
    error_log("Erreur dans index.php : " . $e->getMessage());
    $ongoing_trips = [];
    $upcoming_trips = [];
    $past_trips = [];
}

// 🔹 Récupère les devises pour le sélecteur
$devisesSql = "SELECT id, code, name, symbol, union_flag ,country, is_main 
                    FROM currencies 
                    ORDER BY is_main DESC, name ASC LIMIT 10";
$stmt = $conn->prepare($devisesSql);
$stmt->execute();
$result = $stmt->get_result();

// Stockage des devises dans un tableau
$currencies = [];
while ($row = $result->fetch_assoc()) {
    $currencies[] = $row;
}

function format_date($date)
{
    if (empty($date)) return '';
    $dateObj = new DateTime($date);
    return $dateObj->format('d/m/Y');
}

function calculate_days_remaining($end_date)
{
    $now = new DateTime();
    $end = new DateTime($end_date);
    $diff = $now->diff($end);
    return $diff->days;
}

function calculate_trip_duration($start_date, $end_date)
{
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $diff = $start->diff($end);
    return $diff->days + 1; // +1 pour inclure le jour de départ
}

function calculate_days_before_start($start_date)
{
    $now = new DateTime();
    $now->setTime(0, 0, 0);

    $start = new DateTime($start_date);
    $start->setTime(0, 0, 0);

    if ($start < $now) return 0;

    $diff = $now->diff($start);
    return $diff->days;
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Accueil - TripExpenses</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/trip.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/navbar.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-text">
                <h1>🌍 TripExpenses</h1>
                <p>Gérez vos voyages et dépenses facilement</p>
            </div>

            <button class="btn-add-trip" onclick="openModal()">➕ Ajouter un voyage</button>

        </div>

        <div class="trip-grid">
            <?php
            if (count($ongoing_trips) > 0) {
                foreach ($ongoing_trips as $trip) {
                    // --- LOGIQUE IMAGE ---
                    $bg_image = "https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=1600&q=80"; // Image par défaut

                    // Priorité : 1. Image manuelle (bg_image) | 2. Image Auto BDD (auto_bg)
                    $source = !empty($trip['bg_image']) ? $trip['bg_image'] : ($trip['auto_bg'] ?? '');

                    if (!empty($source)) {
                        // trim() enlève les espaces et sauts de ligne invisibles au début et à la fin
                        $bg_image = trim($source);
                    }

                    // Affichage de la Card avec la bonne image
                    echo "<div class='trip-card current-trip-card' style='background-image: url(\"" . htmlspecialchars($bg_image) . "\");'>";

                    // Overlay sombre
                    echo "<div class='trip-overlay'>";

                    echo "<div class='trip-info-large'>";
                    echo "<div class='status-badge'>✈️ En cours</div>";
                    echo "<h2 class='trip-destination-large'>" . htmlspecialchars($trip['destination']) . "</h2>";
                    echo "<div class='trip-dates-large'>📅 Du " . format_date($trip['start_date']) . " au " . format_date($trip['end_date']) . "</div>";
                    echo "</div>";

                    echo "<div class='trip-actions-large'>";
                    echo "<a href='trip_details.php?id=" . htmlspecialchars($trip['id']) . "' class='btn btn-details-large'>Voir le tableau de bord</a>";

                    echo "<form action='close_trip.php' method='post' onsubmit='return confirm(\"Voulez-vous vraiment clôturer ce voyage ?\")' style='display:inline;'>";
                    echo "<input type='hidden' name='trip_id' value='" . htmlspecialchars($trip['id']) . "'>";
                    echo "<button type='submit' class='btn btn-close-large'>Clôturer</button>";
                    echo "</form>";
                    echo "</div>";

                    echo "</div>"; // Fin overlay
                    echo "</div>"; // Fin card
                }
            } else {
                echo "<div class='no-trip'>Aucun voyage en cours.</div>";
            }
            ?>
        </div>

        <?php if (count($upcoming_trips) > 0): ?>
            <div class="section-separator">
                <h2>🚀 Prochainement</h2>
                <div class="separator-line"></div>
            </div>
        <?php endif; ?>

        <div class="trip-grid upcoming-grid">
            <?php if (count($upcoming_trips) > 0): ?>
                <?php foreach ($upcoming_trips as $trip): ?>
                    <?php
                    $days_before = calculate_days_remaining($trip['start_date']);

                    // --- LOGIQUE IMAGE ---
                    $bg_image = "https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=1600&q=80"; // Défaut
                    $source = !empty($trip['bg_image']) ? $trip['bg_image'] : ($trip['auto_bg'] ?? '');

                    if (!empty($source)) {
                        $bg_image = $source;
                    }
                    ?>

                    <div class='trip-card upcoming-card'>
                        <div class="card-image" style="background-image: url('<?php echo htmlspecialchars($bg_image); ?>');">
                            <span class="trip-badge-overlay">Dans <?php echo $days_before; ?> jours</span>
                        </div>

                        <div class='trip-content'>
                            <div class="trip-header-row">
                                <span class="trip-destination"><?php echo htmlspecialchars($trip['destination']); ?></span>
                            </div>

                            <div class='trip-dates'>
                                📅 <?php echo format_date($trip['start_date']); ?>
                                au <?php echo format_date($trip['end_date']); ?>
                            </div>

                            <div class="trip-budget-preview">
                                <?php if (!empty($trip['budget'])): ?>
                                    <span class="budget-tag">💰 <?php echo number_format($trip['budget'], 0, ',', ' '); ?> €</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class='trip-actions'>
                            <a href='trip_details.php?id=<?php echo htmlspecialchars($trip['id']); ?>' class='btn btn-details'>Voir</a>

                            <form action='delete_trip.php' method='post' onsubmit='return confirm("Supprimer ce futur voyage ?")' style="display:inline;">
                                <input type='hidden' name='trip_id' value='<?php echo htmlspecialchars($trip['id']); ?>'>
                                <button type='submit' class='btn-icon-delete' title="Supprimer">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class='no-trip'>
                    <p>🌴 Pas de futur voyage planifié.</p>
                </div>
            <?php endif; ?>
        </div>

        <div id="addTripModal" class="modal">
            <div class="modal-content">

                <div class="modal-header">
                    <h2>✈️ Ajouter un nouveau voyage</h2>
                    <span class="close" onclick="closeModal()">&times;</span>
                </div>

                <div class="modal-body">
                    <form action="add_trip.php" method="post" id="tripForm">

                        <div class="form-section">
                            <div class="form-section-title">📍 Informations générales</div>

                            <div class="form-group" style="position: relative;">
                                <label for="destination">Destination *</label>
                                <input type="text"
                                    id="destination"
                                    name="destination"
                                    required
                                    placeholder="Tapez le début d'une ville (ex: Tok...)"
                                    autocomplete="off">

                                <div id="suggestions-box" class="suggestions-list"></div>
                                <small style="color: #666; font-size: 12px;">Sélectionnez une ville qui s'affiche dans la liste.</small>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="start_date">Date de début *</label>
                                    <input type="date" id="start_date" name="start_date"
                                        min="<?php echo date('Y-m-d'); ?>"
                                        value="<?php echo date('Y-m-d'); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="end_date">Date de fin *</label>
                                    <input type="date" id="end_date" name="end_date"
                                        min="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="budget">Budget prévu (€)</label>
                                <input type="number" id="budget" name="budget" step="0.01" min placeholder="Ex: 1500.00">
                            </div>

                            <div class="form-group">
                                <label for="devise">Devise locale *</label>
                                <div class="currency-selector-wrapper">
                                    <img id="currency-flag"
                                        src="<?php echo htmlspecialchars($currencies[0]['union_flag']); ?>"
                                        alt="Drapeau"
                                        title="<?php echo htmlspecialchars($currencies[0]['name']); ?>">

                                    <select id="devise" name="devise" class="devise" required>
                                        <option value="">Sélectionnez une devise</option>
                                        <?php foreach ($currencies as $c): ?>
                                            <option value="<?php echo htmlspecialchars($c['code']); ?>">
                                                <?php echo htmlspecialchars($c['name']) . ' (' . htmlspecialchars($c['code']) . ')'; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-section-title">🚗 Transport</div>

                            <div class="form-group">
                                <label for="transport">Mode de transport *</label>
                                <select id="transport" name="transport" required>
                                    <option value="">Sélectionnez un transport</option>
                                    <option value="Avion">✈️ Avion</option>
                                    <option value="Train">🚆 Train</option>
                                    <option value="Voiture">🚗 Voiture</option>
                                    <option value="Bus">🚌 Bus</option>
                                    <option value="Bateau">🚢 Bateau</option>
                                    <option value="Moto">🏍️ Moto</option>
                                    <option value="Velo">🚴 Vélo</option>
                                    <option value="Autre">🔄 Autre</option>
                                </select>
                            </div>

                            <div class="checkbox-group">
                                <input type="checkbox" id="has_transport_cost" name="has_transport_cost" onchange="toggleTransportPrice()">
                                <label for="has_transport_cost">J'ai déjà réservé mon transport</label>
                            </div>

                            <div class="price-input" id="transport_price_container" style="display: none;">
                                <div class="form-group">
                                    <label for="transport_price">Prix du transport (€)</label>
                                    <input type="number" id="transport_price" name="transport_price" step="0.01" placeholder="Ex: 250.00">
                                </div>
                            </div>
                        </div>

                        <div class="form-section" id="section-hebergement">
                            <div class="form-section-title">🏨 Hébergement</div>

                            <div class="form-group">
                                <label for="accommodation">Type d'hébergement *</label>
                                <select id="accommodation" name="accommodation" required>
                                    <option value="">Sélectionnez un hébergement</option>
                                    <option value="Hôtel">🏨 Hôtel</option>
                                    <option value="Auberge">🏠 Auberge de jeunesse</option>
                                    <option value="Airbnb">🏘️ Airbnb / Location</option>
                                    <option value="Camping">🏕️ Camping</option>
                                    <option value="Chez_habitant">👥 Chez l'habitant</option>
                                    <option value="Famille">👨‍👩‍👧‍👦 Famille / Amis</option>
                                    <option value="Autre">🔄 Autre</option>
                                </select>
                            </div>

                            <div class="checkbox-group">
                                <input type="checkbox" id="has_accommodation_cost" name="has_accommodation_cost" onchange="toggleAccommodationPrice()">
                                <label for="has_accommodation_cost">J'ai déjà réservé mon hébergement</label>
                            </div>

                            <div class="price-input" id="accommodation_price_container" style="display: none;">
                                <div class="form-group">
                                    <label for="accommodation_price">Prix de l'hébergement (€)</label>
                                    <input type="number" id="accommodation_price" name="accommodation_price" step="0.01" placeholder="Ex: 500.00">
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-submit">✓ Créer le voyage</button>
                            <button type="button" class="btn-cancel" onclick="closeModal()">✕ Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function openModal() {
                document.getElementById('addTripModal').style.display = 'block';
            }

            function closeModal() {
                document.getElementById('addTripModal').style.display = 'none';
            }

            function toggleTransportPrice() {
                const checkbox = document.getElementById('has_transport_cost');
                const container = document.getElementById('transport_price_container');
                const input = document.getElementById('transport_price');

                if (checkbox.checked) {
                    container.style.display = 'block';
                    input.required = true;
                } else {
                    container.style.display = 'none';
                    input.required = false;
                    input.value = '';
                }
            }

            function toggleAccommodationPrice() {
                const checkbox = document.getElementById('has_accommodation_cost');
                const container = document.getElementById('accommodation_price_container');
                const input = document.getElementById('accommodation_price');

                if (checkbox.checked) {
                    container.style.display = 'block';
                    input.required = true;
                } else {
                    container.style.display = 'none';
                    input.required = false;
                    input.value = '';
                }
            }

            // --- CONFIGURATION DES ÉLÉMENTS ---
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const sectionHebergement = document.getElementById('section-hebergement');
            const selectHebergement = document.getElementById('accommodation');

            // --- FONCTION UNIQUE POUR LES DATES ---
            function handleDates() {
                endDateInput.min = startDateInput.value;
                if (endDateInput.value && endDateInput.value < startDateInput.value) {
                    endDateInput.value = startDateInput.value;
                }

                if (startDateInput.value && endDateInput.value) {
                    const start = new Date(startDateInput.value);
                    const end = new Date(endDateInput.value);
                    const diff = Math.round((end - start) / (1000 * 60 * 60 * 24));

                    if (diff < 1) {
                        sectionHebergement.style.display = 'none';
                        selectHebergement.required = false;
                        selectHebergement.value = "";
                    } else {
                        sectionHebergement.style.display = 'block';
                        selectHebergement.required = true;
                    }
                }
            }

            startDateInput.addEventListener('change', handleDates);
            endDateInput.addEventListener('change', handleDates);
            handleDates();

            window.onclick = function(event) {
                const modal = document.getElementById('addTripModal');
                if (event.target == modal) closeModal();
            }

            // Validation
            document.getElementById('tripForm').addEventListener('submit', function(e) {
                const destination = document.getElementById('destination').value.trim();
                const parts = destination.split(',');

                if (parts.length < 2) {
                    e.preventDefault();
                    alert('❌ Veuillez entrer la destination au format: Ville, Pays\nExemple: Tokyo, Japon');
                    document.getElementById('destination').focus();
                    return false;
                }

                const ville = parts[0].trim();
                const pays = parts.slice(1).join(',').trim();

                if (!ville || !pays) {
                    e.preventDefault();
                    alert('❌ La ville et le pays ne peuvent pas être vides');
                    document.getElementById('destination').focus();
                    return false;
                }
            });

            // Feedback visuel
            document.getElementById('destination').addEventListener('input', function() {
                const value = this.value.trim();
                const parts = value.split(',');
                if (parts.length >= 2 && parts[0].trim() && parts[1].trim()) {
                    this.style.borderColor = '#28a745';
                    this.style.background = '#f0fff4';
                } else {
                    this.style.borderColor = '#e1e8ed';
                    this.style.background = '#f8f9fa';
                }
            });

            // AUTOCOMPLETE
            const destInput = document.getElementById('destination');
            const suggestionsBox = document.getElementById('suggestions-box');

            function debounce(func, wait) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }

            const searchDestination = async (query) => {
                if (query.length < 2) {
                    suggestionsBox.style.display = 'none';
                    return;
                }

                try {
                    const response = await fetch(`../../includes/search_city.php?q=${encodeURIComponent(query)}`);
                    const data = await response.json();

                    suggestionsBox.innerHTML = '';

                    if (data.length > 0) {
                        suggestionsBox.style.display = 'block';
                        suggestionsBox.className = 'suggestions-list';

                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'suggestion-item';
                            div.innerHTML = `🌍 <strong>${item.label}</strong>`;
                            div.onclick = function() {
                                destInput.value = item.label;
                                suggestionsBox.style.display = 'none';
                            };
                            suggestionsBox.appendChild(div);
                        });
                    } else {
                        suggestionsBox.style.display = 'none';
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    suggestionsBox.style.display = 'none';
                }
            };

            const debouncedSearch = debounce((e) => searchDestination(e.target.value.trim()), 300);
            destInput.addEventListener('input', debouncedSearch);

            document.addEventListener('click', function(e) {
                if (e.target !== destInput && e.target !== suggestionsBox) {
                    suggestionsBox.style.display = 'none';
                }
            });

            // Devises
            const select = document.getElementById('devise');
            const flag = document.getElementById('currency-flag');
            const defaultFlag = '../../images/icons/terre.webp';

            const currencyData = <?php
                                    $jsCurrencies = [];
                                    foreach ($currencies as $c) {
                                        $jsCurrencies[$c['code']] = [
                                            'flag' => $c['union_flag'],
                                            'name' => $c['name']
                                        ];
                                    }
                                    echo json_encode($jsCurrencies, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                                    ?>;

            function updateFlag() {
                if (select.value === '') {
                    flag.src = defaultFlag;
                    flag.alt = 'Aucune devise sélectionnée';
                    flag.title = 'Sélectionnez une devise';
                } else {
                    const selectedCurrency = currencyData[select.value];
                    flag.src = selectedCurrency.flag;
                    flag.alt = 'Drapeau ' + selectedCurrency.name;
                    flag.title = selectedCurrency.name;
                }
            }

            select.addEventListener('change', updateFlag);
            window.addEventListener('DOMContentLoaded', updateFlag);
        </script>

</body>

</html>
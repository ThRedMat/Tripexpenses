<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['pseudo'])) {
      header("Location: " . BASE_URL . "pages/login.php");
      exit();
}

$pseudo = $_SESSION['pseudo'];
$id = $_SESSION['user_id'];

// Requête pour récupérer le nombre de voyages et le statut
$sql = "
    SELECT u.trips_count, s.name AS status_name, s.icon, s.description
    FROM users u
    JOIN status s
      ON u.trips_count BETWEEN s.min_trips AND IFNULL(s.max_trips, u.trips_count)
    WHERE u.id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

$status = [
      'name' => 'Voyageur',
      'icon' => '',
      'trips_count' => 0,
      'description' => 'Bienvenue sur Trip Expense ! Commencez à enregistrer vos voyages pour découvrir votre statut.'
];

if ($row = $result->fetch_assoc()) {
      $status['name'] = $row['status_name'];
      $status['icon'] = $row['icon'];
      $status['trips_count'] = $row['trips_count'];
      $status['description'] = $row['description'];
}

// Récupérer les informations utilisateur
$userSql = "SELECT username, lastname, mail, pseudo, avatar, ville, pays FROM users WHERE id = ?";
$userStmt = $conn->prepare($userSql);
$userStmt->bind_param("i", $id);
$userStmt->execute();
$userResult = $userStmt->get_result();
if ($userRow = $userResult->fetch_assoc()) {
      $lastname = $userRow['lastname'];
      $mail = $userRow['mail'];
      $username = $userRow['username'];
      $avatar = $userRow['avatar'];
      $ville = $userRow['ville'];
      $pays = $userRow['pays'];
} else {
      // En cas d'erreur, rediriger vers la page de login
      header("Location: " . BASE_URL . "pages/login.php");
      exit();
}

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

// Récupérer la devise préférée de l'utilisateur
$userCurrencySql = "SELECT preferred_currency FROM users WHERE id = ?";
$userCurrencyStmt = $conn->prepare($userCurrencySql);
$userCurrencyStmt->bind_param("i", $id);
$userCurrencyStmt->execute();
$userCurrencyResult = $userCurrencyStmt->get_result();
$userPreferredCurrency = 'EUR'; // Valeur par défaut

if ($userCurrencyRow = $userCurrencyResult->fetch_assoc()) {
      $userPreferredCurrency = $userCurrencyRow['preferred_currency'];
}
$userCurrencyStmt->close();

$stmt->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Mes Informations - Trip Expense</title>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
      <link rel="stylesheet" href="css/infos.css">
</head>

<body>
      <!-- Particules flottantes -->
      <div class="floating-particles">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
      </div>


      <!-- Contenu principal -->
      <div class="main-container">
            <!-- Card Profil -->
            <div class="profile-card">
                  <div class="profile-avatar" id="profileAvatar">
                        <?php if (!empty($avatar)): ?>
                              <img src="<?php echo AVATARS_URL . htmlspecialchars($avatar); ?>"
                                    style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                        <?php else: ?>
                              <i class="fas fa-user"></i>
                        <?php endif; ?>
                        <input type="file" id="avatarInput" accept="image/*" style="display: none; cursor: pointer;">
                  </div>

                  <?php if (!empty($avatar)): ?>
                        <button id="deleteAvatarBtn" class="delete-btn">
                              <i class="fas fa-trash"></i>
                              Supprimer l’avatar</button>
                  <?php endif; ?>

                  <div class="profile-name" id="profileName"><?php echo $pseudo ?></div>
                  <div class="profile-role"><?php echo htmlspecialchars($status['icon'] . ' ' . $status['name']); ?></div>
                  <div class="profile-description"><?php echo htmlspecialchars($status['description']); ?></div>

                  <div class="profile-stats">
                        <a href="../finish_trip/finish_trip.php" class="stat-item">
                              <span class="stat-number"><?php echo (int)$status['trips_count']; ?></span>
                              <span class="stat-label">Voyages</span>
                        </a>
                  </div>

            </div>

            <!-- Section Informations -->
            <div class="info-section">
                  <div class="section-header">
                        <div class="section-icon">
                              <i class="fas fa-user-edit"></i>
                        </div>
                        <h2 class="section-title">Mes Informations</h2>
                  </div>

                  <form id="userInfoForm">
                        <div class="form-grid">
                              <div class="form-group">
                                    <i class="form-icon fas fa-user"></i>
                                    <input type="text" class="form-input" id="firstName" placeholder=" " value="<?php echo $username ?>" readonly>
                                    <label class="form-label" for="firstName">Prénom</label>
                              </div>

                              <div class="form-group">
                                    <i class="form-icon fas fa-user"></i>
                                    <input type="text" class="form-input" id="lastName" placeholder=" " value="<?php echo $lastname ?>" readonly>
                                    <label class="form-label" for="lastName">Nom</label>
                              </div>

                              <div class="form-group full-width">
                                    <i class="form-icon fas fa-user-tag"></i>
                                    <input type="text" class="form-input" id="pseudo" placeholder=" " value="<?php echo $pseudo ?>" readonly>
                                    <label class="form-label" for="pseudo">Pseudo</label>
                              </div>

                              <div class="form-group full-width">
                                    <i class="form-icon fas fa-envelope"></i>
                                    <input type="email" class="form-input" id="email" placeholder=" " value="<?php echo $mail ?>" readonly>
                                    <label class="form-label" for="email">Adresse e-mail</label>
                              </div>


                              <!-- Remplacer votre div existante par ceci -->
                              <div class="form-group full-width">
                                    <i class="form-icon fas fa-lock"></i>
                                    <input type="password" class="form-input" id="currentPassword" placeholder="" readonly>
                                    <label class="form-label" for="currentPassword">Mot de passe actuel</label>
                              </div>

                              <!-- Champs qui apparaissent en mode édition -->
                              <div class="form-group full-width password-edit-fields" style="display: none;">
                                    <i class="form-icon fas fa-key"></i>

                                    <div style="position: relative; width: 100%;">
                                          <input type="password" class="form-input" id="newPassword" placeholder=" ">
                                          <label class="form-label" for="newPassword">Nouveau mot de passe</label>

                                          <!-- Bouton pour voir/masquer le mot de passe -->
                                          <div class="password-toggle" onclick="togglePasswordVisibility('newPassword', 'toggleNewPassword')"
                                                style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                                <i class="fas fa-eye" id="toggleNewPassword"></i>
                                          </div>
                                    </div>
                              </div>


                              <!-- Confirmer le mot de passe -->
                              <div class="form-group full-width password-edit-fields" style="display: none;">
                                    <i class="form-icon fas fa-key"></i>

                                    <div style="position: relative; width: 100%;">
                                          <input type="password" class="form-input" id="confirmPassword" placeholder=" ">
                                          <label class="form-label" for="confirmPassword">Confirmer le mot de passe</label>

                                          <!-- Bouton pour voir/masquer le mot de passe -->
                                          <div class="password-toggle" onclick="togglePasswordVisibility('confirmPassword', 'toggleConfirmPassword')"
                                                style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                                <i class="fas fa-eye" id="toggleConfirmPassword"></i>
                                          </div>
                                    </div>
                              </div>

                              <div class="form-group">
                                    <i class="form-icon fas fa-globe"></i>
                                    <input type="text" class="form-input" id="ville" name="ville"
                                          placeholder="Commencez à taper une ville"
                                          value="<?php echo htmlspecialchars($ville); ?>" autocomplete="off" readonly>
                                    <label class="form-label" for="ville">Ville</label>
                                    <div id="villeSuggestions" class="autocomplete-suggestions"></div>

                              </div>


                              <div class="form-group">
                                    <i class="form-icon fas fa-flag"></i>
                                    <input type="text" class="form-input" id="pays" name="pays"
                                          placeholder="Pays" value="<?php echo htmlspecialchars($pays); ?>" autocomplete="off" readonly>
                                    <label class="form-label" for="pays">Pays</label>
                              </div>


                              <div class="form-group">
                                    <i class="form-icon fas fa-money-bill-wave"></i>

                                    <!-- Affichage en mode lecture seule -->
                                    <input type="text" class="form-input" id="currencyDisplay" placeholder=" "
                                          value="<?php
                                                      // Trouver la devise correspondante
                                                      foreach ($currencies as $c) {
                                                            if ($c['code'] === $userPreferredCurrency) {
                                                                  echo htmlspecialchars($c['name'] . ' (' . $c['code'] . ')');
                                                                  break;
                                                            }
                                                      }
                                                      ?>" readonly style="display: block;">

                                    <!-- Select en mode édition -->
                                    <select id="currency" class="form-input" disabled style="display: none;">
                                          <?php foreach ($currencies as $c): ?>
                                                <option value="<?php echo htmlspecialchars($c['code']); ?>"
                                                      <?php echo ($c['code'] === $userPreferredCurrency ? 'selected' : ''); ?>>
                                                      <?php echo htmlspecialchars($c['name']) . ' (' . htmlspecialchars($c['code']) . ')'; ?>
                                                </option>
                                          <?php endforeach; ?>
                                    </select>

                                    <label class="form-label" for="currency">Devise préférée</label>
                              </div>
                        </div>


                        <!-- Boutons en mode lecture -->
                        <div class="button-group" id="readOnlyButtons">
                              <button type="button" class="btn btn-primary" id="editBtn">
                                    <i class="fas fa-edit"></i>
                                    Modifier
                              </button>
                              <button type="button" class="btn btn-danger" id="deleteBtn">
                                    <i class="fas fa-trash"></i>
                                    Supprimer le compte
                              </button>
                        </div>

                        <!-- Boutons en mode édition -->
                        <div class="button-group hidden" id="editButtons">
                              <button type="button" class="btn btn-secondary" id="cancelBtn">
                                    <i class="fas fa-times"></i>
                                    Annuler
                              </button>
                              <button type="submit" class="btn btn-primary" id="saveBtn">
                                    <i class="fas fa-save"></i>
                                    Enregistrer
                              </button>
                        </div>
                  </form>
            </div>
      </div>


      <script src="js/script.js"></script>

</body>

</html>
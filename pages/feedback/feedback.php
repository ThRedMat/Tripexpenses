<?php
session_start();
include '../../includes/config.php';
include '../../includes/header.php';

// 1️⃣ Redirection si l'utilisateur n'est pas connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit;
}

// 2️⃣ Initialisation
$success = '';
$errors = [];

// Récupération sécurisée des infos de l’utilisateur
$user_id = $_SESSION['user_id'];
$pseudo = $_SESSION['pseudo'];
$mail = $_SESSION['mail'] ?? '';

if (empty($mail)) {
    $errors[] = "Impossible de récupérer votre adresse mail. Veuillez vous reconnecter.";
}


// 3️⃣ Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST" && empty($errors)) {
    $message = trim($_POST['message'] ?? '');
    $type = $_POST['type'] ?? 'autre';

    // 4️⃣ Validation
    if (empty($message)) {
        $errors[] = "Veuillez saisir un message.";
    } elseif (strlen($message) > 1000) {
        $errors[] = "Le message est trop long (max 1000 caractères).";
    }

    $valid_types = ['positif', 'negatif', 'autre'];
    if (!in_array($type, $valid_types)) $type = 'autre';

    // 5️⃣ Insertion sécurisée dans la BDD
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO feedback (user_id, pseudo, mail, message, type) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $pseudo, $mail, $message, $type);

        if ($stmt->execute()) {
            $stmt->close();
            $success = "Merci pour votre retour ! ✅ Vous allez être redirigé vers la page d’infos...";
            $message = ''; // vide le champ
        } else {
            $errors[] = "Erreur lors de l'enregistrement de votre avis : " . htmlspecialchars($stmt->error);
        }
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donnez votre avis - TripExpenses</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="feedback.css">
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

        <div class="feedback-card glass-panel">

            <div class="card-header">
                <a href="javascript:history.back()" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="header-text">
                    <h1><i class="fas fa-comment-dots"></i> Donnez votre avis</h1>
                    <p>Votre retour nous aide à améliorer TripExpenses</p>
                </div>
            </div>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
                <script>
                    setTimeout(function() {
                        window.location.href = '../infos/infos.php'; // Change le lien si tu veux aller ailleurs
                    }, 3000);
                </script>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p><i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="feedback-form">

                <div class="form-section">
                    <label class="section-label">TYPE D'AVIS</label>
                    <div class="type-grid">

                        <label class="type-option">
                            <input type="radio" name="type" value="positif" checked>
                            <div class="option-content">
                                <div class="icon-circle success"><i class="fas fa-smile-beam"></i></div>
                                <span>Avis positif</span>
                            </div>
                        </label>

                        <label class="type-option">
                            <input type="radio" name="type" value="probleme">
                            <div class="option-content">
                                <div class="icon-circle danger"><i class="fas fa-bug"></i></div>
                                <span>Problème</span>
                            </div>
                        </label>

                        <label class="type-option">
                            <input type="radio" name="type" value="suggestion">
                            <div class="option-content">
                                <div class="icon-circle info"><i class="fas fa-lightbulb"></i></div>
                                <span>Suggestion</span>
                            </div>
                        </label>

                    </div>
                </div>

                <div class="form-section">
                    <label class="section-label">VOTRE MESSAGE</label>
                    <div class="textarea-wrapper">
                        <textarea name="message" id="message" maxlength="1000" placeholder="Partagez votre expérience, signalez un problème ou proposez une amélioration..."></textarea>
                        <div class="char-count"><span id="current">0</span> / 1000 caractères</div>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Envoyer mon avis
                </button>

            </form>
        </div>
    </div>

    <script src="../finish_trip/js/finish_trip.js"></script>

    <script>
        const textarea = document.getElementById('message');
        const counter = document.getElementById('current');
        textarea.addEventListener('input', () => {
            counter.textContent = textarea.value.length;
        });

        function updateCharCount() {
            const textarea = document.getElementById('message');
            const charCount = document.getElementById('charCount');
            charCount.textContent = textarea.value.length;
        }
    </script>
</body>

</html>
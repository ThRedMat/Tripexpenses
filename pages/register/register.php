<?php
session_start();

// --- PHPMailer ---
require '../../includes/PHPMailer/src/PHPMailer.php';
require '../../includes/PHPMailer/src/SMTP.php';
require '../../includes/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// --- Connexion BDD et configuration ---
include '../../includes/config.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Nettoyage et validation
    $username = trim($_POST['username']);           // retire les espaces inutiles
    $lastname = trim($_POST['lastname']);
    $pseudo = trim($_POST['pseudo']);
    $mail = trim($_POST['mail']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation des champs
    if (empty($pseudo)) $errors['pseudo'] = "Le pseudo est requis.";
    if (empty($mail) || !filter_var($mail, FILTER_VALIDATE_EMAIL)) $errors['mail'] = "Adresse email invalide.";
    if ($password !== $confirm_password) $errors['confirm_password'] = "Les mots de passe ne correspondent pas.";

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Vérifier doublons (mail ou pseudo déjà pris)
        $stmt = $conn->prepare("SELECT mail, pseudo FROM users WHERE mail = ? OR pseudo = ?");
        $stmt->bind_param("ss", $mail, $pseudo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $existing = $result->fetch_assoc();
            if ($existing['mail'] === $mail)
                $errors['mail'] = "Cette adresse e-mail est déjà utilisée.";
            if ($existing['pseudo'] === $pseudo)
                $errors['pseudo'] = "Ce pseudo est déjà pris.";
        } else {
            // Création de l'utilisateur avec OTP et token sécurisé
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $otp_expires = date('Y-m-d H:i:s', time() + 300); // OTP = 5 min
            $token = bin2hex(random_bytes(32));               // Token de 64 caractères
            $token_expires = date('Y-m-d H:i:s', time() + 1800); // Token = 30 min

            $insert = $conn->prepare("
                INSERT INTO users (username, lastname, pseudo, mail, password, otp_code, otp_expires, confirm_token, confirm_expires)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $insert->bind_param("sssssssss", $username, $lastname, $pseudo, $mail, $hashedPassword, $otp, $otp_expires, $token, $token_expires);

            if ($insert->execute()) {
                // Envoi du mail de confirmation
                $mailSender = new PHPMailer(true);

                try {
                    $confirm_link = "https://tonsite.com/auth/confirm_code.php?token=$token"; // ← adapte ton domaine ici

                    $mailSender->isSMTP();
                    $mailSender->Host = $mail_config['smtp_host'];
                    $mailSender->SMTPAuth = true;
                    $mailSender->Username = $mail_config['smtp_user'];
                    $mailSender->Password = $mail_config['smtp_pass'];
                    $mailSender->SMTPSecure = 'tls';
                    $mailSender->Port = $mail_config['smtp_port'];

                    $mailSender->setFrom($mail_config['smtp_from_email'], $mail_config['smtp_from_name']);
                    $mailSender->addAddress($mail, $username);

                    $mailSender->isHTML(true);
                    $mailSender->Subject = 'Confirmation de votre compte TripExpenses';
                    $mailSender->Body = "
                        <h3>Bonjour $username,</h3>
                        <p>Merci pour votre inscription sur <b>TripExpenses</b> !</p>
                        <p>Voici votre code de confirmation :</p>
                        <h2>$otp</h2>
                        <p>Ce code est valable 5 minutes.</p>
                        <hr>
                        <p>Ou cliquez simplement sur le lien ci-dessous pour confirmer votre e-mail :</p>
                        <a href='$confirm_link'>$confirm_link</a>
                        <p>(Ce lien est valable 30 minutes)</p>
                    ";

                    $mailSender->send();

                    // Rediriger vers la page de saisie du code OTP
                    $_SESSION['pending_email'] = $mail;
                    header("Location: confirm_code.php");
                    exit;
                } catch (Exception $e) {
                    $errors['global'] = "Erreur lors de l'envoi du mail : " . htmlspecialchars($mailSender->ErrorInfo);
                }
            } else {
                $errors['global'] = "Erreur d'enregistrement : " . htmlspecialchars($insert->error);
            }

            $insert->close();
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - TripExpenses</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="register.css">
</head>

<body>

    <div class="container">
        <div class="left-panel">
            <h1>TripExpenses</h1>
            <p>Gérez vos dépenses de voyage en toute simplicité. Partagez les frais avec vos compagnons de route.</p>
            <ul class="features">
                <li>Suivi en temps réel</li>
                <li>Partage équitable</li>
                <li>Rapports détaillés</li>
                <li>Multi-appareils</li>
            </ul>
        </div>

        <div class="right-panel">
            <div class="form-header">
                <h2>Créer un compte</h2>
                <p>Commencez à gérer vos dépenses de voyage</p>
            </div>

            <form action="register.php" method="post">
                <input type="text" name="username_hp" style="display:none !important" tabindex="-1" autocomplete="off">

                <div class="input-row">
                    <div class="input-group">
                        <label for="username">Prénom</label>
                        <input type="text" id="username" name="username" value="<?= htmlspecialchars($username ?? '') ?>" required>
                    </div>
                    <div class="input-group">
                        <label for="lastname">Nom</label>
                        <input type="text" id="lastname" name="lastname" value="<?= htmlspecialchars($lastname ?? '') ?>" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="pseudo">Pseudo</label>
                    <input type="text" id="pseudo" name="pseudo" value="<?= htmlspecialchars($pseudo ?? '') ?>" required>
                    <?php if (!empty($errors['pseudo'])): ?><span class="error-text"><?= $errors['pseudo'] ?></span><?php endif; ?>
                </div>

                <div class="input-group">
                    <label for="mail">Adresse email</label>
                    <input type="email" id="mail" name="mail" value="<?= htmlspecialchars($mail ?? '') ?>" required>
                    <?php if (!empty($errors['mail'])): ?><span class="error-text"><?= $errors['mail'] ?></span><?php endif; ?>
                </div>

                <div class="input-group">
                    <label for="password">Mot de passe</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" required>
                        <span class="password-toggle" onclick="togglePassword('password', 'toggleIcon1')">
                            <i class="fas fa-eye" id="toggleIcon1"></i>
                        </span>
                    </div>
                </div>

                <div class="input-group">
                    <label for="confirm_password">Confirmer</label>
                    <div class="password-wrapper">
                        <input type="password" id="confirm_password" name="confirm_password" required>
                        <span class="password-toggle" onclick="togglePassword('confirm_password', 'toggleIcon2')">
                            <i class="fas fa-eye" id="toggleIcon2"></i>
                        </span>
                    </div>
                    <?php if (!empty($errors['confirm_password'])): ?><span class="error-text"><?= $errors['confirm_password'] ?></span><?php endif; ?>
                </div>

                <button type="submit" class="btn-primary">S'inscrire</button>
                <button type="button" class="btn-secondary" onclick="window.location.href='../../index.html'">Annuler</button>

                <div class="login-link">
                    <p>Déjà un compte ? <a href="../login/login.php">Se connecter</a></p>
                </div>
            </form>
        </div>
    </div>

    <div id="statusPopup" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close-btn" onclick="closePopup()">&times;</span>
            <div class="modal-icon"><?= ($res_status === "success") ? "✅" : "❌" ?></div>
            <h3 class="<?= $res_status ?>"><?= ($res_status === "success") ? "Bienvenue !" : "Attention" ?></h3>
            <p id="modalMessage"><?= $res_message ?? $message ?? '' ?></p>
            <button class="btn-primary" onclick="closePopup()">D'accord</button>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.replace("fa-eye-slash", "fa-eye");
            }
        }

        function closePopup() {
            document.getElementById('statusPopup').style.display = 'none';
        }

        window.onload = function() {
            const msg = document.getElementById('modalMessage').innerText.trim();
            if (msg.length > 0) {
                document.getElementById('statusPopup').style.display = 'flex';
            }
        };
    </script>
</body>

</html>
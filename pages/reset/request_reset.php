<?php
// --- PHPMailer ---
require '../../includes/PHPMailer/src/PHPMailer.php';
require '../../includes/PHPMailer/src/SMTP.php';
require '../../includes/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include '../../includes/config.php'; // ta connexion BDD + config mail si tu l’as mise ici

$res_status = "";
$res_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. PROTECTION HONEYPOT (Anti-robot)
    if (!empty($_POST['username_hp'])) {
        exit("Robot détecté."); 
    }

    // 2. PROTECTION RATE LIMITING (1 demande par minute)
    if (isset($_SESSION['last_reset_request']) && (time() - $_SESSION['last_reset_request'] < 60)) {
        $res_status = "error";
        $res_message = "Veuillez patienter 1 minute avant une nouvelle tentative.";
    } else {
        $email = $_POST['email'];

        // Vérifier si l'email existe
        $stmt = $conn->prepare("SELECT id FROM users WHERE mail = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $token = bin2hex(random_bytes(16));
            $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

            // Sauvegarder le token (On pourrait le hacher ici pour encore plus de sécurité)
            $update = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE mail = ?");
            $update->bind_param("sss", $token, $expires, $email);
            $update->execute();

            $resetLink = "http://localhost/tripexpenses/pages/reset/reset_password.php?token=" . $token;

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = $mail_config['smtp_host'];
                $mail->SMTPAuth   = true;
                $mail->Username   = $mail_config['smtp_user'];
                $mail->Password   = $mail_config['smtp_pass'];
                $mail->SMTPSecure = 'tls';
                $mail->Port       = $mail_config['smtp_port'];
                $mail->CharSet    = 'UTF-8';

                $mail->setFrom($mail_config['smtp_from_email'], $mail_config['smtp_from_name']);
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Réinitialisation de votre mot de passe';
                $mail->Body    = "<p>Cliquez ici pour réinitialiser : <a href='$resetLink'>$resetLink</a></p>";
                $mail->send();
            } catch (Exception $e) {
                // On log l'erreur mais on ne l'affiche pas à l'utilisateur pour rester neutre
                error_log("Erreur PHPMailer : " . $mail->ErrorInfo);
            }
        }

        // 3. MESSAGE NEUTRE (Succès affiché même si l'email n'existe pas)
        $_SESSION['last_reset_request'] = time(); // On lance le chrono du rate limit
        $res_status = "success";
        $res_message = "Si cette adresse est enregistrée, vous recevrez un lien de réinitialisation d'ici quelques instants.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation - TripExpenses</title>
    <link rel="stylesheet" href="css/request_reset.css">
</head>
<body>

    <div class="reset-container">
        <form method="post" class="reset-form">
            <h2>Mot de passe oublié</h2>
            <p>Entrez votre adresse e-mail pour recevoir un lien de réinitialisation.</p>

            <div class="input-group">
                <label for="email">Adresse e-mail</label>
                <input type="email" id="email" name="email" placeholder="exemple@domaine.com" required>
            </div>

            <div class="button-group">
                <a href="../login/login.php" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Envoyer le lien</button>
            </div>
        </form>
    </div>

    <div id="statusPopup" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close-btn" onclick="closePopup()">&times;</span>
            
            <div class="modal-icon">
                <?php echo ($res_status === "success") ? "✅" : "❌"; ?>
            </div>

            <h3 class="<?php echo $res_status; ?>">
                <?php echo ($res_status === "success") ? "Succès !" : "Attention"; ?>
            </h3>

            <p id="modalMessage">
                <?php echo $res_message; ?>
            </p>

            <button class="btn-primary" onclick="closePopup()">D'accord</button>
        </div>
    </div>

    <script>
        function closePopup() {
            document.getElementById('statusPopup').style.display = 'none';
        }

        // Affiche la popup si le PHP a généré un message
        window.onload = function() {
            const msg = document.getElementById('modalMessage').innerText.trim();
            if (msg.length > 0) {
                document.getElementById('statusPopup').style.display = 'flex';
            }
        };

        // Fermer la popup si on clique à l'extérieur
        window.onclick = function(event) {
            const modal = document.getElementById('statusPopup');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

</body>
</html>
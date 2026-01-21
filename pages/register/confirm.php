<?php
session_start();
include '../../includes/config.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $otpInput = $_POST['otp'];

    $stmt = $conn->prepare("SELECT id, otp_code, otp_expires FROM users WHERE mail = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if ($user['otp_code'] === $otpInput) {
            if (strtotime($user['otp_expires']) >= time()) {
                // Valider l’utilisateur
                $update = $conn->prepare("UPDATE users SET confirmed = 1, otp_code = NULL, otp_expires = NULL WHERE id = ?");
                $update->bind_param("i", $user['id']);
                $update->execute();
                $update->close();

                $message = "Adresse e-mail confirmée ✅ Vous allez être redirigé vers la page de login.";
                echo "<script>setTimeout(function(){ window.location.href='../login/login.php'; }, 3000);</script>";
            } else {
                $message = "Code expiré ❌";
            }
        } else {
            $message = "Code incorrect ❌";
        }
    } else {
        $message = "Utilisateur introuvable ❌";
    }
    $stmt->close();
}
$conn->close();
?>
<form action="confirm.php" method="post">
    <input type="email" name="email" placeholder="Votre e-mail" required>
    <input type="text" name="otp" placeholder="Code de confirmation" required>
    <button type="submit">Confirmer mon e-mail</button>
</form>
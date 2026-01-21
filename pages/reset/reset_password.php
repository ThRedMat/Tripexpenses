<?php
include '../../includes/config.php';

$token = $_GET['token'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT mail FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $email = $row['mail'];

        // Mettre à jour le mot de passe
        $update = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE mail = ?");
        $update->bind_param("ss", $newPassword, $email);
        $update->execute();

        $message = "<div class='alert success'>✅ Mot de passe mis à jour avec succès ! <a href='../login/login.php'>Se connecter</a></div>";
    } else {
        $message = "<div class='alert error'>❌ Lien invalide ou expiré.</div>";
    }
}
?>
<?php if (isset($message)) echo $message; ?>

<div class="reset-container">
    <form method="post" class="reset-form">
        <h2>🔒 Réinitialiser votre mot de passe</h2>
        <p>Veuillez entrer un nouveau mot de passe sécurisé.</p>

        <label for="password">Nouveau mot de passe</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>

        <button type="submit">Réinitialiser</button>
    </form>
</div>

<link rel="stylesheet" href="css/reset_password.css">
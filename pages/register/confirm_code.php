<?php
session_start();
include '../../includes/config.php';

$message = '';

$conn->query("UPDATE users SET confirm_token = NULL, confirm_expires = NULL WHERE confirm_expires < NOW()");

if (isset($_GET['token'])) {
    // --- Cas : confirmation par lien ---
    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT id, mail, confirm_expires, confirmed FROM users WHERE confirm_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if ($user['confirmed'] == 1) {
            $message = "Ce compte est déjà confirmé ✅";
        } elseif (strtotime($user['confirm_expires']) < time()) {
            $message = "Le lien de confirmation a expiré ❌";
        } else {
            $update = $conn->prepare("UPDATE users SET confirmed = 1, confirm_token = NULL, confirm_expires = NULL WHERE id = ?");
            $update->bind_param("i", $user['id']);
            $update->execute();
            $update->close();

            $message = "Adresse e-mail confirmée ✅ Vous allez être redirigé vers la page de connexion.";
            echo "<script>setTimeout(function(){ window.location.href='../login/login.php'; }, 3000);</script>";
        }
    } else {
        $message = "Lien de confirmation invalide ❌";
    }

    $stmt->close();
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- Cas : confirmation manuelle via code OTP ---
    $otpInput = $_POST['otp'];

    $stmt = $conn->prepare("SELECT id, otp_code, otp_expires FROM users WHERE otp_code = ?");
    $stmt->bind_param("s", $otpInput);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (strtotime($user['otp_expires']) >= time()) {
            $now = date('Y-m-d H:i:s');
            $update = $conn->prepare("UPDATE users SET confirmed = 1, otp_code = NULL, otp_expires = NULL, confirm_token = NULL, confirm_expires = NULL, confirmed_at = ? WHERE id = ?");
            $update->bind_param("si", $now, $user['id']);
            $update->execute();
            $update->close();

            $message = "Compte confirmé avec succès ✅";
            echo "<script>setTimeout(function(){ window.location.href='../login/login.php'; }, 3000);</script>";
        } else {
            $message = "Code expiré ❌";
        }
    } else {
        $message = "Code incorrect ❌";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Confirmation d'e-mail</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }

        .container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        .btn {
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h3>Confirmez votre e-mail</h3>
        <?php if ($message): ?>
            <p class='text-danger'><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <?php if (!isset($_GET['token'])): ?>
            <form action="confirm_code.php" method="post">
                <input type="text" name="otp" placeholder="Code reçu par mail" class="form-control" required>
                <button type="submit" class="btn btn-primary">Confirmer</button>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>
<?php
session_start();
include '../../includes/config.php';

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $loginInput = $_POST['pseudo']; // pseudo ou mail
    $password = $_POST['password'];

    // Préparer la requête SQL avec confirmation
    $stmt = $conn->prepare("SELECT id, pseudo, mail, confirmed, password FROM users WHERE pseudo = ? OR mail = ?");
    if ($stmt === false) {
        die("Erreur de préparation de la requête : " . $conn->error);
    }

    // Lier les deux paramètres avec la même valeur
    $stmt->bind_param("ss", $loginInput, $loginInput);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        // Corrigé : bind_result dans le bon ordre
        $stmt->bind_result($id, $pseudo, $mail, $confirmed, $hashedPassword);
        $stmt->fetch();

        if ($confirmed == 0) {
            $error_message = "Veuillez confirmer votre e-mail avant de vous connecter.";
        } elseif (!is_null($hashedPassword) && password_verify($password, $hashedPassword)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['pseudo'] = $pseudo;
            $_SESSION['mail'] = $mail;

            // Gérer "Se souvenir de moi"
            if (isset($_POST['remember'])) {
                $token = bin2hex(random_bytes(16));
                $stmt_token = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                $stmt_token->bind_param("si", $token, $id);
                $stmt_token->execute();
                $stmt_token->close();

                // Cookie 30 jours
                setcookie("remember_token", $token, time() + (86400 * 30), "/", "", false, true);
            }

            header("Location: ../home.php");
            exit();
        } else {
            $error_message = "Identifiants incorrects.";
        }
    } else {
        $error_message = "Identifiants incorrects.";
    }

    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - TripExpenses</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="container">
        <div class="header">
            <h1>TripExpenses</h1>
            <p>Bienvenue ! Connectez-vous à votre compte</p>
        </div>

        <div class="form-content">
            <div class="error-message" id="errorMessage" <?php echo !empty($error_message) ? 'style="display: block;"' : ''; ?>>
                <?php echo !empty($error_message) ? htmlspecialchars($error_message) : ''; ?>
            </div>

            <form action="login.php" method="post">
                <div class="input-group">
                    <label for="pseudo">Entrez votre pseudo ou votre adresse mail :</label>
                    <input type="text" id="pseudo" name="pseudo" autocomplete="username" required>
                </div>

                <div class="input-group">
                    <label for="password">Mot de passe</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" autocomplete="current-password" required>
                        <div class="password-toggle" onclick="togglePasswordVisibility()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </div>
                    </div>
                </div>

                <div class="remember-forgot">
                    <label class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <span>Se souvenir de moi</span>
                    </label>
                    <a href="../reset/request_reset.php" class="forgot-link">Mot de passe oublié ?</a>
                </div>

                <button type="submit" class="btn-primary">Se connecter</button>

                <div class="divider">ou</div>

                <div class="register-link">
                    <p>Vous n'avez pas de compte ? <a href="../register/register.php">S'inscrire</a></p>
                </div>
            </form>
        </div>
    </div>

    <script>
        /**
         * Alterne la visibilité du mot de passe
         */
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Contact Us</title>
      <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
      <style>
      body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: url('background.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            font-family: Arial, sans-serif;
      }

      .contact-container {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
      }

      .contact-container h2 {
            margin-bottom: 20px;
            color: #333;
      }

      .form-group label {
            font-weight: bold;
            color: #333;
      }

      .form-control {
            border-radius: 5px;
      }

      .btn-primary {
            background-color: #007BFF;
            border-color: #007BFF;
            border-radius: 5px;
      }

      .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
      }

      .success-message,
      .error-message {
            text-align: center;
            margin-top: 20px;
            font-size: 1.2em;
      }

      .success-message {
            color: green;
      }

      .error-message {
            color: red;
      }
      </style>
</head>

<body>
      <div class="contact-container">
            <h2>Contactez-nous</h2>
            <?php
        session_start(); // Démarre la session PHP

        include '../../includes/config.php'; // Fichier de configuration de la base de données

        // Supposons que le nom de l'utilisateur est stocké dans $_SESSION['username'] lors de l'authentification
        $userName = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : '';

        if (isset($_POST['submit'])) {
            $name = htmlspecialchars($_POST['name']);
            $email = htmlspecialchars($_POST['email']);
            $subject = htmlspecialchars($_POST['subject']);
            $message = htmlspecialchars($_POST['message']);

            // Préparation de la requête SQL
            $stmt = $conn->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $subject, $message);

            // Exécution de la requête et vérification
            if ($stmt->execute()) {
                $_SESSION['message'] = 'Votre message a été envoyé avec succès!';
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = 'Erreur lors de l\'envoi du message. Veuillez réessayer.';
                $_SESSION['message_type'] = 'error';
            }

            // Fermeture de la requête et de la connexion
            $stmt->close();
            $conn->close();

            // Redirection vers la même page pour éviter le renvoi du formulaire
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        }

        // Affichage des messages de session
        if (isset($_SESSION['message'])) {
            $messageType = $_SESSION['message_type'];
            $messageClass = ($messageType === 'success') ? 'success-message' : 'error-message';
            echo '<div class="' . $messageClass . '">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        }
        ?>
            <form action="" method="post">
                  <div class="form-group">
                        <label for="name">Nom</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $userName; ?>"
                              required readonly>
                  </div>
                  <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                  </div>
                  <div class="form-group">
                        <label for="subject">Sujet</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                  </div>
                  <div class="form-group">
                        <label for="message">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                  </div>
                  <button type="submit" name="submit" class="btn btn-primary">Envoyer</button>
            </form>
      </div>
</body>

</html>
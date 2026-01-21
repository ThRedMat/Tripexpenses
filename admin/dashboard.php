<?php
session_start();
require_once 'include/config.php';

// 🔹 Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// 🔹 Paramètres d’inactivité (en secondes)
$inactive_limit = 600; // 10 minutes

// 🔹 Vérifier la dernière activité
if (isset($_SESSION['last_activity'])) {
    $inactive_duration = time() - $_SESSION['last_activity'];

    if ($inactive_duration > $inactive_limit) {
        // Détruire la session après inactivité
        session_unset();
        session_destroy();
        header("Location: login.php?timeout=1");
        exit();
    }
}

// 🔹 Mise à jour du timestamp de dernière activité
$_SESSION['last_activity'] = time();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 40px;
            max-width: 900px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .timeout {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 16px 24px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-10px);
            }

            75% {
                transform: translateX(10px);
            }
        }

        .header {
            margin-bottom: 32px;
        }

        h1 {
            color: #2d3748;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .role {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 32px;
            list-style: none;
        }

        .menu-item {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        }

        .menu-item:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        .menu-item a {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 24px;
            text-decoration: none;
            color: #2d3748;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .menu-item.logout {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .menu-item.logout a {
            color: white;
        }

        .menu-item .icon {
            font-size: 28px;
            min-width: 40px;
            text-align: center;
        }

        .menu-item .arrow {
            margin-left: auto;
            font-size: 20px;
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.3s ease;
        }

        .menu-item:hover .arrow {
            opacity: 1;
            transform: translateX(0);
        }

        .warning-toast {
            position: fixed;
            top: 24px;
            right: 24px;
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
            color: white;
            padding: 20px 24px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            display: none;
            align-items: center;
            gap: 16px;
            max-width: 400px;
            animation: slideInRight 0.4s ease-out;
            z-index: 1000;
        }

        .warning-toast.show {
            display: flex;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .warning-toast .icon {
            font-size: 32px;
        }

        .warning-toast .content {
            flex: 1;
        }

        .warning-toast .title {
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 4px;
        }

        .warning-toast .message {
            font-size: 14px;
            opacity: 0.95;
        }

        .warning-toast .close-btn {
            background: rgba(255, 255, 255, 0.3);
            border: none;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }

        .warning-toast .close-btn:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        @media (max-width: 768px) {
            .container {
                padding: 24px;
            }

            h1 {
                font-size: 24px;
            }

            .menu-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="warning-toast" id="warning-toast">
        <span class="icon">⚠️</span>
        <div class="content">
            <div class="title">Session bientôt expirée</div>
            <div class="message">Votre session expirera dans 2 minutes</div>
        </div>
        <button class="close-btn" onclick="closeWarning()">×</button>
    </div>

    <div class="container">
        <?php if (isset($_GET['timeout'])) : ?>
            <div class="timeout">
                ⏰ Votre session a expiré pour cause d'inactivité
            </div>
        <?php endif; ?>

        <div class="header">
            <h1>
                <span>👋</span>
                Bienvenue, <?= htmlspecialchars($_SESSION['username']); ?>
            </h1>
            <span class="role">
                <?= htmlspecialchars($_SESSION['role']); ?>
            </span>
        </div>



        <ul class="menu-grid">
            <li class="menu-item">
                <a href="manage/manage_users.php">
                    <span class="icon">👥</span>
                    <span>Gérer les utilisateurs</span>
                    <span class="arrow">→</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="manage/manage_admins.php">
                    <span class="icon">🧑‍💼</span>
                    <span>Gérer les administrateurs</span>
                    <span class="arrow">→</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="logs/logs.php">
                    <span class="icon">🧾</span>
                    <span>Voir les logs</span>
                    <span class="arrow">→</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="feedback/feedback_admin.php">
                    <span class="icon">💬</span>
                    <span>Feedback reçus</span>
                    <span class="arrow">→</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="feedback/feedback_archive.php">
                    <span class="icon">📦</span>
                    <span>Feedback traités</span>
                    <span class="arrow">→</span>
                </a>
            </li>
            <li class="menu-item logout">
                <a href="logout.php">
                    <span class="icon">🚪</span>
                    <span>Déconnexion</span>
                    <span class="arrow">→</span>
                </a>
            </li>
        </ul>
    </div>

    <script>
        const inactiveDuration = 600000; // 10 minutes
        const warningTime = 120000; // 2 minutes avant expiration
        let inactivityTimer;
        let warningTimer;

        function showWarning() {
            const toast = document.getElementById('warning-toast');
            toast.classList.add('show');
        }

        function closeWarning() {
            const toast = document.getElementById('warning-toast');
            toast.classList.remove('show');
        }

        function resetInactivityTimer() {
            clearTimeout(inactivityTimer);
            clearTimeout(warningTimer);
            closeWarning();

            // Afficher l'avertissement 2 minutes avant expiration
            warningTimer = setTimeout(showWarning, inactiveDuration - warningTime);

            // Déconnexion après 10 minutes
            inactivityTimer = setTimeout(() => {
                window.location.href = 'logout.php?timeout=1';
            }, inactiveDuration);
        }

        ['mousemove', 'mousedown', 'keypress', 'touchstart', 'scroll'].forEach(evt => {
            document.addEventListener(evt, resetInactivityTimer);
        });

        resetInactivityTimer();
    </script>
</body>

</html>
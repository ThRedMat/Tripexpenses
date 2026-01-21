<?php
require_once '../admin_auth.php';
require_role('super_admin'); // Seuls les super_admins peuvent gérer les admins
require_once '../include/config.php';
require_once '../logs/log_functions.php';

// Créer un nouvel admin
if (isset($_POST['create_admin'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    // Vérifier que les champs ne sont pas vides
    if ($username && $password && $email && $role) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO administrateurs (username, password, email, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $hashedPassword, $email, $role);
        $stmt->execute();
        $stmt->close();

        log_admin_action($conn, $_SESSION['admin_id'], "Créé nouvel admin '$username' avec rôle '$role'");
        $message = ucfirst($role) . " créé avec succès !";
    } else {
        $error = "Tous les champs sont obligatoires.";
    }
}

// Supprimer un admin
if (isset($_POST['delete_admin'])) {
    $admin_id = (int)$_POST['admin_id'];

    // Interdire la suppression de soi-même
    if ($admin_id === $_SESSION['admin_id']) {
        $error = "Vous ne pouvez pas supprimer votre propre compte.";
    } else {
        $stmt = $conn->prepare("DELETE FROM administrateurs WHERE id=?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $stmt->close();

        log_admin_action($conn, $_SESSION['admin_id'], "Supprimé l'admin ID #$admin_id");
        $message = "Administrateur supprimé.";
    }
}

// Modifier le rôle d’un admin
if (isset($_POST['update_role'])) {
    $admin_id = (int)$_POST['admin_id'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE administrateurs SET role=? WHERE id=?");
    $stmt->bind_param("si", $role, $admin_id);
    $stmt->execute();
    $stmt->close();

    log_admin_action($conn, $_SESSION['admin_id'], "Mis à jour le rôle de l'admin ID #$admin_id en '$role'");
    $message = "Rôle mis à jour.";
}

// Récupérer tous les admins
$result = $conn->query("SELECT id, username, email, role FROM administrateurs ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des administrateurs</title>
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
            padding: 40px 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: white;
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        h1 {
            color: #2d3748;
            font-size: 32px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .back-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            animation: slideIn 0.4s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert.success {
            background: linear-gradient(135deg, #81e6d9 0%, #38b2ac 100%);
            color: white;
        }

        .alert.error {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #2d3748;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 24px;
            padding-bottom: 12px;
            border-bottom: 3px solid #e2e8f0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
        }

        .input-group label {
            color: #4a5568;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            color: #2d3748;
            transition: all 0.3s ease;
            background: white;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        button {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button[type="submit"][name="create_admin"] {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
            grid-column: 1 / -1;
        }

        button[type="submit"][name="create_admin"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
        }

        .table-wrapper {
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            color: #2d3748;
            font-weight: 700;
            text-align: left;
            padding: 16px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid #e2e8f0;
            color: #4a5568;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background: rgba(102, 126, 234, 0.03);
        }

        .action-form {
            display: inline-flex;
            gap: 8px;
            align-items: center;
        }

        .inline-select {
            padding: 8px 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            min-width: 150px;
        }

        .inline-select:focus {
            border-color: #667eea;
            outline: none;
        }

        .btn-update {
            background: linear-gradient(135deg, #81e6d9 0%, #38b2ac 100%);
            color: white;
            padding: 8px 16px;
            font-size: 13px;
        }

        .btn-update:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(56, 178, 172, 0.3);
        }

        .btn-delete {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 8px 16px;
            font-size: 13px;
        }

        .btn-delete:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(245, 87, 108, 0.3);
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge.id {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.2) 0%, rgba(118, 75, 162, 0.2) 100%);
            color: #667eea;
        }

        .badge.super_admin {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.2) 0%, rgba(234, 88, 12, 0.2) 100%);
            color: #d97706;
        }

        .badge.moderateur {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2) 0%, rgba(37, 99, 235, 0.2) 100%);
            color: #2563eb;
        }

        .badge.support {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(5, 150, 105, 0.2) 100%);
            color: #059669;
        }

        .disabled-text {
            color: #a0aec0;
            font-style: italic;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            h1 {
                font-size: 24px;
            }

            .card {
                padding: 20px;
            }

            .table-wrapper {
                font-size: 14px;
            }

            th,
            td {
                padding: 12px 8px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>
                <span>🧑‍💼</span>
                Gestion des administrateurs
            </h1>
            <a href="../dashboard.php" class="back-btn">
                <span>⬅</span>
                Retour au dashboard
            </a>
        </div>

        <?php if (isset($message)): ?>
            <div class="alert success">
                <span>✓</span>
                <span><?= htmlspecialchars($message); ?></span>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert error">
                <span>⚠️</span>
                <span><?= htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>Créer un nouvel administrateur</h2>
            <form method="post" class="form-grid">
                <div class="input-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" placeholder="Nom d'utilisateur" required>
                </div>
                <div class="input-group">
                    <label for="email">Adresse email</label>
                    <input type="email" id="email" name="email" placeholder="email@exemple.com" required>
                </div>
                <div class="input-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>
                <div class="input-group">
                    <label for="role">Rôle</label>
                    <select id="role" name="role" required>
                        <option value="super_admin">🔥 Super Admin</option>
                        <option value="moderateur">⚡ Modérateur</option>
                        <option value="support">💬 Support</option>
                    </select>
                </div>
                <button type="submit" name="create_admin">✓ Créer l'administrateur</button>
            </form>
        </div>

        <div class="card">
            <h2>Liste des administrateurs</h2>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom d'utilisateur</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><span class="badge id">#<?= htmlspecialchars($row['id']); ?></span></td>
                                <td><?= htmlspecialchars($row['username']); ?></td>
                                <td><?= htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <form method="post" class="action-form">
                                        <input type="hidden" name="admin_id" value="<?= htmlspecialchars($row['id']); ?>">
                                        <select name="role" class="inline-select">
                                            <option value="super_admin" <?= $row['role'] == 'super_admin' ? 'selected' : ''; ?>>🔥 Super Admin</option>
                                            <option value="moderateur" <?= $row['role'] == 'moderateur' ? 'selected' : ''; ?>>⚡ Modérateur</option>
                                            <option value="support" <?= $row['role'] == 'support' ? 'selected' : ''; ?>>💬 Support</option>
                                        </select>
                                        <button type="submit" name="update_role" class="btn-update">Modifier</button>
                                    </form>
                                </td>
                                <td>
                                    <?php if ($row['id'] !== $_SESSION['admin_id']): ?>
                                        <form method="post" class="action-form">
                                            <input type="hidden" name="admin_id" value="<?= htmlspecialchars($row['id']); ?>">
                                            <button type="submit" name="delete_admin" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet administrateur ?');">Supprimer</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="disabled-text">Vous-même</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
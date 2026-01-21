<?php
require_once '../admin_auth.php';
require_role(['super_admin', 'moderateur', 'support']);
require_once '../include/config.php';
require_once '../logs/log_functions.php';

// Vérification et mise à jour du feedback
if (isset($_POST['update_status'])) {
    $feedback_id = (int)$_POST['feedback_id'];
    $status = $_POST['status'];
    $admin_id = $_SESSION['admin_id'];

    // Démarrer une transaction
    $conn->begin_transaction();
    try {
        // 1️⃣ Mettre à jour le feedback
        $stmt = $conn->prepare("UPDATE feedback SET status=?, admin_id=? WHERE id=?");
        $stmt->bind_param("sii", $status, $admin_id, $feedback_id);
        $stmt->execute();
        $stmt->close();

        // 2️⃣ Si traité, copier dans feedback_archive et supprimer de feedback
        if ($status === 'traité') {
            $conn->query("
                INSERT INTO feedback_archive (id, user_id, message, status, admin_id, created_at, updated_at)
                SELECT id, user_id, message, status, admin_id, created_at, updated_at
                FROM feedback
                WHERE id = $feedback_id
            ");
            $conn->query("DELETE FROM feedback WHERE id = $feedback_id");
        }

        // 3️⃣ Log de l'action
        log_admin_action($conn, $admin_id, "Feedback #$feedback_id mis à jour en '$status'");

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "Erreur lors de la mise à jour : " . $e->getMessage();
    }
}

// Récupération des feedbacks
$result = $conn->query("
    SELECT f.id, f.message, f.status, f.created_at, u.username AS user_name, a.username AS admin_name
    FROM feedback f
    LEFT JOIN users u ON f.user_id = u.id
    LEFT JOIN administrateurs a ON f.admin_id = a.id
    ORDER BY f.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback utilisateurs</title>
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

        .card {
            background: white;
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #667eea;
        }

        .stat-card.nouveau {
            border-left-color: #f59e0b;
        }

        .stat-card.en_cours {
            border-left-color: #3b82f6;
        }

        .stat-card.traite {
            border-left-color: #10b981;
        }

        .stat-label {
            color: #718096;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .stat-value {
            color: #2d3748;
            font-size: 28px;
            font-weight: 700;
        }

        .filters {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 12px 16px 12px 44px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            color: #2d3748;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: #a0aec0;
        }

        .filter-select {
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            color: #2d3748;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-select:focus {
            outline: none;
            border-color: #667eea;
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
            position: sticky;
            top: 0;
            z-index: 10;
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

        .badge.nouveau {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.2) 0%, rgba(234, 88, 12, 0.2) 100%);
            color: #d97706;
        }

        .badge.en_cours {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2) 0%, rgba(37, 99, 235, 0.2) 100%);
            color: #2563eb;
        }

        .badge.traite {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(5, 150, 105, 0.2) 100%);
            color: #059669;
        }

        .message-cell {
            max-width: 400px;
            line-height: 1.5;
        }

        .message-preview {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .admin-badge {
            background: #f7fafc;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 13px;
            color: #4a5568;
            font-weight: 600;
        }

        .time-badge {
            color: #718096;
            font-size: 14px;
        }

        .action-form {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .inline-select {
            padding: 8px 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            min-width: 130px;
            background: white;
        }

        .inline-select:focus {
            border-color: #667eea;
            outline: none;
        }

        .btn-update {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 16px;
            font-size: 13px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-update:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
        }

        .no-feedback {
            text-align: center;
            padding: 60px 20px;
            color: #718096;
        }

        .no-feedback-icon {
            font-size: 64px;
            margin-bottom: 16px;
            opacity: 0.5;
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

            .message-cell {
                max-width: 200px;
            }

            .action-form {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>
                <span>💬</span>
                Feedback utilisateurs
            </h1>
            <a href="../dashboard.php" class="back-btn">
                <span>⬅</span>
                Retour au dashboard
            </a>
        </div>

        <div class="card">
            <div class="stats-grid">
                <div class="stat-card nouveau">
                    <div class="stat-label">Nouveaux</div>
                    <div class="stat-value" id="nouveau-count">0</div>
                </div>
                <div class="stat-card en_cours">
                    <div class="stat-label">En cours</div>
                    <div class="stat-value" id="en-cours-count">0</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total actifs</div>
                    <div class="stat-value" id="total-feedback">0</div>
                </div>
            </div>

            <div class="filters">
                <div class="search-box">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="search-input" placeholder="Rechercher par utilisateur ou message...">
                </div>
                <select class="filter-select" id="status-filter">
                    <option value="">Tous les statuts</option>
                    <option value="nouveau">🔔 Nouveaux</option>
                    <option value="en_cours">⚡ En cours</option>
                </select>
            </div>

            <div class="table-wrapper">
                <table id="feedback-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Utilisateur</th>
                            <th>Message</th>
                            <th>Statut</th>
                            <th>Pris en charge par</th>
                            <th>Date de création</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        $nouveau = 0;
                        $en_cours = 0;

                        while ($row = $result->fetch_assoc()):
                            $total++;
                            if ($row['status'] === 'nouveau') $nouveau++;
                            if ($row['status'] === 'en_cours') $en_cours++;

                            $status_class = strtolower(str_replace('é', 'e', $row['status']));
                        ?>
                            <tr class="feedback-row" data-status="<?= htmlspecialchars($row['status']); ?>">
                                <td><span class="badge id">#<?= htmlspecialchars($row['id']); ?></span></td>
                                <td><strong><?= htmlspecialchars($row['user_name']); ?></strong></td>
                                <td class="message-cell">
                                    <div class="message-preview"><?= htmlspecialchars($row['message']); ?></div>
                                </td>
                                <td><span class="badge <?= $status_class; ?>"><?= htmlspecialchars($row['status']); ?></span></td>
                                <td>
                                    <?php if ($row['admin_name']): ?>
                                        <span class="admin-badge"><?= htmlspecialchars($row['admin_name']); ?></span>
                                    <?php else: ?>
                                        <span style="color: #a0aec0;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="time-badge"><?= htmlspecialchars($row['created_at']); ?></span></td>
                                <td>
                                    <form method="post" class="action-form">
                                        <input type="hidden" name="feedback_id" value="<?= htmlspecialchars($row['id']); ?>">
                                        <select name="status" class="inline-select">
                                            <option value="nouveau" <?= $row['status'] == 'nouveau' ? 'selected' : ''; ?>>🔔 Nouveau</option>
                                            <option value="en_cours" <?= $row['status'] == 'en_cours' ? 'selected' : ''; ?>>⚡ En cours</option>
                                            <option value="traité" <?= $row['status'] == 'traité' ? 'selected' : ''; ?>>✓ Archiver</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn-update">Mettre à jour</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                        <?php if ($total === 0): ?>
                            <tr>
                                <td colspan="7">
                                    <div class="no-feedback">
                                        <div class="no-feedback-icon">📭</div>
                                        <div>Aucun feedback disponible</div>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Mise à jour des statistiques
        document.getElementById('total-feedback').textContent = <?= $total; ?>;
        document.getElementById('nouveau-count').textContent = <?= $nouveau; ?>;
        document.getElementById('en-cours-count').textContent = <?= $en_cours; ?>;

        // Fonction de recherche et filtrage
        const searchInput = document.getElementById('search-input');
        const statusFilter = document.getElementById('status-filter');
        const feedbackRows = document.querySelectorAll('.feedback-row');

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const statusValue = statusFilter.value;

            feedbackRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const status = row.dataset.status;

                const matchesSearch = text.includes(searchTerm);
                const matchesStatus = !statusValue || status === statusValue;

                if (matchesSearch && matchesStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('input', filterTable);
        statusFilter.addEventListener('change', filterTable);
    </script>
</body>

</html>
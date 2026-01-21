<?php
require_once '../admin_auth.php';
require_role(['super_admin', 'moderateur', 'support']);
require_once '../include/config.php';

// Récupération des feedbacks archivés
$result = $conn->query("
    SELECT f.id, f.message, f.status, f.created_at, f.updated_at, u.username AS user_name, a.username AS admin_name
    FROM feedback_archive f
    LEFT JOIN users u ON f.user_id = u.id
    LEFT JOIN administrateurs a ON f.admin_id = a.id
    ORDER BY f.updated_at DESC
");
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback archivés</title>
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
            text-align: center;
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
            text-align: center;
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

        .badge.resolved {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(5, 150, 105, 0.2) 100%);
            color: #059669;
        }

        .badge.archived {
            background: linear-gradient(135deg, rgba(107, 114, 128, 0.2) 0%, rgba(75, 85, 99, 0.2) 100%);
            color: #4b5563;
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
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>
                <span>📦</span>
                Feedback archivés
            </h1>
            <a href="../dashboard.php" class="back-btn">
                <span>⬅</span>
                Retour au dashboard
            </a>
        </div>

        <div class="card">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total archivés</div>
                    <div class="stat-value" id="total-feedback">0</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Ce mois</div>
                    <div class="stat-value" id="month-count">0</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Cette semaine</div>
                    <div class="stat-value" id="week-count">0</div>
                </div>
            </div>

            <div class="filters">
                <div class="search-box">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="search-input" placeholder="Rechercher par utilisateur, message ou admin...">
                </div>
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
                            <th>Date de traitement</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        $month = 0;
                        $week = 0;
                        $now = time();
                        $month_start = strtotime('first day of this month');
                        $week_start = strtotime('monday this week');

                        while ($row = $result->fetch_assoc()):
                            $total++;

                            $update_time = strtotime($row['updated_at']);
                            if ($update_time >= $month_start) $month++;
                            if ($update_time >= $week_start) $week++;

                            $status_class = strtolower($row['status']);
                        ?>
                            <tr class="feedback-row" data-status="<?= htmlspecialchars($status_class); ?>">
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
                                <td><span class="time-badge"><?= date_format(date_create($row['updated_at']), 'd/m/Y H:i:s'); ?></span></td>
                            </tr>
                        <?php endwhile; ?>

                        <?php if ($total === 0): ?>
                            <tr>
                                <td colspan="6">
                                    <div class="no-feedback">
                                        <div class="no-feedback-icon">📭</div>
                                        <div>Aucun feedback archivé</div>
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
        document.getElementById('month-count').textContent = <?= $month; ?>;
        document.getElementById('week-count').textContent = <?= $week; ?>;

        // Fonction de recherche
        const searchInput = document.getElementById('search-input');
        const feedbackRows = document.querySelectorAll('.feedback-row');

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();

            feedbackRows.forEach(row => {
                const text = row.textContent.toLowerCase();

                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('input', filterTable);
    </script>
</body>

</html>
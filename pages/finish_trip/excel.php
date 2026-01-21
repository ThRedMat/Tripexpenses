
<?php
include '../../includes/config.php';

if (isset($_POST['trip_id'])) {
    $trip_id = $_POST['trip_id'];

    $sql = "SELECT e.id, e.amount, e.expense_date, e.description, e.lieu, c.name as category 
            FROM expenses e 
            JOIN categories c ON e.category_id = c.id 
            WHERE e.trip_id = ? 
            ORDER BY e.expense_date";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $trip_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"voyage_$trip_id.xls\"");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo "\xEF\xBB\xBF"; // BOM UTF-8

        echo "<table border='1' cellspacing='0' cellpadding='5' style='border-collapse:collapse; width:100%; font-family:Arial, sans-serif;'>";

        // En-têtes stylés
        echo "<tr style='background-color:#f2f2f2; font-weight:bold; text-align:center;'>";
        echo "<th>Numéro de l'achat</th><th>Date de la Dépense</th><th>Catégorie</th><th>Description</th><th>Montant</th><th>Lieu de l'achat</th>";
        echo "</tr>";

        // Contenu avec alternance des couleurs
        $i = 0;
        while ($row = $result->fetch_assoc()) {
            $bgColor = $i % 2 == 0 ? '#ffffff' : '#e6f7ff';
            echo "<tr style='background-color:$bgColor;'>";
            echo "<td style='text-align:center;'>" . $row['id'] . "</td>";
            echo "<td style='text-align:center;'>" . $row['expense_date'] . "</td>";
            echo "<td style='text-align:center;'>" . htmlspecialchars($row['category']) . "</td>";
            echo "<td style='text-align:center;'>" . htmlspecialchars($row['description']) . "</td>";
            echo "<td style='text-align:center;'>" . number_format($row['amount'], 2, ',', ' ') . "</td>";
            echo "<td style='text-align:center;'>" . htmlspecialchars($row['lieu']) . "</td>";
            echo "</tr>";
            $i++;
        }

        echo "</table>";
    } else {
        echo "Aucune donnée trouvée.";
    }

    $stmt->close();
} else {
    echo "ID du voyage non spécifié.";
}

$conn->close();

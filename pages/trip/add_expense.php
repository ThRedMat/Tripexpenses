<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';
//include '../includes/header.php';

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['pseudo'])) {
      header("Location: login.php");
      exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Récupère les informations du formulaire
      $trip_id = $_POST['trip_id'];
      $category_id = $_POST['category_id'];
      $amount = $_POST['amount'];
      $expense_date = $_POST['expense_date'];
      $description = $_POST['description'];
      $lieu = $_POST['lieu'];


      // Insère la dépense dans la base de données
      $sql = "INSERT INTO expenses (trip_id, category_id, amount, expense_date, description, lieu) VALUES (?, ?, ?, ?, ?, ?)";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("iissss", $trip_id, $category_id, $amount, $expense_date, $description, $lieu);

      if ($stmt->execute()) {
            echo "Dépense ajoutée avec succès!";
            // Rediriger vers la page de détails du voyage ou une autre page appropriée
            header("Location: trip_details.php?id=" . $trip_id . "&msg=added");
            exit();
      } else {
            echo "Erreur: " . $stmt->error;
      }

      $stmt->close();
      $conn->close();
} else {
      // Afficher le formulaire pour ajouter des dépenses
      if (isset($_GET['trip_id'])) {
            $trip_id = $_GET['trip_id'];

            // Récupérer les dates de début et de fin du voyage depuis la base de données
            $sql_trip = "SELECT start_date, end_date FROM trip WHERE id = ?";
            $stmt_trip = $conn->prepare($sql_trip);
            $stmt_trip->bind_param("i", $trip_id);
            $stmt_trip->execute();
            $trip_result = $stmt_trip->get_result();
            $trip = $trip_result->fetch_assoc();

            if ($trip) {
                  $start_date = $trip['start_date'];
                  $end_date = $trip['end_date'];
            } else {
                  echo "Voyage non trouvé.";
                  exit();
            }
            $stmt_trip->close();

            // Récupérer les catégories depuis la base de données
            $sql_categories = "SELECT id, name FROM categories order by name ASC";
            $result = $conn->query($sql_categories);
            $categories = [];
            while ($row = $result->fetch_assoc()) {
                  $categories[] = $row;
            }
?>
            <!DOCTYPE html>
            <html>

            <head>
                  <title>Ajouter des Dépenses</title>
                  <link rel="stylesheet" type="text/css" href="css/add_expense.css">
            </head>

            <body>
                  <div class="container">
                        <h1>Ajouter des Dépenses pour le Voyage</h1>
                        <form action="add_expense.php" method="post">
                              <input type="hidden" name="trip_id" value="<?php echo htmlspecialchars($trip_id); ?>"
                                    class="hidden-input">
                              <h4>Catégorie:</h4>
                              <select name="category_id" required class="select-category">
                                    <?php
                                    foreach ($categories as $category) {
                                          echo '<option value="' . htmlspecialchars($category['id']) . '">' . htmlspecialchars($category['name']) . '</option>';
                                    }
                                    ?>
                              </select>
                              <h4>Montant:</h4>
                              <input type="number" step="0.01" name="amount" required pattern="\d+(\.\d{1,2})?">
                              <h4>Date de la Dépense:</h4>
                              <input type="date" name="expense_date" required min="<?php echo htmlspecialchars($start_date); ?>"
                                    max="<?php echo htmlspecialchars($end_date); ?>" value="<?php echo date('Y-m-d'); ?>">
                              <h4>Description:</h4>
                              <textarea name="description" rows="5" cols="40" class="description"></textarea>
                              <h4>Lieu:</h4>
                              <input type="text" name="lieu" required>
                              <input type="submit" value="Ajouter la Dépense">
                        </form>
                        <a href="trip_details.php?id=<?php echo htmlspecialchars($trip_id); ?>" class="btn-retour">Annuler</a>
                  </div>
            </body>

            </html>


<?php
      } else {
            echo "Aucun voyage spécifié.";
      }
}
?>
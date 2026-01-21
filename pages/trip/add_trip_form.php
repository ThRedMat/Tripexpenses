<!DOCTYPE html>
<html lang="en">

<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Ajouter un Voyage</title>
      <link rel="stylesheet" href="css/add_trip_form.css">
</head>

<body>
      <div class="container">
            <h1>Ajouter un Nouveau Voyage</h1>
            <form action="add_trip.php" method="post">
                  <label for="destination">Destination:</label>
                  <input type="text" id="destination" name="destination" required><br>
                  <label for="start_date">Date de Départ:</label>
                  <input type="date" id="start_date" name="start_date" required min="<?php echo date('Y-m-d'); ?>"
                        value="<?php echo date('Y-m-d'); ?>"><br>
                  <label for="end_date">Date de Retour: </label>
                  <input type="date" id="end_date" name="end_date" min="<?php echo date('Y-m-d'); ?>" required><br>
                  <label for="budget">Budget Total: </label>
                  <input type="number" id="budget" step="0.01" name="budget" required><br>
                  <label for="devise">Choisissez la devise:</label>
                  <div class="currency-container">
                        <select id="devise" name="devise" required>
                              <?php foreach ($currencies as $c): ?>
                                    <option value="<?php echo htmlspecialchars($c['code']); ?>"
                                          <?php echo ($c['code'] === $userPreferredCurrency ? 'selected' : ''); ?>>
                                          <?php echo htmlspecialchars($c['name']) . ' (' . htmlspecialchars($c['code']) . ')'; ?>
                                    </option>
                              <?php endforeach; ?>
                        </select>
                  </div>
                  <input type="submit" value="Ajouter le Voyage">
            </form>
            <a href="trip.php" class="btn-retour">Retour</a>
      </div>
</body>

</html>
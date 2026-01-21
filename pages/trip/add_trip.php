<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['pseudo'])) {
    header("Location: " . BASE_URL . "pages/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Récupération des données du formulaire
        $destination = trim($_POST['destination']);
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $budget_total = !empty($_POST['budget']) ? floatval($_POST['budget']) : 0;
        $devise = $_POST['devise'];

        // Transport
        $transport_type = $_POST['transport'];
        $transport_reserved = isset($_POST['has_transport_cost']) ? 1 : 0;
        $transport_cost = $transport_reserved && !empty($_POST['transport_price'])
            ? floatval($_POST['transport_price'])
            : null;

        // Hébergement
        $accommodation_type = $_POST['accommodation'];
        $accommodation_reserved = isset($_POST['has_accommodation_cost']) ? 1 : 0;
        $accommodation_cost = $accommodation_reserved && !empty($_POST['accommodation_price'])
            ? floatval($_POST['accommodation_price'])
            : null;

        // Validation des dates
        if (strtotime($end_date) < strtotime($start_date)) {
            throw new Exception("La date de fin ne peut pas être antérieure à la date de début");
        }

        // Récupération de l'ID utilisateur
        $user_sql = "SELECT id FROM users WHERE pseudo = ?";
        $user_stmt = $conn->prepare($user_sql);
        $user_stmt->bind_param("s", $_SESSION['pseudo']);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        $user = $user_result->fetch_assoc();
        $user_stmt->close();

        if (!$user) {
            throw new Exception("Utilisateur non trouvé");
        }

        // Détermination du statut initial
        $current_date = date('Y-m-d');
        if ($current_date >= $start_date && $current_date <= $end_date) {
            $status = 'En cours';
        } elseif ($current_date > $end_date) {
            $status = 'Terminé';
        } else {
            $status = 'À venir';
        }

        $insert_sql = "INSERT INTO trip 
                (user_id, destination, start_date, end_date, budget_total, devise, 
                 transport_type, transport_reserved, transport_cost, 
                 accommodation_type, accommodation_reserved, accommodation_cost, 
                 status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param(
            "isssdssidsids",
            $user['id'],
            $destination,
            $start_date,
            $end_date,
            $budget_total,
            $devise,
            $transport_type,
            $transport_reserved,
            $transport_cost,
            $accommodation_type,
            $accommodation_reserved,
            $accommodation_cost,
            $status
        );

        if ($stmt->execute()) {
            $trip_id = $conn->insert_id;
            $_SESSION['success_message'] = "Voyage créé avec succès !";

            // Redirection vers la page de détails du voyage
            header("Location: " . BASE_URL . "pages/trip/trip_details.php?id=" . $trip_id);
            exit();
        } else {
            throw new Exception("Erreur lors de la création du voyage");
        }

        $stmt->close();
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        //header("Location: " . BASE_URL . "pages/trip/index.php");
        echo $e->getMessage();
        exit();
    }
} else {
    //header("Location: " . BASE_URL . "pages/trip/index.php");
    echo $e->getMessage();
    exit();
}

// Récupération et nettoyage
$destination = trim($_POST['destination']);

// Validation du format
if (!preg_match('/^[^,]+,\s*.+$/', $destination)) {
    $_SESSION['error_message'] = "Format de destination invalide. Utilisez: Ville, Pays";
    header("Location: " . BASE_URL . "pages/trip/trip.php");
    exit();
}

// Séparation et validation
$parts = explode(',', $destination, 2);
$ville = trim($parts[0]);
$pays = trim($parts[1]);

if (empty($ville) || empty($pays)) {
    $_SESSION['error_message'] = "La ville et le pays sont obligatoires";
    header("Location: " . BASE_URL . "pages/trip/trip.php");
    exit();
}

if (strlen($ville) < 2 || strlen($pays) < 2) {
    $_SESSION['error_message'] = "La ville et le pays doivent contenir au moins 2 caractères";
    header("Location: " . BASE_URL . "pages/trip/trip.php");
    exit();
}

// Reformatage propre
$destination = ucfirst($ville) . ', ' . ucfirst($pays);

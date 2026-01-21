<!DOCTYPE html>
<html lang="fr">

<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>TripExpenses</title>
      
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
      
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

      <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/navbar.css">
</head>

<body>

      <nav class="navbar">
            <div class="nav-container">
                  
                  <a href="<?php echo BASE_URL; ?>pages/home.php" class="logo">
                        <img src="<?php echo IMAGES_URL; ?>logo-tripexpenses.jfif" alt="Logo">
                        TripExpenses
                  </a>

                  <div class="nav-center">
                        <ul class="nav-links">
                              <li>
                                    <a href="<?php echo BASE_URL; ?>pages/home.php" class="active">
                                          <i class="fas fa-home"></i> Accueil
                                    </a>
                              </li>
                              <li>
                                    <a href="<?php echo BASE_URL; ?>pages/trip/trip.php">
                                          <i class="fas fa-plane"></i> En cours
                                    </a>
                              </li>
                              <li>
                                    <a href="<?php echo BASE_URL; ?>pages/finish_trip/finish_trip.php">
                                          <i class="fas fa-history"></i> Historique
                                    </a>
                              </li>
                              <li>
                                    <a href="<?php echo BASE_URL; ?>pages/devises_app/index.php">
                                          <i class="fas fa-exchange-alt"></i> Devises
                                    </a>
                              </li>
                        </ul>
                  </div>

                  <div class="nav-right">
                        <a href="#" class="profile-btn">
                              <i class="fas fa-user-circle"></i> Mon Compte <i class="fas fa-chevron-down" style="font-size: 0.8em; margin-left: 5px;"></i>
                        </a>
                        <ul class="submenu">
                              <li>
                                    <a href="<?php echo BASE_URL; ?>pages/infos/infos.php">
                                          <i class="fas fa-id-card"></i> Mes informations
                                    </a>
                              </li>
                              <li>
                                    <a href="<?php echo BASE_URL; ?>pages/feedback/feedback.php">
                                          <i class="fas fa-comment-dots"></i> Donner mon avis
                                    </a>
                              </li>
                              <li>
                                    <a href="<?php echo BASE_URL; ?>/pages/logout/logout.php" class="logout">
                                          <i class="fas fa-sign-out-alt"></i> Se déconnecter
                                    </a>
                              </li>
                        </ul>
                  </div>

                  <button class="mobile-toggle">
                        <i class="fas fa-bars"></i>
                  </button>
            </div>
      </nav>

      <div class="mobile-menu">
            <button class="close-menu">&times;</button>
            
            <a href="<?php echo BASE_URL; ?>pages/home.php" class="active"><i class="fas fa-home"></i> Accueil</a>
            <a href="<?php echo BASE_URL; ?>pages/trip/trip.php"><i class="fas fa-plane"></i> En cours</a>
            <a href="<?php echo BASE_URL; ?>pages/finish_trip/finish_trip.php"><i class="fas fa-history"></i> Historique</a>
            <a href="<?php echo BASE_URL; ?>pages/devises_app/index.php"><i class="fas fa-exchange-alt"></i> Devises</a>
            
            <hr style="width: 50%; border: 1px solid rgba(255,255,255,0.1); margin: 10px 0;">
            
            <a href="<?php echo BASE_URL; ?>pages/infos/infos.php"><i class="fas fa-id-card"></i> Mon Profil</a>
            <a href="<?php echo BASE_URL; ?>pages/feedback/feedback.php"><i class="fas fa-comment-dots"></i> Avis</a>
            <a href="<?php echo BASE_URL; ?>/pages/logout/logout.php" style="color: #ff8080;"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
      </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- 1. CONFIGURATION ---
        const mobileToggle = document.querySelector('.mobile-toggle');
        const mobileMenu = document.querySelector('.mobile-menu');
        const closeMenu = document.querySelector('.close-menu');
        const navbar = document.querySelector('.navbar');

        // --- 2. GESTION DU SCROLL NAVBAR ---
        if (navbar) {
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
        }

        // --- 3. OUVERTURE DU MENU ---
        if (mobileToggle && mobileMenu) {
            mobileToggle.addEventListener('click', function(e) {
                e.preventDefault(); // Empêche le comportement par défaut
                mobileMenu.classList.add('active'); // Ajoute la classe qui rend visible
                document.body.style.overflow = 'hidden'; // Bloque le scroll
            });
        }

        // --- 4. FERMETURE DU MENU ---
        // Fonction pour fermer
        function closeMobileMenu() {
            if (mobileMenu) {
                mobileMenu.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        }

        // Clic sur la croix
        if (closeMenu) {
            closeMenu.addEventListener('click', closeMobileMenu);
        }

        // Clic sur un lien du menu (pour fermer automatiquement après choix)
        const mobileLinks = document.querySelectorAll('.mobile-menu a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', closeMobileMenu);
        });
    });
</script>

</body>
</html>
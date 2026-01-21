document.addEventListener("DOMContentLoaded", () => {
  /* =========================================
     1. GESTION DU DIAPORAMA DE FOND (SLIDESHOW)
     ========================================= */
  const slides = document.querySelectorAll(".slide");
  let currentSlide = 0;
  const totalSlides = slides.length;

  // On vérifie qu'il y a bien des slides avant de lancer l'animation
  if (totalSlides > 0) {
    
    function showSlide(n) {
      // Enlève la classe active de la slide actuelle
      slides[currentSlide].classList.remove("active");
      
      // Calcule l'index de la prochaine slide (boucle infinie)
      currentSlide = (n + totalSlides) % totalSlides;
      
      // Ajoute la classe active à la nouvelle slide
      slides[currentSlide].classList.add("active");
    }

    function nextSlide() {
      showSlide(currentSlide + 1);
    }

    // Change d'image toutes les 5 secondes
    setInterval(nextSlide, 5000);
  }
});
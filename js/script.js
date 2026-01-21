 // Navbar scroll effect et gestion de la flèche
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            const scrollIndicator = document.getElementById('scrollIndicator');
            const scrollIcon = document.getElementById('scrollIcon');
            
            if (window.scrollY > 100) {
                navbar.classList.add('scrolled');
                // Changer la flèche pour remonter
                scrollIcon.className = 'fas fa-chevron-up';
                scrollIndicator.classList.remove('show-down');
                scrollIndicator.classList.add('show-up');
            } else {
                navbar.classList.remove('scrolled');
                // Changer la flèche pour descendre
                scrollIcon.className = 'fas fa-chevron-down';
                scrollIndicator.classList.remove('show-up');
                scrollIndicator.classList.add('show-down');
            }
        });

// Carousel functionality
const images = document.querySelectorAll('.image');
const dots = document.querySelectorAll('.carousel-dot');
const backBtn = document.getElementById('back');
const nextBtn = document.getElementById('next');
const heroSection = document.querySelector('.hero-section');
let currentSlide = 0;

// Array of background images that correspond to carousel images
const backgroundImages = [
      '../images/lac.avif', // Lac
      '../images/nature.avif', // Nature
      '../images/foret.avif', // Forêt
      '../images/montagne.avif'  // Route/voyage
];

function updateBackgroundImage(index) {
      const newBgImage = backgroundImages[index];
      heroSection.style.backgroundImage = `
    linear-gradient(135deg, rgba(102, 126, 234, 0.7) 0%, rgba(118, 75, 162, 0.7) 100%), 
    url('${newBgImage}')
`;
}

function showSlide(index) {
      images.forEach((img, i) => {
            img.classList.remove('active', 'prev');
            if (i === index) {
                  img.classList.add('active');
            } else if (i < index) {
                  img.classList.add('prev');
            }
      });

      dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
      });

      // Update background image to match carousel
      updateBackgroundImage(index);
      currentSlide = index;
}

function nextSlide() {
      const next = (currentSlide + 1) % images.length;
      showSlide(next);
}

function prevSlide() {
      const prev = (currentSlide - 1 + images.length) % images.length;
      showSlide(prev);
}

// Event listeners
nextBtn.addEventListener('click', nextSlide);
backBtn.addEventListener('click', prevSlide);

dots.forEach((dot, index) => {
      dot.addEventListener('click', () => showSlide(index));
});

// Auto-advance carousel
setInterval(nextSlide, 6000);

        // Gestion du clic sur la flèche de scroll
        document.getElementById('scrollIndicator').addEventListener('click', () => {
            const scrollIcon = document.getElementById('scrollIcon');
            
            if (scrollIcon.classList.contains('fa-chevron-down')) {
                // Si flèche vers le bas, descendre vers la section conseils
                document.querySelector('.content-section').scrollIntoView({
                    behavior: 'smooth'
                });
            } else {
                // Si flèche vers le haut, remonter en haut de la page
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        });




// Enhanced interactive elements
document.addEventListener('mousemove', (e) => {
      const shapes = document.querySelectorAll('.shape');
      shapes.forEach((shape, index) => {
            const x = e.clientX / window.innerWidth;
            const y = e.clientY / window.innerHeight;
            const moveX = (x - 0.5) * (index + 1) * 25;
            const moveY = (y - 0.5) * (index + 1) * 25;
            shape.style.transform = `translate(${moveX}px, ${moveY}px)`;
      });
});

// Animate stats on scroll
const observerOptions = {
      threshold: 0.5,
      rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
            if (entry.isIntersecting) {
                  const statNumbers = entry.target.querySelectorAll('.stat-number');
                  statNumbers.forEach(stat => {
                        animateNumber(stat);
                  });
                  observer.unobserve(entry.target);
            }
      });
}, observerOptions);

const statsSection = document.querySelector('.stats-section');
if (statsSection) {
      observer.observe(statsSection);
}

function animateNumber(element) {
      const target = element.textContent;
      const isEuro = target.includes('€');
      const isK = target.includes('K');
      const isM = target.includes('M');

      let targetNum = parseFloat(target.replace(/[€KM+]/g, ''));
      if (isK) targetNum *= 1000;
      if (isM) targetNum *= 1000000;

      let current = 0;
      const increment = targetNum / 100;
      const timer = setInterval(() => {
            current += increment;
            if (current >= targetNum) {
                  current = targetNum;
                  clearInterval(timer);
            }

            let displayValue = Math.floor(current);
            if (isK && displayValue >= 1000) {
                  displayValue = Math.floor(displayValue / 1000) + 'K';
            } else if (isM && displayValue >= 1000000) {
                  displayValue = (displayValue / 1000000).toFixed(1) + 'M';
            }

            element.textContent = (isEuro ? '€' : '') + displayValue + (target.includes('+') ? '+' : '');
      }, 20);
}
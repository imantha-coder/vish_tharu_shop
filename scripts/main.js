// Mobile Navigation
const burger = document.querySelector('.burger');
const nav = document.querySelector('.nav-links');

// Check if burger exists before adding event listener
if (burger) {
    burger.addEventListener('click', () => {
        // Toggle Nav
        nav.classList.toggle('active');

        // Animate Links
        const navLinks = document.querySelectorAll('.nav-links li');
        navLinks.forEach((link, index) => {
            if (link.style.animation) {
                link.style.animation = '';
            } else {
                link.style.animation = `navLinkFade 0.5s ease forwards ${index / 7 + 0.3}s`;
            }
        });

        // Burger Animation
        burger.classList.toggle('toggle');
    });
}

// Scroll Reveal Animation
const revealElements = document.querySelectorAll('.reveal');

const revealOnScroll = () => {
    revealElements.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;

        if (elementTop < windowHeight - 100) {
            element.classList.add('active');
        }
    });
};

window.addEventListener('scroll', revealOnScroll);

// Smooth Scrolling
// Logout functionality
const logoutBtn = document.querySelector('#logoutBtn');
if (logoutBtn) {
    logoutBtn.addEventListener('click', function(e) {
        e.preventDefault();

        // Clear admin token to properly logout
        localStorage.removeItem('admin_token');

        // Clear any other auth tokens if they exist
        localStorage.removeItem('authToken');
        sessionStorage.removeItem('authToken');

        // Redirect to admin page which will show the login form
        window.location.href = '../pages/admin.html';
    });
}

// GSAP Animations
gsap.from('.hero-content', {
    duration: 1,
    y: -50,
    opacity: 0,
    ease: 'power2.out'
});

gsap.from('.feature-card', {
    duration: 1,
    y: 50,
    opacity: 0,
    stagger: 0.2,
    ease: 'power2.out',
    scrollTrigger: {
        trigger: '.features',
        start: 'top 80%'
    }
});

// Form Validation
const forms = document.querySelectorAll('form');

forms.forEach(form => {
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const inputs = form.querySelectorAll('input, textarea');
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('error');
            } else {
                input.classList.remove('error');
            }
        });

        if (isValid) {
            // Add loading state
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            submitButton.innerHTML = '<div class="loading-spinner"></div>';

            // Simulate form submission
            setTimeout(() => {
                submitButton.textContent = 'Message Sent!';
                setTimeout(() => {
                    submitButton.textContent = originalText;
                    form.reset();
                }, 2000);
            }, 1500);
        }
    });
});

// Add active class to current navigation link
const currentLocation = location.href;
const menuItems = document.querySelectorAll('.nav-links a');

menuItems.forEach(item => {
    if (item.href === currentLocation) {
        item.classList.add('active');
    }
});

// Parallax Effect
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const parallaxElements = document.querySelectorAll('.parallax');

    parallaxElements.forEach(element => {
        const speed = element.dataset.speed || 0.5;
        element.style.transform = `translateY(${scrolled * speed}px)`;
    });
});
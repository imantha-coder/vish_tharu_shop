// Intersection Observer for Scroll Animations
const observerOptions = {
    root: null,
    rootMargin: '0px',
    threshold: 0.1
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate');
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Apply observer to elements with animation classes
document.querySelectorAll('.animate-on-scroll').forEach(element => {
    observer.observe(element);
});

// Hover Animation Effects
const hoverElements = document.querySelectorAll('.hover-effect');

hoverElements.forEach(element => {
    element.addEventListener('mouseenter', () => {
        element.classList.add('hover-active');
    });
    
    element.addEventListener('mouseleave', () => {
        element.classList.remove('hover-active');
    });
});

// Text Animation
const textElements = document.querySelectorAll('.animate-text');

textElements.forEach(element => {
    const text = element.textContent;
    element.textContent = '';
    
    text.split('').forEach((char, index) => {
        const span = document.createElement('span');
        span.textContent = char;
        span.style.animationDelay = `${index * 0.1}s`;
        element.appendChild(span);
    });
});

// Loading Animation
const loadingElements = document.querySelectorAll('.loading');

loadingElements.forEach(element => {
    const dots = element.querySelector('.loading-dots');
    let dotCount = 0;
    
    setInterval(() => {
        dotCount = (dotCount + 1) % 4;
        dots.textContent = '.'.repeat(dotCount);
    }, 500);
});

// Parallax Scrolling
const parallaxElements = document.querySelectorAll('.parallax');

window.addEventListener('scroll', () => {
    const scrollPosition = window.pageYOffset;
    
    parallaxElements.forEach(element => {
        const speed = element.dataset.speed || 0.5;
        const yPos = -(scrollPosition * speed);
        element.style.transform = `translateY(${yPos}px)`;
    });
});

// Smooth Page Transitions
document.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', (e) => {
        if (link.hostname === window.location.hostname) {
            e.preventDefault();
            const target = link.getAttribute('href');
            
            document.body.classList.add('fade-out');
            
            setTimeout(() => {
                window.location.href = target;
            }, 500);
        }
    });
});

// Remove cursor animation code as there's no matching element
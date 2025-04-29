document.addEventListener('DOMContentLoaded', function() {
    // Fade in elements
    const fadeElements = document.querySelectorAll('.fade-in');
    if (fadeElements && fadeElements.length > 0) {
        fadeElements.forEach((element, index) => {
            if (element) {
                setTimeout(() => {
                    element.style.opacity = '1';
                }, index * 200);
            }
        });
    }

    // Slide in elements
    const slideElements = document.querySelectorAll('.slide-in');
    if (slideElements && slideElements.length > 0) {
        slideElements.forEach((element, index) => {
            if (element) {
                setTimeout(() => {
                    element.style.transform = 'translateX(0)';
                }, index * 200);
            }
        });
    }

    // Initialize tooltips if they exist
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    if (tooltipElements && tooltipElements.length > 0) {
        tooltipElements.forEach(element => {
            if (element) {
                element.addEventListener('mouseenter', function() {
                    const tooltip = document.createElement('div');
                    tooltip.className = 'tooltip';
                    tooltip.textContent = this.getAttribute('data-tooltip');
                    document.body.appendChild(tooltip);

                    const rect = this.getBoundingClientRect();
                    if (rect) {
                        tooltip.style.top = `${rect.top - tooltip.offsetHeight - 10}px`;
                        tooltip.style.left = `${rect.left + (rect.width - tooltip.offsetWidth) / 2}px`;
                    }
                });

                element.addEventListener('mouseleave', function() {
                    const tooltip = document.querySelector('.tooltip');
                    if (tooltip) {
                        tooltip.remove();
                    }
                });
            }
        });
    }
}); 
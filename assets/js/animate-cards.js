document.addEventListener('DOMContentLoaded', function () {
const cards = document.querySelectorAll('.animate-card');

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
    if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target); // Animate once
    }
    });
}, {
    threshold: 0.1
});

cards.forEach(card => observer.observe(card));
});
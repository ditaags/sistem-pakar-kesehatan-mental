import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();

// Fade-up halus saat section masuk viewport (400-500ms, hanya sekali)
document.addEventListener('DOMContentLoaded', () => {
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08 });

        document.querySelectorAll('.reveal-fade-up').forEach(el => observer.observe(el));
    } else {
        document.querySelectorAll('.reveal-fade-up').forEach(el => el.classList.add('is-visible'));
    }
});

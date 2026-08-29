import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

// Global Toast Notification Helper
window.toast = function (message = 'Action successful', type = 'success') {
    window.dispatchEvent(new CustomEvent('toast', {
        detail: { message, type }
    }));
};

// Alpine Animated Counter Directive / Helper
document.addEventListener('alpine:init', () => {
    Alpine.data('counter', (target = 0, duration = 2000) => ({
        current: 0,
        target: target,
        duration: duration,
        start() {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / this.duration, 1);
                this.current = Math.floor(progress * this.target);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                } else {
                    this.current = this.target;
                }
            };
            window.requestAnimationFrame(step);
        }
    }));
});

// Configure Chart.js Global Typography & Colors
if (window.Chart) {
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.color = '#94A3B8';
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.padding = 16;
}

Alpine.start();

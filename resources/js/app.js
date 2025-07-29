import './bootstrap';
import './video-recorder.js';
import Alpine from 'alpinejs';

// Initialiser Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Code existant
const actionButton = document.getElementById('actionButton');
if (actionButton) {
    actionButton.addEventListener('click', function() {
        alert('Button clicked!');
    });
}

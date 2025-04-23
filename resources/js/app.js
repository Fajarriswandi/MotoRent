import './bootstrap';
import * as bootstrap from 'bootstrap';

// Setup Iconify
const script = document.createElement('script');
script.src = 'https://code.iconify.design/2/2.2.1/iconify.min.js'; // versi stabil
script.defer = true;
document.head.appendChild(script);


document.addEventListener('DOMContentLoaded', function () {
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});

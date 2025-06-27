import './bootstrap';

// Import SweetAlert2
import Swal from 'sweetalert2';
window.Swal = Swal; // <-- Membuatnya bisa dipanggil dari mana saja (termasuk Blade)

import Alpine from 'alpinejs';

(async () => {
    try {
        const twElements = await import("tw-elements");
        const { Input, Ripple, initTWE } = twElements;
        initTWE({ Input, Ripple });
        console.log('TW-Elements Initialized'); // Pesan debug
    } catch (e) {
        console.error('Failed to initialize TW-Elements', e);
    }
})();
// --- END ---

window.Alpine = Alpine;
Alpine.start();
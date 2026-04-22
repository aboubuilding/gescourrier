/**
 * services/config.js
 * Constantes globales & Notifications SweetAlert2 élégantes (MODULE SERVICES)
 */

window.CSRF_TOKEN =
    document.querySelector('meta[name="csrf-token"]')?.content || '';

window.servicesConfig = {
    csrfToken: window.CSRF_TOKEN,

    routes: {
        base: '/services',
        store: '/services',
        show: '/services/',
        edit: '/services/',
        update: '/services/update/',
        delete: '/services/',
    }
};

// ─────────────────────────────────────
// NOTIFICATIONS SWEETALERT2 ÉLÉGANTES
// ─────────────────────────────────────

if (typeof window.showNotification !== 'function') {

    window.showNotification = function (message, type = 'success', options = {}) {

        if (typeof Swal === 'undefined') {
            console.log(`[${type.toUpperCase()}] ${message}`);
            return Promise.resolve();
        }

        const themes = {
            success: { icon: 'success', color: '#10b981', bg: 'rgba(16,185,129,0.12)' },
            error:   { icon: 'error',   color: '#ef4444', bg: 'rgba(239,68,68,0.12)' },
            warning: { icon: 'warning', color: '#f59e0b', bg: 'rgba(245,158,11,0.12)' },
            info:    { icon: 'info',    color: '#3b82f6', bg: 'rgba(59,130,246,0.12)' },
        };

        const theme = themes[type] || themes.info;

        return Swal.fire({
            toast: options.modal !== true,
            position: options.modal ? 'center' : 'top-end',
            icon: theme.icon,

            title: options.modal ? message : undefined,
            text: options.modal ? undefined : message,

            showConfirmButton: options.modal === true,
            timer: options.timer ?? 3000,
            timerProgressBar: true,

            background: '#fff',
            backdrop: options.modal ? theme.bg : false,

            customClass: {
                popup: `colored-toast swal2-${type}`
            },

            didOpen: (popup) => {
                if (!options.modal) {
                    popup.addEventListener('mouseenter', Swal.stopTimer);
                    popup.addEventListener('mouseleave', Swal.resumeTimer);
                }
            }
        });
    };
}

// ─────────────────────────────────────
// STYLE SWEETALERT2 (inchangé mais propre)
// ─────────────────────────────────────

(function injectSweetAlertStyles() {
    if (document.getElementById('swal-custom-styles')) return;

    const style = document.createElement('style');
    style.id = 'swal-custom-styles';

    style.textContent = `
        .swal2-popup {
            font-family: inherit !important;
            border-radius: 14px !important;
            padding: 1.2rem !important;
        }

        .toast-popup {
            max-width: 320px !important;
            border-left: 4px solid var(--swal-color, #3b82f6) !important;
        }

        .modal-popup {
            border-top: 4px solid var(--swal-color, #3b82f6) !important;
        }

        .swal2-title {
            font-size: 1.05rem !important;
            font-weight: 600 !important;
        }

        .swal2-html-container {
            font-size: 0.9rem !important;
        }

        .swal2-confirm {
            border-radius: 10px !important;
            font-size: 0.9rem !important;
        }
    `;

    document.head.appendChild(style);
})();
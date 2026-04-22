/**
 * organisations/config.js
 * Constantes globales & Notifications SweetAlert2 élégantes
 */

// ─────────────────────────────────────
// CONFIGURATION GLOBALE
// ─────────────────────────────────────



window.CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';

window.organisationsConfig = {
    csrfToken: window.CSRF_TOKEN,
    routes: {
        base:        '/organisations',
        store:       '/organisations',
        show:        '/organisations/',
        edit:        '/organisations/',
        update:      '/organisations/update/',
        delete:      '/organisations/',
        disable:     '/organisations/',
        enable:      '/organisations/',
        bulkDelete:  '/organisations/bulk-delete',
        bulkDisable: '/organisations/bulk-disable',
        export:      '/organisations/export',
    }
};

// ─────────────────────────────────────
// NOTIFICATIONS SWEETALERT2 ÉLÉGANTES
// ─────────────────────────────────────

if (typeof window.showNotification !== 'function') {
    
    /**
     * Affiche une notification SweetAlert2 stylisée
     * @param {string} message - Le message à afficher
     * @param {string} type - 'success' | 'error' | 'warning' | 'info'
     * @param {object} options - Options supplémentaires (optionnel)
     */
    window.showNotification = function(message, type = 'success', options = {}) {
        
        // Fallback si SweetAlert2 n'est pas chargé
        if (typeof Swal === 'undefined') {
            if (typeof toastr !== 'undefined') {
                toastr.options = {
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    timeOut: 3000,
                    closeButton: true
                };
                toastr[type]?.(message) ?? toastr.info(message);
            } else {
                console.log(`[${type.toUpperCase()}] ${message}`);
            }
            return Promise.resolve();
        }

        // Configuration par type
        const themes = {
            success: {
                icon: 'success',
                title: 'Succès',
                color: '#10b981',
                bg: 'rgba(16, 185, 129, 0.12)',
                iconColor: '#10b981'
            },
            error: {
                icon: 'error',
                title: 'Erreur',
                color: '#ef4444',
                bg: 'rgba(239, 68, 68, 0.12)',
                iconColor: '#ef4444'
            },
            warning: {
                icon: 'warning',
                title: 'Attention',
                color: '#f59e0b',
                bg: 'rgba(245, 158, 11, 0.12)',
                iconColor: '#f59e0b'
            },
            info: {
                icon: 'info',
                title: 'Info',
                color: '#3b82f6',
                bg: 'rgba(59, 130, 246, 0.12)',
                iconColor: '#3b82f6'
            }
        };

        const theme = themes[type] || themes.info;
        
        // Options par défaut
        const defaults = {
            // Mode toast (coin) ou modal (centré)
            toast: options.modal !== true,
            position: options.modal === true ? 'center' : 'top-end',
            
            // Style
            background: '#ffffff',
            backdrop: options.modal === true ? theme.bg : 'transparent',
            showConfirmButton: options.modal === true,
            showCancelButton: false,
            
            // Timer
            timer: options.timer ?? (options.modal === true ? 4000 : 3000),
            timerProgressBar: true,
            
            // Animation
            showClass: { popup: 'swal2-noanimation', backdrop: 'swal2-noanimation' },
            hideClass: { popup: 'swal2-noanimation', backdrop: 'swal2-noanimation' },
            
            // Contenu
            icon: theme.icon,
            iconColor: theme.iconColor,
            title: options.modal === true ? theme.title : undefined,
            text: options.modal === true ? message : undefined,
            titleText: options.toast !== false ? message : undefined,
            
            // Custom classes pour styling avancé
            customClass: {
                popup: `colored-toast swal2-${type} ${options.modal === true ? 'modal-popup' : 'toast-popup'}`,
                title: 'swal2-title',
                htmlContainer: 'swal2-html-container',
                timerProgressBar: 'swal2-timer-progress-bar-custom',
                confirmButton: `btn-slim btn-slim-${type}`
            },
            
            // Bouton OK personnalisé
            confirmButtonText: options.confirmButtonText || 'OK',
            confirmButtonColor: theme.color,
            
            // Pause timer au survol
            didOpen: (popup) => {
                if (!options.modal) {
                    popup.addEventListener('mouseenter', Swal.stopTimer);
                    popup.addEventListener('mouseleave', Swal.resumeTimer);
                }
            }
        };

        // Fusion des options
        const config = { ...defaults, ...options };

        // Affichage
        return Swal.fire(config);
    };
}

// ─────────────────────────────────────
// INJECTION DES STYLES SWEETALERT2
// ─────────────────────────────────────

(function injectSweetAlertStyles() {
    if (document.getElementById('swal-custom-styles')) return;
    
    const style = document.createElement('style');
    style.id = 'swal-custom-styles';
    style.textContent = `
        /* ========== POPUP PRINCIPAL ========== */
        .swal2-popup {
            font-family: inherit !important;
            border-radius: 14px !important;
            padding: 1.25rem !important;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12) !important;
        }
        
        /* Toast en coin */
        .toast-popup {
            max-width: 320px !important;
            margin: 0.5rem !important;
            border-left: 4px solid var(--swal-color, #3b82f6) !important;
        }
        
        /* Modal centré */
        .modal-popup {
            border-top: 4px solid var(--swal-color, #3b82f6) !important;
        }
        
        /* Titre */
        .swal2-title {
            font-size: 1.1rem !important;
            font-weight: 600 !important;
            color: #1e293b !important;
            margin: 0 0 0.5rem 0 !important;
        }
        
        /* Message */
        .swal2-html-container,
        .swal2-title-text {
            font-size: 0.95rem !important;
            color: #475569 !important;
            margin: 0 !important;
            line-height: 1.4 !important;
        }
        
        /* Icônes personnalisées */
        .swal2-icon {
            margin: 0.5rem auto 1rem !important;
            border-width: 3px !important;
            width: 48px !important;
            height: 48px !important;
        }
        
        .swal2-success {
            border-color: #10b981 !important;
            color: #10b981 !important;
        }
        .swal2-success .swal2-success-ring {
            border-color: rgba(16, 185, 129, 0.3) !important;
        }
        .swal2-success [class^='swal2-success-line'] {
            background-color: #10b981 !important;
        }
        
        .swal2-error {
            border-color: #ef4444 !important;
            color: #ef4444 !important;
        }
        .swal2-error .swal2-error-x-mark {
            border-color: #ef4444 !important;
        }
        
        .swal2-warning {
            border-color: #f59e0b !important;
            color: #f59e0b !important;
        }
        
        .swal2-info {
            border-color: #3b82f6 !important;
            color: #3b82f6 !important;
        }
        
        /* Bouton de confirmation */
        .btn-slim-success { background: #10b981 !important; }
        .btn-slim-error { background: #ef4444 !important; }
        .btn-slim-warning { background: #f59e0b !important; }
        .btn-slim-info { background: #3b82f6 !important; }
        
        .swal2-confirm {
            border-radius: 10px !important;
            font-weight: 500 !important;
            padding: 0.5rem 1.5rem !important;
            font-size: 0.9rem !important;
            border: none !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
            transition: transform 0.15s ease, box-shadow 0.15s ease !important;
        }
        .swal2-confirm:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
        }
        .swal2-confirm:active {
            transform: translateY(0) !important;
        }
        
        /* Barre de progression du timer */
        .swal2-timer-progress-bar-custom {
            background: rgba(255,255,255,0.7) !important;
            border-radius: 0 0 14px 14px !important;
            height: 4px !important;
        }
        
        /* Animation d'entrée douce */
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideInDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .toast-popup.swal2-show {
            animation: slideInRight 0.3s ease-out !important;
        }
        .modal-popup.swal2-show {
            animation: slideInDown 0.3s ease-out !important;
        }
        
        /* Mode sombre (optionnel) */
        @media (prefers-color-scheme: dark) {
            .swal2-popup {
                background: #1e293b !important;
            }
            .swal2-title {
                color: #f1f5f9 !important;
            }
            .swal2-html-container,
            .swal2-title-text {
                color: #cbd5e1 !important;
            }
            .swal2-timer-progress-bar-custom {
                background: rgba(255,255,255,0.4) !important;
            }
        }
    `;
    document.head.appendChild(style);
})();
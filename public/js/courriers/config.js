/**
 * Configuration Globale & Helpers
 */

// Variables Globales
window.CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content') || '';
window.TOASTR_CONFIG = {
    progressBar: true,
    positionClass: 'toast-top-right',
    timeOut: 3000,
    closeButton: true
};

// Helper: Notification
/**
 * Affiche une notification "Smart Alert" avec SweetAlert2
 * @param {string} message - Le message à afficher
 * @param {string} type - 'success', 'error', 'warning', 'info'
 * @param {boolean} toastMode - Si true, affiche en toast (coin), sinon en modal centré
 */
window.showNotification = function(message, type = 'info', toastMode = true) {
    
    // Fallback si SweetAlert2 n'est pas chargé
    if (typeof Swal === 'undefined') {
        console.log(`[${type.toUpperCase()}] ${message}`);
        // Fallback Toastr si disponible
        if (typeof toastr !== 'undefined') {
            toastr.options = window.TOASTR_CONFIG || { positionClass: 'toast-top-right', timeOut: 60000 };
            toastr[type](message);
        }
        return;
    }

    // Configuration des icônes et couleurs par type
    const config = {
        success: {
            icon: 'success',
            title: 'Succès !',
            color: '#10b981',
            bg: 'rgba(16, 185, 129, 0.15)'
        },
        error: {
            icon: 'error',
            title: 'Erreur',
            color: '#ef4444',
            bg: 'rgba(239, 68, 68, 0.15)'
        },
        warning: {
            icon: 'warning',
            title: 'Attention',
            color: '#f59e0b',
            bg: 'rgba(245, 158, 11, 0.15)'
        },
        info: {
            icon: 'info',
            title: 'Info',
            color: '#3b82f6',
            bg: 'rgba(59, 130, 246, 0.15)'
        }
    };

    const settings = config[type] || config.info;

    // Options communes
    const commonOptions = {
        background: '#ffffff',
        backdrop: settings.bg,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        customClass: {
            popup: 'colored-toast swal2-show',
            title: 'swal2-title',
            htmlContainer: 'swal2-html-container',
            timerProgressBar: 'swal2-timer-progress-bar'
        },
        didOpen: (popup) => {
            popup.addEventListener('mouseenter', Swal.stopTimer);
            popup.addEventListener('mouseleave', Swal.resumeTimer);
        }
    };

    // Mode Toast (coin de l'écran) vs Modal (centré)
    if (toastMode) {
        Swal.fire({
            ...commonOptions,
            toast: true,
            position: 'top-end',
            icon: settings.icon,
            title: message,
            iconColor: settings.color,
            customClass: {
                ...commonOptions.customClass,
                popup: 'colored-toast toast-position'
            }
        });
    } else {
        // Mode Modal centré (plus impactant)
        Swal.fire({
            ...commonOptions,
            position: 'center',
            icon: settings.icon,
            title: settings.title,
            text: message,
            iconColor: settings.color,
            showCancelButton: false,
            confirmButtonColor: settings.color,
            confirmButtonText: 'OK'
        });
    }
};

// Helper: Toggle Button Loading
window.toggleButtonLoading = function($btn, $spinner, isLoading) {
    $btn.prop('disabled', isLoading);
    if ($spinner) $spinner.toggleClass('d-none', !isLoading);
};

// Initialisation DataTable (Stockée dans une variable globale pour accès ailleurs)
window.courrierTable = null;

function initDataTable() {
    window.courrierTable = $('#courriersTable').DataTable({
        responsive: true,
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json' },
        pageLength: 15,
        order: [], 
        columnDefs: [{ orderable: false, targets: -1 }],
        dom: '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>'
    });

    
}
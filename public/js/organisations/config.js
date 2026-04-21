/**
 * CONFIGURATION GLOBALE - Organisations
 */

// Configuration de Toastr
toastr.options = {
    progressBar: true,
    positionClass: 'toast-top-right',
    timeOut: 3500,
    closeButton: true,
    showDuration: 300,
    hideDuration: 300,
    extendedTimeOut: 1000,
    showEasing: 'swing',
    hideEasing: 'linear',
    showMethod: 'fadeIn',
    hideMethod: 'fadeOut'
};

// Configuration globale
window.organisationsConfig = {
    csrfToken: $('meta[name="csrf-token"]').attr('content'),
    routes: {
        index: '/organisations',
        store: '/organisations',
        update: '/organisations/',
        delete: '/organisations/',
        restore: '/organisations/',
        edit: '/organisations/',
        export: '/organisations/export'
    },
    constants: {
        ETAT_ACTIF: 1,
        ETAT_INACTIF: 0
    }
};

// Initialisation de la DataTable
function initDataTable() {
    return $('#organisationsTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
        },
        pageLength: 15,
        lengthMenu: [[15, 30, 50, -1], [15, 30, 50, 'Tous']],
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: -1 }
        ],
        dom: '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn-success btn-sm',
                exportOptions: { columns: [0, 1, 2, 3, 4] }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn-danger btn-sm',
                exportOptions: { columns: [0, 1, 2, 3, 4] }
            },
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn-primary btn-sm',
                exportOptions: { columns: [0, 1, 2, 3, 4] }
            }
        ]
    });
}

// Fonction utilitaire pour afficher les erreurs de validation
function showValidationErrors(errors) {
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    
    Object.keys(errors).forEach(field => {
        const $field = $(`[name="${field}"]`);
        if ($field.length) {
            $field.addClass('is-invalid');
            $field.after(`<div class="invalid-feedback">${errors[field][0]}</div>`);
        }
    });
}

// Fonction utilitaire pour effacer les erreurs
function clearValidationErrors() {
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
}

// Fonction utilitaire pour toggle le loading du bouton
function toggleButtonLoading($btn, $spinner, isLoading) {
    if (isLoading) {
        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');
    } else {
        $btn.prop('disabled', false);
        $spinner.addClass('d-none');
    }
}
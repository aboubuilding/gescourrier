/**
 * SELECT2 - Organisations
 */

$(document).ready(function() {
    initSelect2();
});

function initSelect2() {
    // Initialiser Select2 sur les selects qui en ont besoin
    $('.select2').each(function() {
        if (!$(this).hasClass('select2-hidden-accessible')) {
            $(this).select2({
                width: '100%',
                dropdownParent: $(this).closest('.modal').length ? $(this).closest('.modal') : $(document.body),
                placeholder: $(this).data('placeholder') || 'Sélectionner une option',
                allowClear: $(this).data('allow-clear') || false
            });
        }
    });
}

// Réinitialiser Select2 après un chargement dynamique
function refreshSelect2() {
    $('.select2').each(function() {
        if ($(this).hasClass('select2-hidden-accessible')) {
            $(this).select2('destroy');
        }
        $(this).select2({
            width: '100%',
            dropdownParent: $(this).closest('.modal').length ? $(this).closest('.modal') : $(document.body)
        });
    });
}
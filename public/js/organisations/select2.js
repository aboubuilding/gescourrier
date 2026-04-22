/**
 * organisations/select2.js
 * Initialisation Select2 pour les modals organisations
 */

function initOrganisationSelect2Helpers() {

    function initSelect2OnModal(modalId) {
        $(`${modalId} .select2`).each(function () {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
            $(this).select2({
                width: '100%',
                dropdownParent: $(modalId),
                placeholder:   $(this).data('placeholder') || 'Sélectionner une option',
                allowClear:    $(this).data('allow-clear') === true
            });
        });
    }

    // Initialiser Select2 à l'ouverture de chaque modal
    $('#modalCreateOrganisation').off('shown.bs.modal.select2').on('shown.bs.modal.select2', function () {
        initSelect2OnModal('#modalCreateOrganisation');
    });

    $('#modalEditOrganisation').off('shown.bs.modal.select2').on('shown.bs.modal.select2', function () {
        initSelect2OnModal('#modalEditOrganisation');
    });

    // Select2 globaux hors modal (si présents)
    $('.select2:not(.modal .select2)').each(function () {
        if (!$(this).hasClass('select2-hidden-accessible')) {
            $(this).select2({ width: '100%' });
        }
    });
}
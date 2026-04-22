/**
 * services/select2.js
 * Initialisation Select2 pour les modals services
 */

function initServiceSelect2Helpers() {

    function initSelect2OnModal(modalId) {
        $(`${modalId} .select2`).each(function () {

            // Éviter double init
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }

            $(this).select2({
                width: '100%',
                dropdownParent: $(modalId),

                placeholder: $(this).data('placeholder') || 'Sélectionner une option',
                allowClear: $(this).data('allow-clear') === true,

                // amélioration UX (utile pour liste d'organisations)
                minimumResultsForSearch: 0
            });
        });
    }

    // ─────────────────────────────────────
    // MODAL CREATE SERVICE
    // ─────────────────────────────────────

    $('#modalCreateService')
        .off('shown.bs.modal.select2')
        .on('shown.bs.modal.select2', function () {
            initSelect2OnModal('#modalCreateService');
        });

    // ─────────────────────────────────────
    // MODAL EDIT SERVICE
    // ─────────────────────────────────────

    $('#modalEditService')
        .off('shown.bs.modal.select2')
        .on('shown.bs.modal.select2', function () {
            initSelect2OnModal('#modalEditService');
        });

    // ─────────────────────────────────────
    // SELECT2 GLOBAL (hors modal)
    // ─────────────────────────────────────

    $('.select2:not(.modal .select2)').each(function () {
        if (!$(this).hasClass('select2-hidden-accessible')) {
            $(this).select2({
                width: '100%',
                placeholder: $(this).data('placeholder') || 'Sélectionner une option',
                allowClear: $(this).data('allow-clear') === true
            });
        }
    });

    // ─────────────────────────────────────
    // UX : refresh select2 si modal réouverte
    // ─────────────────────────────────────

    $(document).on('hidden.bs.modal', '#modalCreateService, #modalEditService', function () {
        $(this).find('.select2').val(null).trigger('change');
    });
}
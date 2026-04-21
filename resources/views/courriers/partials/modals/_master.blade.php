{{-- ============================================
     CONTENEUR PRINCIPAL DES MODALS
     Inclut CSS, JS et tous les modals
============================================ --}}

{{-- 1. STYLES (Injectés dans la section CSS du layout parent) --}}
@push('css')
<style>
    /* ========== MODALS AMÉLIORÉS ========== */
    .modal-content { border: none; border-radius: 24px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; }
    .modal-header { background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%); border-bottom: 1px solid rgba(0, 0, 0, 0.05); padding: 1.5rem 1.75rem; }
    .modal-header .modal-title { font-size: 1.35rem; font-weight: 700; display: flex; align-items: center; gap: 12px; }
    .modal-header .modal-title i { font-size: 1.5rem; }
    .modal-body { padding: 1.75rem; background: #ffffff; max-height: 70vh; overflow-y: auto; }
    .modal-footer { background: #f8fafc; border-top: 1px solid rgba(0, 0, 0, 0.05); padding: 1.25rem 1.75rem; }

    /* ========== CHAMPS MODERNES ========== */
    .form-group-modern { margin-bottom: 1.35rem; }
    .form-label-modern { display: flex; align-items: center; gap: 8px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #475569; margin-bottom: 0.6rem; }
    .form-label-modern i { font-size: 0.85rem; color: #c0392b; }
    .form-label-modern .required { color: #ef4444; margin-left: 4px; }
    .form-control-modern, .form-select-modern { width: 100%; padding: 0.75rem 1rem; font-size: 0.9rem; border: 1.5px solid #e2e8f0; border-radius: 14px; transition: all 0.2s ease; background: #ffffff; font-family: inherit; }
    .form-control-modern:focus, .form-select-modern:focus { border-color: #c0392b; box-shadow: 0 0 0 4px rgba(192, 57, 43, 0.1); outline: none; }
    .form-control-modern:hover, .form-select-modern:hover { border-color: #cbd5e1; }

    /* ========== UPLOAD DE FICHIER ========== */
    .file-upload-modern { position: relative; border: 2px dashed #e2e8f0; border-radius: 20px; padding: 1.75rem; text-align: center; cursor: pointer; transition: all 0.25s ease; background: #fafcff; }
    .file-upload-modern:hover { border-color: #c0392b; background: #fef2f2; }
    .file-upload-modern i { font-size: 2.2rem; color: #94a3b8; margin-bottom: 0.75rem; transition: color 0.25s; }
    .file-upload-modern:hover i { color: #c0392b; }
    .file-upload-modern .upload-text { font-size: 0.9rem; font-weight: 500; color: #475569; }
    .file-upload-modern .upload-hint { font-size: 0.7rem; color: #94a3b8; margin-top: 0.5rem; }
    .file-upload-modern input { position: absolute; opacity: 0; width: 100%; height: 100%; top: 0; left: 0; cursor: pointer; }
    .file-info-modern { margin-top: 1rem; padding: 0.75rem; background: #f1f5f9; border-radius: 12px; display: flex; align-items: center; gap: 10px; font-size: 0.8rem; display: none; }
    .file-info-modern i { font-size: 1.1rem; color: #c0392b; }

    /* ========== BOUTONS MODERNES ========== */
    .btn-modern { padding: 0.65rem 1.5rem; border-radius: 40px; font-weight: 600; font-size: 0.85rem; transition: all 0.25s ease; border: none; display: inline-flex; align-items: center; gap: 8px; }
    .btn-modern-primary { background: linear-gradient(135deg, #c0392b 0%, #96281b 100%); color: white; box-shadow: 0 2px 8px rgba(192, 57, 43, 0.3); }
    .btn-modern-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(192, 57, 43, 0.4); }
    .btn-modern-secondary { background: #f1f5f9; color: #475569; }
    .btn-modern-secondary:hover { background: #e2e8f0; transform: translateY(-1px); }
    .btn-modern-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3); }
    .btn-modern-warning:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(245, 158, 11, 0.4); }

    /* ========== SELECT2 & GRID ========== */
    .select2-container--default .select2-selection--single { border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 0.5rem; height: auto; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 1.5; color: #1e293b; }
    .form-grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.25rem; }
    .form-grid-full { grid-column: span 2; }
    .date-field { transition: all 0.3s ease; }
    
    @media (max-width: 768px) {
        .form-grid-2 { grid-template-columns: 1fr; gap: 1rem; }
        .form-grid-full { grid-column: span 1; }
        .modal-body { padding: 1.25rem; }
    }
</style>
@endpush

{{-- 2. INCLUSION DES MODALS (HTML) --}}
@include('courriers.partials.modals._create')
@include('courriers.partials.modals._edit')
@include('courriers.partials.modals._affecter')
@include('courriers.partials.modals._export')
@include('courriers.partials.modals._show')

{{-- 3. JAVASCRIPT (Injecté dans la section JS du layout parent) --}}
@push('js')
<script>
$(document).ready(function() {
    
    // ========== CORRECTION SELECT2 QUI NE SE FERME PAS ==========
    $.fn.modal.Constructor.prototype._enforceFocus = function() {};

    function initSelect2(selector, parentModal) {
        $(selector).select2({
            dropdownParent: $(parentModal),
            width: '100%',
            placeholder: 'Sélectionner...',
            allowClear: true,
            closeOnSelect: true
        });
        $(selector).on('select2:select', function (e) { $(this).select2('close'); });
    }

    // Initialisation
    initSelect2('.select2-organisation', '#modalCreate');
    initSelect2('.select2-organisation', '#modalEdit');
    initSelect2('.select2-agent, .select2-service', '#modalAffecter');

    // Réinitialisation à l'ouverture
    $('#modalCreate, #modalEdit, #modalAffecter').on('shown.bs.modal', function () {
        const modalId = '#' + $(this).attr('id');
        if(modalId === '#modalAffecter') {
            initSelect2('.select2-agent, .select2-service', modalId);
        } else {
            initSelect2('.select2-organisation', modalId);
        }
    });

    // ========== GESTION DES DATES ==========
    function toggleDateFields(type, isEdit = false) {
        $('.date-reception-field, .date-envoi-field').hide();
        if (type == '0') $('.date-reception-field').show(); // Entrant
        else if (type == '1') $('.date-envoi-field').show(); // Sortant
    }

    $('#createType').on('change', function() { toggleDateFields($(this).val(), false); });
    $('#editType').on('change', function() { toggleDateFields($(this).val(), true); });

    // ========== UPLOAD DE FICHIER ==========
    $('#fileInputModern').on('change', function() {
        const fileName = $(this).val().split('\\').pop();
        if (fileName) { $('#fileNameModern').text(fileName); $('#fileInfoModern').css('display', 'flex'); } 
        else { $('#fileInfoModern').hide(); }
    });

    $('#editFileInput').on('change', function() {
        const fileName = $(this).val().split('\\').pop();
        if (fileName) { $('#editFileCurrent').html('<i class="fas fa-file"></i> Nouveau: ' + fileName).css('display', 'flex'); }
    });

    // ========== EXPORT ANIMATIONS & CLICKS ==========
    $('.btn-export-option').on('mouseenter', function() {
        $(this).find('.export-card').addClass('shadow-lg').css('transform', 'translateY(-5px)');
    }).on('mouseleave', function() {
        $(this).find('.export-card').removeClass('shadow-lg').css('transform', 'translateY(0)');
    });

    $('#exportExcel, #exportPDF, #exportCSV').on('click', function(e) {
        e.preventDefault();
        const format = $(this).attr('id').replace('export', '');
        $('#modalExport').modal('hide');
        if (typeof toastr !== 'undefined') toastr.success('Export ' + format + ' lancé');
    });

    // ========== SOUMISSION FORMULAIRES (SPINNERS) ==========
    $('#formCreate').on('submit', function() {
        const btn = $('#btnSubmitCreate'); btn.prop('disabled', true); $('#spinnerCreate').removeClass('d-none'); btn.find('i:not(.spinner-border)').hide();
    });
    $('#formEdit').on('submit', function() {
        const btn = $('#btnSubmitEdit'); btn.prop('disabled', true); $('#spinnerEdit').removeClass('d-none'); btn.find('i:not(.spinner-border)').hide();
    });
    $('#formAffecter').on('submit', function() {
        const btn = $('#btnSubmitAffecter'); btn.prop('disabled', true); $('#spinnerAffecter').removeClass('d-none'); btn.find('i:not(.spinner-border)').hide();
    });

    // ========== RESET MODALS ==========
    $('#modalCreate').on('hidden.bs.modal', function() {
        $('#formCreate')[0].reset(); $('#fileInfoModern').hide();
        $('#btnSubmitCreate').prop('disabled', false); $('#spinnerCreate').addClass('d-none'); $('#btnSubmitCreate i').show();
        $('.date-reception-field, .date-envoi-field').hide(); $('#createType').val('').trigger('change');
        $('.select2-organisation', this).val(null).trigger('change');
    });

    $('#modalEdit').on('hidden.bs.modal', function() {
        $('#btnSubmitEdit').prop('disabled', false); $('#spinnerEdit').addClass('d-none'); $('#btnSubmitEdit i').show();
        $('.select2-organisation', this).val(null).trigger('change');
    });

    $('#modalAffecter').on('hidden.bs.modal', function() {
        $('#formAffecter')[0].reset();
        $('#btnSubmitAffecter').prop('disabled', false); $('#spinnerAffecter').addClass('d-none'); $('#btnSubmitAffecter i').show();
        $('.select2-agent, .select2-service').val(null).trigger('change');
    });

    // ========== HELPER AFFECTATION ==========
    window.fillAffectationModal = function(courrierId, courrierInfo) {
        $('#affecterCourrierId').val(courrierId);
        $('#affecterCourrierInfo').html(`<i class="fas fa-envelope"></i> ${courrierInfo}`);
    };
});
</script>
@endpush
{{-- ============================================
     STYLES DÉDIÉS AUX MODALS
============================================ --}}
@section('css')
@parent
<style>
    /* ========== MODALS AMÉLIORÉS ========== */
    .modal-content {
        border: none;
        border-radius: 24px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
    }

    .modal-header {
        background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1.5rem 1.75rem;
    }

    .modal-header .modal-title {
        font-size: 1.35rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .modal-header .modal-title i {
        font-size: 1.5rem;
    }

    .modal-body {
        padding: 1.75rem;
        background: #ffffff;
        max-height: 70vh;
        overflow-y: auto;
    }

    .modal-footer {
        background: #f8fafc;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1.25rem 1.75rem;
    }

    /* ========== CHAMPS MODERNES ========== */
    .form-group-modern {
        margin-bottom: 1.35rem;
    }

    .form-label-modern {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #475569;
        margin-bottom: 0.6rem;
    }

    .form-label-modern i {
        font-size: 0.85rem;
        color: #c0392b;
    }

    .form-label-modern .required {
        color: #ef4444;
        margin-left: 4px;
    }

    .form-control-modern,
    .form-select-modern {
        width: 100%;
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        transition: all 0.2s ease;
        background: #ffffff;
        font-family: inherit;
    }

    .form-control-modern:focus,
    .form-select-modern:focus {
        border-color: #c0392b;
        box-shadow: 0 0 0 4px rgba(192, 57, 43, 0.1);
        outline: none;
    }

    .form-control-modern:hover,
    .form-select-modern:hover {
        border-color: #cbd5e1;
    }

    /* ========== BADGES ========== */
    .priority-badge, .type-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0.35rem 0.9rem;
        border-radius: 40px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* ========== UPLOAD DE FICHIER ========== */
    .file-upload-modern {
        position: relative;
        border: 2px dashed #e2e8f0;
        border-radius: 20px;
        padding: 1.75rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.25s ease;
        background: #fafcff;
    }

    .file-upload-modern:hover {
        border-color: #c0392b;
        background: #fef2f2;
    }

    .file-upload-modern i {
        font-size: 2.2rem;
        color: #94a3b8;
        margin-bottom: 0.75rem;
        transition: color 0.25s;
    }

    .file-upload-modern:hover i {
        color: #c0392b;
    }

    .file-upload-modern .upload-text {
        font-size: 0.9rem;
        font-weight: 500;
        color: #475569;
    }

    .file-upload-modern .upload-hint {
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 0.5rem;
    }

    .file-upload-modern input {
        position: absolute;
        opacity: 0;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        cursor: pointer;
    }

    .file-info-modern {
        margin-top: 1rem;
        padding: 0.75rem;
        background: #f1f5f9;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.8rem;
        display: none;
    }

    .file-info-modern i {
        font-size: 1.1rem;
        color: #c0392b;
    }

    /* ========== BOUTONS MODERNES ========== */
    .btn-modern {
        padding: 0.65rem 1.5rem;
        border-radius: 40px;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.25s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-modern-primary {
        background: linear-gradient(135deg, #c0392b 0%, #96281b 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(192, 57, 43, 0.3);
    }

    .btn-modern-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(192, 57, 43, 0.4);
    }

    .btn-modern-secondary {
        background: #f1f5f9;
        color: #475569;
    }

    .btn-modern-secondary:hover {
        background: #e2e8f0;
        transform: translateY(-1px);
    }

    .btn-modern-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
    }

    .btn-modern-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(245, 158, 11, 0.4);
    }

    /* ========== SELECT2 DANS MODAL ========== */
    .select2-container--default .select2-selection--single {
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        padding: 0.5rem;
        height: auto;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        color: #1e293b;
    }

    /* ========== ANIMATION ========== */
    .modal.fade .modal-dialog {
        transform: scale(0.95);
        transition: transform 0.2s ease-out;
    }

    .modal.show .modal-dialog {
        transform: scale(1);
    }

    /* ========== TRANSITIONS ========== */
    .date-field {
        transition: all 0.3s ease;
    }

    /* ========== GRID ========== */
    .form-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.25rem;
    }

    .form-grid-full {
        grid-column: span 2;
    }

    @media (max-width: 768px) {
        .form-grid-2 {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        .form-grid-full {
            grid-column: span 1;
        }
        .modal-body {
            padding: 1.25rem;
        }
    }
</style>
@endsection

{{-- ============================================
     MODAL CRÉATION
============================================ --}}
<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form id="formCreate" action="{{ route('courriers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle" style="color: #c0392b;"></i>
                        Nouveau courrier
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-grid-2">
                        <!-- Type -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-tag"></i> Type <span class="required">*</span>
                            </label>
                            <select name="type" id="createType" class="form-select-modern" required>
                                <option value="">Sélectionner</option>
                                <option value="0">📥 Entrant</option>
                                <option value="1">📤 Sortant</option>
                                <option value="2">🔄 Interne</option>
                            </select>
                        </div>

                        <!-- Priorité -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-flag"></i> Priorité <span class="required">*</span>
                            </label>
                            <select name="priorite" class="form-select-modern" required>
                                <option value="0">🟢 Normale</option>
                                <option value="1">🟠 Urgente</option>
                                <option value="2">🔴 Très urgente</option>
                            </select>
                        </div>

                        <!-- Référence -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-barcode"></i> Référence
                            </label>
                            <input type="text" name="reference" class="form-control-modern" placeholder="Ex: REF-2024-001">
                        </div>

                        <!-- Numéro -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-hashtag"></i> Numéro
                            </label>
                            <input type="text" name="numero" class="form-control-modern" placeholder="Ex: N°2024-001">
                        </div>

                        <!-- Organisation (Select2 simple - Nom uniquement) -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-building"></i> Organisation <span class="required">*</span>
                            </label>
                            <select name="organisation_id" id="createOrganisation" class="form-select-modern select2-organisation" required>
                                <option value="">Sélectionner une organisation</option>
                                @foreach($organisations as $org)
                                    <option value="{{ $org->id }}">{{ $org->nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Service - CACHÉ -->
                        <div class="form-group-modern" style="display: none;">
                            <input type="hidden" name="service_id" value="">
                        </div>

                        <!-- Objet -->
                        <div class="form-grid-full">
                            <div class="form-group-modern">
                                <label class="form-label-modern">
                                    <i class="fas fa-heading"></i> Objet <span class="required">*</span>
                                </label>
                                <input type="text" name="objet" class="form-control-modern" placeholder="Saisir l'objet du courrier" required>
                            </div>
                        </div>

                        <!-- Description SUPPRIMÉE -->

                        <!-- Date réception (visible uniquement pour Entrant) -->
                        <div class="form-group-modern date-field date-reception-field">
                            <label class="form-label-modern">
                                <i class="fas fa-calendar-alt"></i> Date réception
                            </label>
                            <input type="date" name="date_reception" class="form-control-modern" value="{{ date('Y-m-d') }}">
                        </div>

                        <!-- Date envoi (visible uniquement pour Sortant) -->
                        <div class="form-group-modern date-field date-envoi-field" style="display: none;">
                            <label class="form-label-modern">
                                <i class="fas fa-paper-plane"></i> Date envoi
                            </label>
                            <input type="date" name="date_envoi" class="form-control-modern">
                        </div>

                        <!-- Fichier -->
                        <div class="form-grid-full">
                            <div class="form-group-modern">
                                <label class="form-label-modern">
                                    <i class="fas fa-paperclip"></i> Document scanné
                                </label>
                                <div class="file-upload-modern" id="fileUploadModern">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <div class="upload-text">Glissez-déposez ou cliquez pour sélectionner</div>
                                    <div class="upload-hint">PDF, JPG, PNG - Max 5 Mo</div>
                                    <input type="file" name="fichier" id="fileInputModern" accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                <div class="file-info-modern" id="fileInfoModern">
                                    <i class="fas fa-file-pdf"></i>
                                    <span id="fileNameModern"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modern btn-modern-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn-modern btn-modern-primary" id="btnSubmitCreate">
                        <span class="spinner-border spinner-border-sm d-none" id="spinnerCreate"></span>
                        <i class="fas fa-save"></i> Créer le courrier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================
     MODAL MODIFICATION
============================================ --}}
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form id="formEdit" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-pen" style="color: #f59e0b;"></i>
                        Modifier le courrier
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editId">
                    <div class="form-grid-2">
                        <!-- Type -->
                        <div class="form-group-modern">
                            <label class="form-label-modern"><i class="fas fa-tag"></i> Type</label>
                            <select name="type" id="editType" class="form-select-modern" required>
                                <option value="0">📥 Entrant</option>
                                <option value="1">📤 Sortant</option>
                                <option value="2">🔄 Interne</option>
                            </select>
                        </div>

                        <!-- Priorité -->
                        <div class="form-group-modern">
                            <label class="form-label-modern"><i class="fas fa-flag"></i> Priorité</label>
                            <select name="priorite" id="editPriorite" class="form-select-modern" required>
                                <option value="0">🟢 Normale</option>
                                <option value="1">🟠 Urgente</option>
                                <option value="2">🔴 Très urgente</option>
                            </select>
                        </div>

                        <!-- Référence -->
                        <div class="form-group-modern">
                            <label class="form-label-modern"><i class="fas fa-barcode"></i> Référence</label>
                            <input type="text" name="reference" id="editReference" class="form-control-modern">
                        </div>

                        <!-- Numéro -->
                        <div class="form-group-modern">
                            <label class="form-label-modern"><i class="fas fa-hashtag"></i> Numéro</label>
                            <input type="text" name="numero" id="editNumero" class="form-control-modern">
                        </div>

                        <!-- Organisation (Select2 simple - Nom uniquement) -->
                        <div class="form-group-modern">
                            <label class="form-label-modern"><i class="fas fa-building"></i> Organisation</label>
                            <select name="organisation_id" id="editOrganisation" class="form-select-modern select2-organisation">
                                <option value="">Sélectionner une organisation</option>
                                @foreach($organisations as $org)
                                    <option value="{{ $org->id }}">{{ $org->nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Service - CACHÉ -->
                        <div class="form-group-modern" style="display: none;">
                            <input type="hidden" name="service_id" id="editService" value="">
                        </div>

                        <!-- Objet -->
                        <div class="form-grid-full">
                            <div class="form-group-modern">
                                <label class="form-label-modern"><i class="fas fa-heading"></i> Objet</label>
                                <input type="text" name="objet" id="editObjet" class="form-control-modern" required>
                            </div>
                        </div>

                        <!-- Description SUPPRIMÉE -->

                        <!-- Dates avec affichage conditionnel -->
                        <div class="form-group-modern date-field edit-date-reception-field">
                            <label class="form-label-modern"><i class="fas fa-calendar-alt"></i> Date réception</label>
                            <input type="date" name="date_reception" id="editDateReception" class="form-control-modern">
                        </div>

                        <div class="form-group-modern date-field edit-date-envoi-field" style="display: none;">
                            <label class="form-label-modern"><i class="fas fa-paper-plane"></i> Date envoi</label>
                            <input type="date" name="date_envoi" id="editDateEnvoi" class="form-control-modern">
                        </div>

                        <!-- Fichier actuel -->
                        <div class="form-grid-full">
                            <div class="form-group-modern">
                                <label class="form-label-modern"><i class="fas fa-paperclip"></i> Document actuel</label>
                                <div id="editFileCurrent" class="file-info-modern" style="display: flex; background: #f1f5f9;">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>Aucun fichier</span>
                                </div>
                                <div class="file-upload-modern mt-2">
                                    <i class="fas fa-sync-alt"></i>
                                    <div class="upload-text">Remplacer le document</div>
                                    <div class="upload-hint">PDF, JPG, PNG - Max 5 Mo</div>
                                    <input type="file" name="fichier" id="editFileInput" accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modern btn-modern-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn-modern btn-modern-warning" id="btnSubmitEdit">
                        <span class="spinner-border spinner-border-sm d-none" id="spinnerEdit"></span>
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================
     MODAL AFFECTATION
============================================ --}}
<div class="modal fade" id="modalAffecter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formAffecter" method="POST">
                @csrf
                <input type="hidden" name="courrier_id" id="affecterCourrierId">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-share-alt" style="color: #3b82f6;"></i>
                        Affecter le courrier
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div class="priority-badge priority-normal" id="affecterCourrierInfo">
                            <i class="fas fa-envelope"></i> Chargement...
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <i class="fas fa-user-check"></i> Agent responsable <span class="required">*</span>
                        </label>
                        <select name="agent_id" class="form-select-modern select2-agent" required>
                            <option value="">Sélectionner un agent</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->prenom ?? '' }} {{ $agent->nom ?? '' }} - {{ $agent->fonction ?? 'Agent' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <i class="fas fa-users"></i> Service <span class="required">*</span>
                        </label>
                        <select name="service_id" class="form-select-modern select2-service" required>
                            <option value="">Sélectionner un service</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}">{{ $service->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <i class="fas fa-sticky-note"></i> Note d'affectation
                        </label>
                        <textarea name="note" class="form-control-modern" rows="3" placeholder="Instructions ou remarques..."></textarea>
                    </div>

                    <div class="alert alert-info mt-3" style="background: #eff6ff; border: none; border-radius: 16px;">
                        <i class="fas fa-info-circle me-2"></i>
                        Une notification sera envoyée à l'agent concerné.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modern btn-modern-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn-modern btn-modern-primary" id="btnSubmitAffecter">
                        <span class="spinner-border spinner-border-sm d-none" id="spinnerAffecter"></span>
                        <i class="fas fa-share-alt"></i> Affecter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================
     MODAL EXPORT
============================================ --}}
<div class="modal fade" id="modalExport" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-download" style="color: #10b981;"></i>
                    Exporter les données
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="row g-3">
                    <div class="col-12 mb-3">
                        <div class="alert alert-success" style="background: #ecfdf5; border: none; border-radius: 16px;">
                            <i class="fas fa-chart-line me-2"></i>
                            Choisissez le format d'export
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn-export-option w-100" id="exportExcel" style="background: none; border: none;">
                            <div class="card border-0 shadow-sm rounded-3 p-3 text-center export-card" style="transition: all 0.2s;">
                                <i class="fas fa-file-excel fa-3x" style="color: #10b981;"></i>
                                <h6 class="mt-2 mb-0">Excel</h6>
                                <small class="text-muted">.xlsx</small>
                            </div>
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn-export-option w-100" id="exportPDF" style="background: none; border: none;">
                            <div class="card border-0 shadow-sm rounded-3 p-3 text-center export-card" style="transition: all 0.2s;">
                                <i class="fas fa-file-pdf fa-3x" style="color: #ef4444;"></i>
                                <h6 class="mt-2 mb-0">PDF</h6>
                                <small class="text-muted">.pdf</small>
                            </div>
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn-export-option w-100" id="exportCSV" style="background: none; border: none;">
                            <div class="card border-0 shadow-sm rounded-3 p-3 text-center export-card" style="transition: all 0.2s;">
                                <i class="fas fa-file-csv fa-3x" style="color: #3b82f6;"></i>
                                <h6 class="mt-2 mb-0">CSV</h6>
                                <small class="text-muted">.csv</small>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn-modern btn-modern-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Fermer
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================
     JAVASCRIPT POUR LES MODALS
============================================ --}}
@section('js')
@parent
<script>
$(document).ready(function() {
    
    // ========== CORRECTION SELECT2 QUI NE SE FERME PAS ==========
    // Désactive l'enforcement du focus de Bootstrap qui entre en conflit avec Select2
    $.fn.modal.Constructor.prototype._enforceFocus = function() {};

    // Fonction d'initialisation Select2
    function initSelect2(selector, parentModal) {
        $(selector).select2({
            dropdownParent: $(parentModal),
            width: '100%',
            placeholder: 'Sélectionner...',
            allowClear: true,
            closeOnSelect: true
        });

        // Force la fermeture du dropdown après sélection
        $(selector).on('select2:select', function (e) {
            $(this).select2('close');
        });
    }

    // Initialisation au chargement
    initSelect2('.select2-organisation', '#modalCreate');
    initSelect2('.select2-organisation', '#modalEdit');
    initSelect2('.select2-agent, .select2-service', '#modalAffecter');

    // Réinitialisation propre à l'ouverture pour éviter les bugs de focus
    $('#modalCreate, #modalEdit, #modalAffecter').on('shown.bs.modal', function () {
        const modalId = '#' + $(this).attr('id');
        if(modalId === '#modalAffecter') {
            initSelect2('.select2-agent, .select2-service', modalId);
        } else {
            initSelect2('.select2-organisation', modalId);
        }
    });

    // ========== GESTION DES DATES SELON LE TYPE ==========
    function toggleDateFields(type, isEdit = false) {
        // Réinitialiser l'affichage
        $('.date-reception-field, .date-envoi-field').hide();
        
        if (type == '0') { // Entrant
            $('.date-reception-field').show();
        } else if (type == '1') { // Sortant
            $('.date-envoi-field').show();
        }
        // Interne (2) : rien ne s'affiche
    }

    // Événement pour le modal de création
    $('#createType').on('change', function() {
        toggleDateFields($(this).val(), false);
    });

    // Événement pour le modal de modification
    $('#editType').on('change', function() {
        toggleDateFields($(this).val(), true);
    });

    // ========== UPLOAD DE FICHIER ==========
    $('#fileInputModern').on('change', function() {
        const fileName = $(this).val().split('\\').pop();
        if (fileName) {
            $('#fileNameModern').text(fileName);
            $('#fileInfoModern').css('display', 'flex');
        } else {
            $('#fileInfoModern').hide();
        }
    });

    $('#editFileInput').on('change', function() {
        const fileName = $(this).val().split('\\').pop();
        if (fileName) {
            $('#editFileCurrent').html('<i class="fas fa-file"></i> Nouveau: ' + fileName).css('display', 'flex');
        }
    });

    // ========== ANIMATION DES CARTES D'EXPORT ==========
    $('.btn-export-option').on('mouseenter', function() {
        $(this).find('.export-card').addClass('shadow-lg').css('transform', 'translateY(-5px)');
    }).on('mouseleave', function() {
        $(this).find('.export-card').removeClass('shadow-lg').css('transform', 'translateY(0)');
    });

    // ========== EXPORT CLICKS ==========
    $('#exportExcel, #exportPDF, #exportCSV').on('click', function(e) {
        e.preventDefault();
        const format = $(this).attr('id').replace('export', '');
        $('#modalExport').modal('hide');
        
        if (typeof toastr !== 'undefined') {
            toastr.success('Export ' + format + ' lancé');
        } else {
            alert('Export ' + format + ' lancé');
        }
    });

    // ========== SOUMISSION DES FORMULAIRES AVEC SPINNER ==========
    $('#formCreate').on('submit', function() {
        const btn = $('#btnSubmitCreate');
        btn.prop('disabled', true);
        $('#spinnerCreate').removeClass('d-none');
        btn.find('i:not(.spinner-border)').hide();
    });

    $('#formEdit').on('submit', function() {
        const btn = $('#btnSubmitEdit');
        btn.prop('disabled', true);
        $('#spinnerEdit').removeClass('d-none');
        btn.find('i:not(.spinner-border)').hide();
    });

    $('#formAffecter').on('submit', function() {
        const btn = $('#btnSubmitAffecter');
        btn.prop('disabled', true);
        $('#spinnerAffecter').removeClass('d-none');
        btn.find('i:not(.spinner-border)').hide();
    });

    // ========== RÉINITIALISATION DES FORMULAIRES ==========
    $('#modalCreate').on('hidden.bs.modal', function() {
        $('#formCreate')[0].reset();
        $('#fileInfoModern').hide();
        $('#btnSubmitCreate').prop('disabled', false);
        $('#spinnerCreate').addClass('d-none');
        $('#btnSubmitCreate i').show();
        $('.date-reception-field, .date-envoi-field').hide();
        $('#createType').val('').trigger('change');
        // Reset Select2
        $('.select2-organisation', this).val(null).trigger('change');
    });

    $('#modalEdit').on('hidden.bs.modal', function() {
        $('#btnSubmitEdit').prop('disabled', false);
        $('#spinnerEdit').addClass('d-none');
        $('#btnSubmitEdit i').show();
        // Reset Select2
        $('.select2-organisation', this).val(null).trigger('change');
    });

    $('#modalAffecter').on('hidden.bs.modal', function() {
        $('#formAffecter')[0].reset();
        $('#btnSubmitAffecter').prop('disabled', false);
        $('#spinnerAffecter').addClass('d-none');
        $('#btnSubmitAffecter i').show();
        $('.select2-agent, .select2-service').val(null).trigger('change');
    });

    // ========== FONCTION POUR REMPLIR LE MODAL D'AFFECTATION ==========
    window.fillAffectationModal = function(courrierId, courrierInfo) {
        $('#affecterCourrierId').val(courrierId);
        $('#affecterCourrierInfo').html(`
            <i class="fas fa-envelope"></i> 
            ${courrierInfo}
        `);
    };
});
</script>
@endsection
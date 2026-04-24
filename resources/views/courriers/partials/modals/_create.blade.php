{{-- ============================================
     MODAL CRÉER UN COURRIER
============================================ --}}
<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 modal-content-custom">
            <form id="formCreate" action="{{ route('courriers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Header --}}
                <div class="modal-header border-0 px-4 py-3 modal-header-custom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="modal-header-icon">
                            <i class="fas fa-plus"></i>
                        </div>
                        <h5 class="modal-title mb-0">Nouveau courrier</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body p-4">
                    <div class="row g-3">

                        {{-- Type et Priorité --}}
                        <div class="col-md-6">
                            <label class="form-label-slim">
                                <i class="fas fa-tag me-1"></i>Type 
                                <span class="text-danger">*</span>
                            </label>
                            <div class="select-wrapper">
                                <select name="type" id="createType" class="input-slim" required>
                                    <option value="">Sélectionner</option>
                                    <option value="0">📥 ARRIVE</option>
                                    <option value="1">📤 DEPART</option>    
                                </select>
                                <i class="fas fa-chevron-down select-icon"></i>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-slim">
                                <i class="fas fa-flag me-1"></i>Priorité 
                                <span class="text-danger">*</span>
                            </label>
                            <div class="select-wrapper">
                                <select name="priorite" class="input-slim" required>
                                    <option value="0">⚪ Normale</option>
                                    <option value="1">🟡 Urgente</option>
                                    <option value="2">🔴 Très urgente</option>
                                </select>
                                <i class="fas fa-chevron-down select-icon"></i>
                            </div>
                        </div>

                        {{-- Référence et Numéro --}}
                        <div class="col-md-6">
                            <label class="form-label-slim">
                                <i class="fas fa-hashtag me-1"></i>Référence
                            </label>
                            <input type="text" name="reference" class="input-slim" placeholder="Ex : REF-2024-001">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-slim">
                                <i class="fas fa-sort-numeric-down me-1"></i>Numéro
                            </label>
                            <input type="text" name="numero" class="input-slim" placeholder="Ex : N°2024-001">
                        </div>

                        {{-- Expéditeur --}}
                        <div class="col-12">
                            <label class="form-label-slim">
                                <i class="fas fa-building me-1"></i>Expéditeur 
                                <span class="text-danger">*</span>
                            </label>
                            <div class="select-wrapper">
                                <select name="organisation_id" id="createOrganisation" class="input-slim select2-organisation" required>
                                    <option value="">Sélectionner une organisation</option>
                                    @foreach($organisations as $org)
                                        <option value="{{ $org->id }}">{{ $org->nom }}</option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down select-icon"></i>
                            </div>
                        </div>
                        
                        <input type="hidden" name="service_id" value="">

                        {{-- Objet --}}
                        <div class="col-12">
                            <label class="form-label-slim">
                                <i class="fas fa-heading me-1"></i>Objet 
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="objet" class="input-slim" placeholder="Saisir l'objet du courrier" required>
                        </div>

                       {{-- Description --}}
                        <div class="col-12">
                            <label class="form-label-slim">
                                <i class="fas fa-heading me-1"></i>Description(reunion,atelier etc..) 
                                <span class="text-danger"></span>
                            </label>
                            <input type="text" name="description" class="input-slim" placeholder="Saisir la description du courrier">
                        </div>

                        {{-- Dates --}}
                        <div class="col-md-6 date-reception-field">
                            <label class="form-label-slim">
                                <i class="fas fa-calendar-alt me-1"></i>Date de réception
                            </label>
                            <input type="date" name="date_reception" class="input-slim" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-6 date-envoi-field" style="display:none;">
                            <label class="form-label-slim">
                                <i class="fas fa-paper-plane me-1"></i>Date d'envoi
                            </label>
                            <input type="date" name="date_envoi" class="input-slim">
                        </div>

                        {{-- Upload fichier --}}
                        <div class="col-12">
                            <label class="form-label-slim">
                                <i class="fas fa-paperclip me-1"></i>Document scanné
                            </label>
                            <div class="dropzone-slim" id="fileUploadZone">
                                <div class="dropzone-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                               <p class="dropzone-title">Glissez-déposez ou cliquez pour choisir</p>
                                <p class="dropzone-hint">PDF, JPG, PNG — Max 5 Mo</p>
                                <input type="file" name="fichier" id="fileInputModern" accept=".pdf,.jpg,.jpeg,.png" style="display:none;">
                            </div>

                            <div class="file-preview-slim d-none" id="fileInfoModern">
                                <div class="file-icon-wrap">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <span class="file-name-slim" id="fileNameModern"></span>
                                <i class="fas fa-times file-clear"></i>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Footer --}}
                <div class="modal-footer border-top px-4 py-3 modal-footer-custom">
                    <button type="button" class="btn-slim btn-slim-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn-slim btn-slim-primary" id="btnSubmitCreate">
                        <span class="spinner-border spinner-border-sm d-none me-1" id="spinnerCreate"></span>
                        <i class="fas fa-save me-1"></i> Créer le courrier
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<style>
/* Animations */
@keyframes slideInUp {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.modal-content-custom {
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    animation: slideInUp 0.3s ease;
}

.modal-header-custom {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
}

.modal-header-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: rgba(255,255,255,0.12);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.modal-header-icon i {
    color: rgba(255,255,255,0.85);
    font-size: 13px;
}

.modal-header-custom .modal-title {
    color: #f8fafc;
    font-size: 15px;
    font-weight: 500;
}

.modal-footer-custom {
    background: #f8fafc;
    border-color: #e2e8f0 !important;
}

/* Form Labels */
.form-label-slim {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    font-weight: 600;
    color: #64748b;
    display: block;
    margin-bottom: 6px;
}

.form-label-slim i {
    font-size: 10px;
}

/* Input Styles */
.input-slim {
    width: 100%;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 9px 12px;
    font-size: 13px;
    color: #1e293b;
    appearance: none;
    transition: all 0.2s ease;
}

.input-slim:hover {
    border-color: #cbd5e1;
    background: #fff;
}

.input-slim:focus {
    outline: none;
    border-color: #185FA5;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(24,95,165,0.08);
}

/* Error States */
.input-slim.is-invalid {
    border-color: #dc3545;
    background-color: #fff8f8;
    animation: shake 0.3s ease;
}

.input-slim.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.08);
}

.field-error {
    font-size: 11px;
    margin-top: 5px;
    display: flex;
    align-items: center;
    gap: 4px;
    animation: slideInUp 0.2s ease;
}

.field-error:before {
    content: "⚠";
    font-size: 10px;
}

/* Select Wrapper */
.select-wrapper {
    position: relative;
}

.select-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 11px;
    color: #94a3b8;
    pointer-events: none;
    transition: transform 0.2s ease;
}

select.input-slim:focus + .select-icon {
    transform: translateY(-50%) rotate(180deg);
}

.select-wrapper .input-slim {
    padding-right: 30px;
    cursor: pointer;
}

/* Dropzone */
.dropzone-slim {
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    padding: 28px 16px;
    text-align: center;
    cursor: pointer;
    background: #f8fafc;
    transition: all 0.3s ease;
}

.dropzone-slim:hover {
    border-color: #185FA5;
    background: #EFF6FF;
    transform: translateY(-2px);
}

.dropzone-slim.is-invalid {
    border-color: #dc3545;
    background-color: #fff8f8;
}

.dropzone-slim.dragover {
    border-color: #185FA5;
    background: #EFF6FF;
    transform: scale(0.98);
}

.dropzone-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #fff;
    border: 1px solid #e2e8f0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
    color: #94a3b8;
    font-size: 20px;
    transition: all 0.3s ease;
}

.dropzone-slim:hover .dropzone-icon {
    color: #185FA5;
    transform: scale(1.05);
}

.dropzone-title {
    font-size: 13px;
    font-weight: 500;
    color: #334155;
    margin: 0 0 4px;
}

.dropzone-hint {
    font-size: 11px;
    color: #94a3b8;
    margin: 0;
}

/* File Preview */
.file-preview-slim {
    margin-top: 12px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 12px;
    display: flex;
    align-items: center;
    gap: 12px;
    animation: slideInUp 0.2s ease;
}

.file-icon-wrap {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: linear-gradient(135deg, #FCEBEB 0%, #FCE4E4 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.file-icon-wrap i {
    color: #A32D2D;
    font-size: 16px;
}

.file-name-slim {
    font-size: 13px;
    font-weight: 500;
    color: #1e293b;
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.file-clear {
    font-size: 12px;
    color: #94a3b8;
    cursor: pointer;
    flex-shrink: 0;
    padding: 6px;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.file-clear:hover {
    color: #E24B4A;
    background: #f1f5f9;
    transform: rotate(90deg);
}

/* Buttons */
.btn-slim {
    font-size: 13px;
    font-weight: 500;
    padding: 8px 20px;
    border-radius: 8px;
    cursor: pointer;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
}

.btn-slim:hover {
    transform: translateY(-1px);
    opacity: 0.9;
}

.btn-slim:active {
    transform: translateY(0);
}

.btn-slim-secondary {
    background: #fff;
    border: 1px solid #cbd5e1;
    color: #64748b;
}

.btn-slim-secondary:hover {
    background: #f8fafc;
    border-color: #94a3b8;
    color: #475569;
}

.btn-slim-primary {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    color: #f8fafc;
}

.btn-slim-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

/* Loading State */
.btn-slim-primary .spinner-border {
    width: 12px;
    height: 12px;
    border-width: 2px;
}
</style>

<script>
// Gestion du Drag & Drop améliorée
document.addEventListener('DOMContentLoaded', function() {
    const dropzone = document.getElementById('fileUploadZone');
    const fileInput = document.getElementById('fileInputModern');
    
    if (dropzone) {
        dropzone.addEventListener('click', () => fileInput.click());
        
        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('dragover');
        });
        
        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('dragover');
        });
        
        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFilePreview(fileInput);
            }
        });
        
        fileInput.addEventListener('change', () => handleFilePreview(fileInput));
    }
});

// Gestion du type de courrier pour afficher/masquer les dates
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('createType');
    const dateReceptionField = document.querySelector('.date-reception-field');
    const dateEnvoiField = document.querySelector('.date-envoi-field');
    
    if (typeSelect) {
        typeSelect.addEventListener('change', function() {
            const type = this.value;
            if (type === '0') { // Entrant
                dateReceptionField.style.display = 'block';
                dateEnvoiField.style.display = 'none';
            } else if (type === '1') { // Sortant
                dateReceptionField.style.display = 'none';
                dateEnvoiField.style.display = 'block';
            } else { // Interne
                dateReceptionField.style.display = 'block';
                dateEnvoiField.style.display = 'block';
            }
        });
    }
});

// Gestion du preview du fichier
function handleFilePreview(input) {
    const fileZone = document.getElementById('fileUploadZone');
    const fileInfo = document.getElementById('fileInfoModern');
    const fileName = document.getElementById('fileNameModern');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        fileName.textContent = file.name;
        
        // Changer l'icône selon le type de fichier
        const fileIcon = fileInfo.querySelector('.file-icon-wrap i');
        if (file.type === 'application/pdf') {
            fileIcon.className = 'fas fa-file-pdf';
        } else if (file.type.startsWith('image/')) {
            fileIcon.className = 'fas fa-file-image';
        } else {
            fileIcon.className = 'fas fa-file';
        }
        
        fileZone.style.display = 'none';
        fileInfo.classList.remove('d-none');
        fileInfo.style.display = 'flex';
        
        // Supprimer l'erreur si elle existe
        const errorDiv = fileZone.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('field-error')) {
            errorDiv.remove();
        }
        fileZone.classList.remove('is-invalid');
    }
}

function clearFilePreview() {
    const fileInput = document.getElementById('fileInputModern');
    const fileZone = document.getElementById('fileUploadZone');
    const fileInfo = document.getElementById('fileInfoModern');
    
    fileInput.value = '';
    fileZone.style.display = 'block';
    fileInfo.classList.add('d-none');
    fileInfo.style.display = 'none';
    
    // Supprimer l'erreur
    const errorDiv = fileZone.nextElementSibling;
    if (errorDiv && errorDiv.classList.contains('field-error')) {
        errorDiv.remove();
    }
    fileZone.classList.remove('is-invalid');
}

// Utilitaires pour les erreurs (à intégrer avec votre fonction initCreateModal)
function clearFieldError($field) {
    const errorDiv = $field[0].nextElementSibling;
    if (errorDiv && errorDiv.classList.contains('field-error')) {
        errorDiv.remove();
    }
    $field.removeClass('is-invalid');
}

function showFieldError($field, message) {
    clearFieldError($field);
    $field.addClass('is-invalid');
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.innerHTML = message;
    $field[0].insertAdjacentElement('afterend', errorDiv);
}
</script>
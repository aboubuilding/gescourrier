<div class="modal fade" id="modalEditOrganisation" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formEditOrganisation" method="POST" action="">
                @csrf 
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-building" style="color: #185FA5;"></i> 
                        Modifier l'organisation
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editOrganisationId">
                    
                    <div class="form-grid-2">
                        {{-- Nom --}}
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-building"></i> Nom de l'organisation 
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nom" id="editNom" class="form-control-modern" required>
                        </div>

                        {{-- Sigle --}}
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-tag"></i> Sigle / Acronyme
                            </label>
                            <input type="text" name="sigle" id="editSigle" class="form-control-modern">
                        </div>

                        {{-- Type d'organisation --}}
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-layer-group"></i> Type d'organisation 
                                <span class="text-danger">*</span>
                            </label>
                            <select name="type" id="editType" class="form-select-modern" required>
                                <option value="">Sélectionner le type</option>
                                <option value="0">🏢 Externe</option>
                                <option value="1">🏛️ Interne</option>
                                <option value="2">🏛️ Gouvernementale</option>
                                <option value="3">🏭 Privée</option>
                                <option value="4">🌍 ONG</option>
                            </select>
                        </div>

                        {{-- Adresse --}}
                        <div class="form-grid-full">
                            <div class="form-group-modern">
                                <label class="form-label-modern">
                                    <i class="fas fa-map-marker-alt"></i> Adresse postale
                                </label>
                                <input type="text" name="adresse" id="editAdresse" class="form-control-modern">
                            </div>
                        </div>

                        {{-- Téléphone --}}
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-phone"></i> Téléphone
                            </label>
                            <input type="tel" name="telephone" id="editTelephone" class="form-control-modern">
                        </div>

                        {{-- Email --}}
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-envelope"></i> Email
                            </label>
                            <input type="email" name="email" id="editEmail" class="form-control-modern">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modern btn-modern-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn-modern btn-modern-primary">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.form-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.form-grid-full {
    grid-column: span 2;
}

.form-group-modern {
    margin-bottom: 0;
}

.form-label-modern {
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    margin-bottom: 0.5rem;
}

.form-label-modern i {
    width: 1rem;
    margin-right: 0.25rem;
    font-size: 0.7rem;
}

.form-control-modern,
.form-select-modern {
    width: 100%;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    background-color: #fff;
    transition: all 0.2s ease;
}

.form-control-modern:focus,
.form-select-modern:focus {
    outline: none;
    border-color: #185FA5;
    box-shadow: 0 0 0 3px rgba(24, 95, 165, 0.1);
}

.form-control-modern:hover,
.form-select-modern:hover {
    border-color: #cbd5e1;
}

.modal-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-bottom: 1px solid #e2e8f0;
}

.modal-header .modal-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e293b;
}

.modal-header .modal-title i {
    margin-right: 0.5rem;
}

.modal-footer {
    background-color: #f8fafc;
    border-top: 1px solid #e2e8f0;
}

.btn-modern {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 0.5rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-modern:hover {
    transform: translateY(-1px);
}

.btn-modern:active {
    transform: translateY(0);
}

.btn-modern-secondary {
    background-color: #fff;
    border: 1px solid #cbd5e1;
    color: #64748b;
}

.btn-modern-secondary:hover {
    background-color: #f8fafc;
    border-color: #94a3b8;
    color: #475569;
}

.btn-modern-primary {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    color: #fff;
}

.btn-modern-primary:hover {
    opacity: 0.9;
}

.text-danger {
    color: #dc3545;
}

/* Responsive */
@media (max-width: 768px) {
    .form-grid-2 {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .form-grid-full {
        grid-column: span 1;
    }
}
</style>


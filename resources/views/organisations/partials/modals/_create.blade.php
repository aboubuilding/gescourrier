{{-- ============================================
     MODAL CRÉER UNE ORGANISATION
     IDs corrigés pour correspondre à create.js
============================================ --}}
<div class="modal fade" id="modalCreateOrganisation" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 modal-content-custom">
            <form id="formCreateOrganisation" action="{{ route('organisations.store') }}" method="POST">
                @csrf

                {{-- Header --}}
                <div class="modal-header border-0 px-4 py-3 modal-header-custom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="modal-header-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <h5 class="modal-title mb-0">Nouvelle organisation</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body p-4">
                    <div class="row g-3">

                        {{-- Nom --}}
                        <div class="col-md-8">
                            <label class="form-label-slim" for="orgNom">
                                <i class="fas fa-building me-1"></i>Nom de l'organisation 
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nom" id="orgNom" class="input-slim" 
                                   placeholder="Ex : Ministère de l'Intérieur" required maxlength="150">
                        </div>

                        {{-- Sigle --}}
                        <div class="col-md-4">
                            <label class="form-label-slim" for="orgSigle">
                                <i class="fas fa-tag me-1"></i>Sigle / Acronyme
                            </label>
                            <input type="text" name="sigle" id="orgSigle" class="input-slim" 
                                   placeholder="Ex : MININT" maxlength="20">
                        </div>

                        {{-- Type d'organisation --}}
                        <div class="col-12">
                            <label class="form-label-slim" for="orgType">
                                <i class="fas fa-layer-group me-1"></i>Type d'organisation 
                                <span class="text-danger">*</span>
                            </label>
                            <div class="select-wrapper">
                                <select name="type" id="orgType" class="input-slim" required>
                                    <option value="">Sélectionner le type</option>
                                    <option value="0">🏢 Externe</option>
                                    <option value="1">🏛️ Interne</option>
                                    <option value="2">🏛️ Gouvernementale</option>
                                    <option value="3">🏭 Privée</option>
                                    <option value="4">🌍 ONG</option>
                                </select>
                                <i class="fas fa-chevron-down select-icon"></i>
                            </div>
                        </div>

                        {{-- Adresse --}}
                        <div class="col-12">
                            <label class="form-label-slim" for="orgAdresse">
                                <i class="fas fa-map-marker-alt me-1"></i>Adresse postale
                            </label>
                            <input type="text" name="adresse" id="orgAdresse" class="input-slim" 
                                   placeholder="Ex : 123 Avenue de la République, Dakar" maxlength="255">
                        </div>

                        {{-- Téléphone --}}
                        <div class="col-md-6">
                            <label class="form-label-slim" for="orgTelephone">
                                <i class="fas fa-phone me-1"></i>Téléphone
                            </label>
                            <input type="tel" name="telephone" id="orgTelephone" class="input-slim" 
                                   placeholder="Ex : +221 33 123 45 67" maxlength="20">
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <label class="form-label-slim" for="orgEmail">
                                <i class="fas fa-envelope me-1"></i>Email
                            </label>
                            <input type="email" name="email" id="orgEmail" class="input-slim" 
                                   placeholder="contact@organisation.com" maxlength="150">
                        </div>

                    </div>
                </div>

                {{-- Footer --}}
                <div class="modal-footer border-top px-4 py-3 modal-footer-custom">
                    <button type="button" class="btn-slim btn-slim-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Annuler
                    </button>
                    {{-- ID du bouton corrigé pour correspondre au JS --}}
                    <button type="submit" class="btn-slim btn-slim-primary" id="btnSubmitCreateOrg">
                        <span class="spinner-border spinner-border-sm d-none me-1" id="spinnerCreateOrg"></span>
                        <i class="fas fa-save me-1"></i> Créer l'organisation
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- Styles (inchangés) --}}
<style>
/* ... (tes styles CSS existants) ... */
@keyframes slideInUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
@keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
.modal-content-custom { border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.15); animation: slideInUp 0.3s ease; }
.modal-header-custom { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); }
.modal-header-icon { width: 32px; height: 32px; border-radius: 8px; background: rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; }
.modal-header-icon i { color: rgba(255,255,255,0.85); font-size: 13px; }
.modal-header-custom .modal-title { color: #f8fafc; font-size: 15px; font-weight: 500; }
.modal-footer-custom { background: #f8fafc; border-color: #e2e8f0 !important; }
.form-label-slim { font-size: 11px; text-transform: uppercase; letter-spacing: 0.06em; font-weight: 600; color: #64748b; display: block; margin-bottom: 6px; }
.form-label-slim i { font-size: 10px; }
.input-slim { width: 100%; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 9px 12px; font-size: 13px; color: #1e293b; appearance: none; transition: all 0.2s ease; }
.input-slim:hover { border-color: #cbd5e1; background: #fff; }
.input-slim:focus { outline: none; border-color: #185FA5; background: #fff; box-shadow: 0 0 0 3px rgba(24,95,165,0.08); }
.input-slim.is-invalid { border-color: #dc3545; background-color: #fff8f8; animation: shake 0.3s ease; }
.input-slim.is-invalid:focus { border-color: #dc3545; box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.08); }
.field-error { font-size: 11px; margin-top: 5px; display: flex; align-items: center; gap: 4px; animation: slideInUp 0.2s ease; color: #dc3545; }
.field-error:before { content: "⚠"; font-size: 10px; }
.select-wrapper { position: relative; }
.select-icon { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); font-size: 11px; color: #94a3b8; pointer-events: none; transition: transform 0.2s ease; }
select.input-slim:focus + .select-icon { transform: translateY(-50%) rotate(180deg); }
.select-wrapper .input-slim { padding-right: 30px; cursor: pointer; }
.btn-slim { font-size: 13px; font-weight: 500; padding: 8px 20px; border-radius: 8px; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s ease; }
.btn-slim:hover { transform: translateY(-1px); opacity: 0.9; }
.btn-slim:active { transform: translateY(0); }
.btn-slim-secondary { background: #fff; border: 1px solid #cbd5e1; color: #64748b; }
.btn-slim-secondary:hover { background: #f8fafc; border-color: #94a3b8; color: #475569; }
.btn-slim-primary { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #f8fafc; }
.btn-slim-primary:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
</style>

<script>
// UI uniquement : Formatage du sigle en majuscules
document.addEventListener('DOMContentLoaded', function() {
    const sigleInput = document.getElementById('orgSigle');
    if (sigleInput) {
        sigleInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
    
    // Reset visuel à la fermeture
    $('#modalCreateOrganisation').on('hidden.bs.modal', function() {
        $('#formCreateOrganisation')[0]?.reset();
        $('#formCreateOrganisation .is-invalid').removeClass('is-invalid');
        $('#formCreateOrganisation .field-error').remove();
    });
});
</script>
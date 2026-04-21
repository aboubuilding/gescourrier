{{-- ============================================
     MODAL CRÉATION
============================================ --}}
<div class="modal fade" id="modalCreate" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formCreate" action="{{ route('organisations.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle text-success"></i> Nouvelle organisation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nom *</label>
                            <input type="text" name="nom" class="form-control" required placeholder="Nom complet">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sigle</label>
                            <input type="text" name="sigle" class="form-control" placeholder="Ex: DGA" maxlength="20">
                            <div class="form-text">Acronyme officiel</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type *</label>
                            <select name="type" class="form-select" required>
                                <option value="">Sélectionner</option>
                                <option value="1">Interne</option>
                                <option value="0">Externe</option>
                                <option value="2">Gouvernementale</option>
                                <option value="3">Privée</option>
                                <option value="4">ONG</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="contact@exemple.tg">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" name="telephone" class="form-control" placeholder="+228 XX XX XX XX">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Adresse</label>
                            <input type="text" name="adresse" class="form-control" placeholder="Adresse postale">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success" id="btnSubmitCreate">
                        <span class="spinner-border spinner-border-sm d-none" id="spinnerCreate"></span>
                        Créer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form id="formEdit" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-pen" style="color: #f59e0b;"></i> Modifier le courrier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editId">
                    <div class="form-grid-2">
                        <div class="form-group-modern">
                            <label class="form-label-modern"><i class="fas fa-tag"></i> Type</label>
                            <select name="type" id="editType" class="form-select-modern" required>
                                <option value="0">📥 Entrant</option>
                                <option value="1">📤 Sortant</option>
                                <option value="2">🔄 Interne</option>
                            </select>
                        </div>
                        <div class="form-group-modern">
                            <label class="form-label-modern"><i class="fas fa-flag"></i> Priorité</label>
                            <select name="priorite" id="editPriorite" class="form-select-modern" required>
                                <option value="0">🟢 Normale</option>
                                <option value="1">🟠 Urgente</option>
                                <option value="2">🔴 Très urgente</option>
                            </select>
                        </div>
                        <div class="form-group-modern">
                            <label class="form-label-modern"><i class="fas fa-barcode"></i> Référence</label>
                            <input type="text" name="reference" id="editReference" class="form-control-modern">
                        </div>
                        <div class="form-group-modern">
                            <label class="form-label-modern"><i class="fas fa-hashtag"></i> Numéro</label>
                            <input type="text" name="numero" id="editNumero" class="form-control-modern">
                        </div>
                        <div class="form-group-modern">
                            <label class="form-label-modern"><i class="fas fa-building"></i> Organisation</label>
                            <select name="organisation_id" id="editOrganisation" class="form-select-modern select2-organisation">
                                <option value="">Sélectionner une organisation</option>
                                @foreach($organisations as $org)
                                    <option value="{{ $org->id }}">{{ $org->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group-modern" style="display: none;"><input type="hidden" name="service_id" id="editService" value=""></div>
                        
                        <div class="form-grid-full">
                            <div class="form-group-modern">
                                <label class="form-label-modern"><i class="fas fa-heading"></i> Objet</label>
                                <input type="text" name="objet" id="editObjet" class="form-control-modern" required>
                            </div>
                        </div>

                        <div class="form-group-modern date-field edit-date-reception-field">
                            <label class="form-label-modern"><i class="fas fa-calendar-alt"></i> Date réception</label>
                            <input type="date" name="date_reception" id="editDateReception" class="form-control-modern">
                        </div>
                        <div class="form-group-modern date-field edit-date-envoi-field" style="display: none;">
                            <label class="form-label-modern"><i class="fas fa-paper-plane"></i> Date envoi</label>
                            <input type="date" name="date_envoi" id="editDateEnvoi" class="form-control-modern">
                        </div>

                        <div class="form-grid-full">
                            <div class="form-group-modern">
                                <label class="form-label-modern"><i class="fas fa-paperclip"></i> Document actuel</label>
                                <div id="editFileCurrent" class="file-info-modern" style="display: flex; background: #f1f5f9;">
                                    <i class="fas fa-file-pdf"></i><span>Aucun fichier</span>
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
                    <button type="button" class="btn-modern btn-modern-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Annuler</button>
                    <button type="submit" class="btn-modern btn-modern-warning" id="btnSubmitEdit">
                        <span class="spinner-border spinner-border-sm d-none" id="spinnerEdit"></span><i class="fas fa-save"></i> Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
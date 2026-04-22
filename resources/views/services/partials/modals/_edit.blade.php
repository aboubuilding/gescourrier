{{-- ============================================
     MODAL EDIT SERVICE
============================================ --}}
<div class="modal fade" id="modalEditService" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form id="formEditService" method="POST" action="">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-layer-group" style="color:#10b981;"></i>
                        Modifier le service
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    {{-- ID caché --}}
                    <input type="hidden" name="id" id="editServiceId">

                    <div class="form-grid-2">

                        {{-- Nom --}}
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-layer-group"></i> Nom du service
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="nom"
                                   id="editNom"
                                   class="form-control-modern"
                                   required>
                        </div>

                        {{-- Organisation --}}
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-building"></i> Organisation
                            </label>
                            <select name="organisation_id"
                                    id="editOrganisationId"
                                    class="form-select-modern">
                                <option value="">-- Aucune --</option>

                                @foreach($organisations as $org)
                                    <option value="{{ $org->id }}">
                                        {{ $org->nom }}
                                        @if(!empty($org->sigle)) ({{ $org->sigle }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Téléphone --}}
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-phone"></i> Téléphone
                            </label>
                            <input type="text"
                                   name="telephone"
                                   id="editTelephone"
                                   class="form-control-modern"
                                   placeholder="+228...">
                        </div>

                        {{-- Email --}}
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-envelope"></i> Email
                            </label>
                            <input type="email"
                                   name="email"
                                   id="editEmail"
                                   class="form-control-modern">
                        </div>

                        {{-- Localisation --}}
                        <div class="form-grid-full">
                            <div class="form-group-modern">
                                <label class="form-label-modern">
                                    <i class="fas fa-map-marker-alt"></i> Localisation
                                </label>
                                <input type="text"
                                       name="localisation"
                                       id="editLocalisation"
                                       class="form-control-modern"
                                       placeholder="Bâtiment, étage, bureau...">
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="form-grid-full">
                            <div class="form-group-modern">
                                <label class="form-label-modern">
                                    <i class="fas fa-align-left"></i> Description
                                </label>
                                <textarea name="description"
                                          id="editDescription"
                                          class="form-control-modern"
                                          rows="3"></textarea>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn-modern btn-modern-secondary"
                            data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Annuler
                    </button>

                    <button type="submit"
                            class="btn-modern btn-modern-primary">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
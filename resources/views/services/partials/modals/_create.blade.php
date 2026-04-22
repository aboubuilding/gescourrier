{{-- ============================================
     MODAL CRÉER UN SERVICE
============================================ --}}
<div class="modal fade" id="modalCreateService" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 modal-content-custom">

            <form id="formCreateService"
                  action="{{ route('services.store') }}"
                  method="POST">
                @csrf

                {{-- HEADER --}}
                <div class="modal-header border-0 px-4 py-3 modal-header-custom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="modal-header-icon">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <h5 class="modal-title mb-0">Nouveau service</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                {{-- BODY --}}
                <div class="modal-body p-4">
                    <div class="row g-3">

                        {{-- NOM --}}
                        <div class="col-md-8">
                            <label class="form-label-slim">
                                <i class="fas fa-layer-group me-1"></i>Nom du service <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="nom"
                                   id="serviceNom"
                                   class="input-slim"
                                   placeholder="Ex : Service Informatique"
                                   required maxlength="150">
                        </div>

                        {{-- ORGANISATION --}}
                        <div class="col-md-4">
                            <label class="form-label-slim">
                                <i class="fas fa-building me-1"></i>Organisation
                            </label>
                            <div class="select-wrapper">
                                <select name="organisation_id"
                                        id="serviceOrganisation"
                                        class="input-slim">
                                    <option value="">-- Facultatif --</option>
                                    @foreach($organisations as $org)
                                        <option value="{{ $org->id }}">
                                            {{ $org->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down select-icon"></i>
                            </div>
                        </div>

                        {{-- DESCRIPTION --}}
                        <div class="col-12">
                            <label class="form-label-slim">
                                <i class="fas fa-align-left me-1"></i>Description
                            </label>
                            <textarea name="description"
                                      id="serviceDescription"
                                      class="input-slim"
                                      rows="3"
                                      placeholder="Description du service..."></textarea>
                        </div>

                        {{-- LOCALISATION --}}
                        <div class="col-md-6">
                            <label class="form-label-slim">
                                <i class="fas fa-map-marker-alt me-1"></i>Localisation
                            </label>
                            <input type="text"
                                   name="localisation"
                                   class="input-slim"
                                   placeholder="Bâtiment, étage, bureau">
                        </div>

                        {{-- TELEPHONE --}}
                        <div class="col-md-6">
                            <label class="form-label-slim">
                                <i class="fas fa-phone me-1"></i>Téléphone
                            </label>
                            <input type="tel"
                                   name="telephone"
                                   class="input-slim"
                                   placeholder="+228 XX XX XX XX">
                        </div>

                        {{-- EMAIL --}}
                        <div class="col-12">
                            <label class="form-label-slim">
                                <i class="fas fa-envelope me-1"></i>Email
                            </label>
                            <input type="email"
                                   name="email"
                                   class="input-slim"
                                   placeholder="service@organisation.tg">
                        </div>

                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="modal-footer border-top px-4 py-3 modal-footer-custom">
                    <button type="button"
                            class="btn-slim btn-slim-secondary"
                            data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Annuler
                    </button>

                    <button type="submit"
                            class="btn-slim btn-slim-primary"
                            id="btnSubmitCreateService">
                        <span class="spinner-border spinner-border-sm d-none me-1"
                              id="spinnerCreateService"></span>
                        <i class="fas fa-save me-1"></i> Créer le service
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalAffecter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formAffecter" method="POST">
                @csrf
                <input type="hidden" name="courrier_id" id="affecterCourrierId">
                {{-- Champ service_id caché qui sera rempli automatiquement --}}
                <input type="hidden" name="service_id" id="affecterServiceId"> 
                
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
                        <select name="agent_id" id="affecterAgent" class="form-select-modern select2-agent" required>
                            <option value="">Sélectionner un agent</option>
                            @foreach($agents as $agent)
                                {{-- On ajoute data-service-id pour le JS --}}
                                <option value="{{ $agent->id }}" 
        data-service-id="{{ $agent->service?->id ?? '' }}"
        data-service-name="{{ $agent->service?->nom ?? '' }}">
    {{ $agent->nom_complet ?? $agent->prenom . ' ' . $agent->nom }} 
    {{ $agent->service ? '- ' . $agent->service->nom : '(Aucun service)' }}
</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- AFFICHAGE DYNAMIQUE DU SERVICE (Lecture seule) --}}
                    <div class="form-group-modern" id="serviceDisplayGroup" style="display: none;">
                        <label class="form-label-modern text-muted">
                            <i class="fas fa-building"></i> Service assigné
                        </label>
                        <div class="p-2 bg-light rounded border text-dark fw-semibold d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span id="affecterServiceName">—</span>
                        </div>
                        <small class="text-muted mt-1 d-block">
                            <i class="fas fa-info-circle"></i> Service déterminé automatiquement selon l'agent.
                        </small>
                    </div>

                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <i class="fas fa-sticky-note"></i> Note d'affectation
                        </label>
                        <textarea name="note" class="form-control-modern" rows="3" placeholder="Instructions ou remarques..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modern btn-modern-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn-modern btn-modern-primary" id="btnSubmitAffecter">
                        <span class="spinner-border spinner-border-sm d-none" id="spinnerAffecter"></span>
                        <i class="fas fa-share-alt"></i> Affecter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
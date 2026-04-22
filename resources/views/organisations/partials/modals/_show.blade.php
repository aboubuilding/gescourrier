{{-- ============================================
     MODAL VOIR L'ORGANISATION (READ-ONLY)
     Modal unique - Rempli dynamiquement par show.js
============================================ --}}
<div class="modal fade" id="modalShowOrganisation" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 20px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">

            {{-- Header --}}
            <div class="modal-header border-0 px-4 py-3" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center justify-content-center rounded-2" style="width:36px; height:36px; background: rgba(255,255,255,0.12);">
                        <i class="fas fa-building" style="color: rgba(255,255,255,0.85); font-size: 16px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" style="color: #f8fafc; font-size: 16px; font-weight: 600;">Détails de l'organisation</h5>
                        <p class="mb-0 mt-1" style="color: rgba(255,255,255,0.6); font-size: 12px;" id="showOrganisationSigle">—</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">

                {{-- Loading --}}
                <div id="showOrganisationLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <p class="mt-2 text-muted">Chargement des détails...</p>
                </div>

                {{-- Contenu principal (caché initialement) --}}
                <div id="showOrganisationContent" style="display: none;">

                    {{-- En-tête avec nom et type --}}
                    <div class="d-flex justify-content-between align-items-start pb-3 mb-4 border-bottom">
                        <div>
                            <h3 class="mb-1 fw-bold" id="showOrganisationNom" style="color: #1e293b; font-size: 24px;">—</h3>
                            <div class="d-flex gap-2 mt-2">
                                <span class="badge rounded-pill px-3 py-1" id="showOrganisationTypeBadge" style="font-size: 11px; font-weight: 500;">—</span>
                                @if(isset($organisation) && $organisation->sigle)
                                <span class="badge rounded-pill px-3 py-1 bg-light text-dark border" id="showOrganisationSigleBadge" style="font-size: 11px; font-weight: 500;">—</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-end">
                            <i class="fas fa-calendar-alt text-muted" style="font-size: 12px;"></i>
                            <small class="text-muted d-block mt-1" id="showOrganisationCreatedAt">Créée le —</small>
                        </div>
                    </div>

                    {{-- Grille infos organisation --}}
                    <div class="row g-4 mb-4">
                        {{-- Colonne gauche --}}
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm rounded-3 h-100" style="background: #f8fafc;">
                                <div class="card-body p-3">
                                    <p class="text-muted mb-3" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.06em; font-weight: 600;">
                                        <i class="fas fa-info-circle me-1"></i> Informations générales
                                    </p>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted d-block" style="font-size: 11px;">Nom complet</small>
                                        <span class="fw-500" id="showOrganisationNomFull" style="font-size: 14px; color: #1e293b;">—</span>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted d-block" style="font-size: 11px;">Sigle / Acronyme</small>
                                        <span class="fw-500" id="showOrganisationSigleFull" style="font-size: 14px; color: #1e293b;">—</span>
                                    </div>
                                    
                                    <div>
                                        <small class="text-muted d-block" style="font-size: 11px;">Type d'organisation</small>
                                        <span class="fw-500" id="showOrganisationTypeFull" style="font-size: 14px; color: #1e293b;">—</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Colonne droite --}}
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm rounded-3 h-100" style="background: #f8fafc;">
                                <div class="card-body p-3">
                                    <p class="text-muted mb-3" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.06em; font-weight: 600;">
                                        <i class="fas fa-address-card me-1"></i> Coordonnées
                                    </p>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted d-block" style="font-size: 11px;">
                                            <i class="fas fa-map-marker-alt me-1" style="font-size: 10px;"></i> Adresse
                                        </small>
                                        <span class="fw-500" id="showOrganisationAdresse" style="font-size: 14px; color: #1e293b;">—</span>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted d-block" style="font-size: 11px;">
                                            <i class="fas fa-phone me-1" style="font-size: 10px;"></i> Téléphone
                                        </small>
                                        <span class="fw-500" id="showOrganisationTelephone" style="font-size: 14px; color: #1e293b;">—</span>
                                    </div>
                                    
                                    <div>
                                        <small class="text-muted d-block" style="font-size: 11px;">
                                            <i class="fas fa-envelope me-1" style="font-size: 10px;"></i> Email
                                        </small>
                                        <span class="fw-500" id="showOrganisationEmail" style="font-size: 14px; color: #1e293b;">—</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section Courriers associés --}}
                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 fw-bold" style="color: #1e293b; font-size: 14px;">
                                <i class="fas fa-envelope me-2" style="color: #185FA5;"></i>
                                Courriers associés
                                <span class="badge bg-secondary rounded-pill ms-2" id="courriersCount">0</span>
                            </h6>
                        </div>

                        {{-- Liste des courriers --}}
                        <div class="border rounded-3" style="background: #fff; max-height: 400px; overflow-y: auto;">
                            <div id="courriersList" class="list-group list-group-flush">
                                <!-- Les courriers seront injectés ici -->
                            </div>
                            <div id="noCourriersMessage" class="text-center py-5 d-none">
                                <i class="fas fa-inbox fa-3x text-muted mb-3 opacity-25"></i>
                                <p class="text-muted mb-0">Aucun courrier associé à cette organisation</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Footer --}}
            <div class="modal-footer border-top px-4 py-3" style="background: #f8fafc; border-color: #e2e8f0 !important;">
                <button type="button" class="btn-slim btn-slim-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Fermer
                </button>
            </div>

        </div>
    </div>
</div>

<style>
.fw-500 { font-weight: 500; }

/* Animation d'entrée */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-content {
    animation: fadeInUp 0.3s ease;
}

/* Styles pour les courriers */
.courrier-item {
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
    cursor: pointer;
}

.courrier-item:hover {
    background-color: #f8fafc;
    transform: translateX(4px);
}

.courrier-item.entrant {
    border-left-color: #10b981;
}

.courrier-item.sortant {
    border-left-color: #f59e0b;
}

.courrier-item.interne {
    border-left-color: #3b82f6;
}

.priorite-badge {
    font-size: 10px;
    padding: 2px 8px;
    border-radius: 20px;
    display: inline-block;
}

.priorite-0 { background: #e2e8f0; color: #64748b; }
.priorite-1 { background: #fed7aa; color: #9a3412; }
.priorite-2 { background: #fecaca; color: #991b1b; }

.type-badge {
    font-size: 10px;
    padding: 2px 8px;
    border-radius: 20px;
    display: inline-block;
}

.type-entrant { background: #d1fae5; color: #065f46; }
.type-sortant { background: #fed7aa; color: #92400e; }
.type-interne { background: #dbeafe; color: #1e40af; }

/* Scrollbar personnalisée */
.border.rounded-3::-webkit-scrollbar {
    width: 6px;
}

.border.rounded-3::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.border.rounded-3::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.border.rounded-3::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Card styles */
.card {
    transition: all 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08) !important;
}

/* Button styles */
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

/* Badge bg-light */
.badge.bg-light {
    background-color: #f1f5f9 !important;
}

/* Text colors */
.text-muted {
    color: #64748b !important;
}

.extra-small {
    font-size: 0.65rem;
}
</style>
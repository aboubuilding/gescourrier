{{-- ============================================
     MODAL VOIR SERVICE (READ-ONLY)
     Rempli dynamiquement par show.js
============================================ --}}
<div class="modal fade" id="modalShowService" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0"
             style="border-radius: 20px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">

            {{-- HEADER --}}
            <div class="modal-header border-0 px-4 py-3"
                 style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">

                <div class="d-flex align-items-center gap-2">

                    <div class="d-flex align-items-center justify-content-center rounded-2"
                         style="width:36px;height:36px;background:rgba(255,255,255,0.12);">
                        <i class="fas fa-layer-group"
                           style="color:rgba(255,255,255,0.85);font-size:16px;"></i>
                    </div>

                    <div>
                        <h5 class="modal-title mb-0"
                            style="color:#f8fafc;font-size:16px;font-weight:600;">
                            Détails du service
                        </h5>

                        <p class="mb-0 mt-1"
                           style="color:rgba(255,255,255,0.7);font-size:12px;"
                           id="showServiceNom">
                            —
                        </p>
                    </div>

                </div>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>

            {{-- BODY --}}
            <div class="modal-body p-4">

                {{-- LOADING --}}
                <div id="showServiceLoading" class="text-center py-5">
                    <div class="spinner-border text-success"></div>
                    <p class="mt-2 text-muted">Chargement...</p>
                </div>

                {{-- CONTENT --}}
                <div id="showServiceContent" style="display:none;">

                    {{-- HEADER INFO --}}
                    <div class="d-flex justify-content-between align-items-start pb-3 mb-4 border-bottom">

                        <div>
                            <h3 class="fw-bold mb-1" id="showServiceNomFull"
                                style="color:#1e293b;font-size:24px;">
                                —
                            </h3>

                            <div class="d-flex gap-2 mt-2">

                                <span class="badge rounded-pill px-3 py-1 bg-success"
                                      id="showServiceAgentsBadge">
                                    —
                                </span>

                                <span class="badge rounded-pill px-3 py-1 bg-primary"
                                      id="showServiceCourriersBadge">
                                    —
                                </span>

                            </div>
                        </div>

                        <div class="text-end">
                            <i class="fas fa-calendar-alt text-muted"></i>
                            <small class="d-block mt-1 text-muted"
                                   id="showServiceCreatedAt">
                                —
                            </small>
                        </div>

                    </div>

                    {{-- GRID INFOS --}}
                    <div class="row g-4">

                        {{-- LEFT --}}
                        <div class="col-md-6">

                            <div class="card border-0 shadow-sm rounded-3 h-100"
                                 style="background:#f8fafc;">

                                <div class="card-body p-3">

                                    <p class="text-muted mb-3"
                                       style="font-size:11px;text-transform:uppercase;font-weight:600;">
                                        <i class="fas fa-info-circle me-1"></i> Informations
                                    </p>

                                    <div class="mb-3">
                                        <small class="text-muted d-block">Nom</small>
                                        <span id="showServiceNomInfo"
                                              class="fw-semibold text-dark">—</span>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted d-block">Organisation</small>
                                        <span id="showServiceOrganisation"
                                              class="fw-semibold text-dark">—</span>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted d-block">Email</small>
                                        <span id="showServiceEmail"
                                              class="fw-semibold text-dark">—</span>
                                    </div>

                                    <div>
                                        <small class="text-muted d-block">Téléphone</small>
                                        <span id="showServiceTelephone"
                                              class="fw-semibold text-dark">—</span>
                                    </div>

                                </div>

                            </div>

                        </div>

                        {{-- RIGHT --}}
                        <div class="col-md-6">

                            <div class="card border-0 shadow-sm rounded-3 h-100"
                                 style="background:#f8fafc;">

                                <div class="card-body p-3">

                                    <p class="text-muted mb-3"
                                       style="font-size:11px;text-transform:uppercase;font-weight:600;">
                                        <i class="fas fa-chart-line me-1"></i> Activité
                                    </p>

                                    <div class="mb-3">
                                        <small class="text-muted d-block">Total agents</small>
                                        <span id="showTotalAgents"
                                              class="fw-bold text-success">0</span>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted d-block">Total courriers</small>
                                        <span id="showTotalCourriers"
                                              class="fw-bold text-primary">0</span>
                                    </div>

                                    <div>
                                        <small class="text-muted d-block">Top agent</small>
                                        <span id="showTopAgent"
                                              class="fw-semibold text-dark">—</span>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- LIST AGENTS --}}
                    <div class="mt-4">

                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-users me-2 text-success"></i>
                            Agents du service
                        </h6>

                        <div class="border rounded-3 p-3 bg-white"
                             style="max-height:300px;overflow-y:auto;">

                            <div id="agentsList"></div>

                            <div id="noAgents" class="text-center text-muted d-none">
                                Aucun agent
                            </div>

                        </div>

                    </div>

                </div>
            </div>

            {{-- FOOTER --}}
            <div class="modal-footer border-top px-4 py-3"
                 style="background:#f8fafc;">
                <button class="btn btn-light"
                        data-bs-dismiss="modal">
                    Fermer
                </button>
            </div>

        </div>
    </div>
</div>
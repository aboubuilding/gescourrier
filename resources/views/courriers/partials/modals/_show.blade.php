{{-- ============================================
     MODAL VOIR LE DOSSIER (READ-ONLY)
============================================ --}}
<div class="modal fade" id="modalShow" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">

            {{-- Header sombre et sobre --}}
            <div class="modal-header border-0 px-4 py-3" style="background: #1e293b;">
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center justify-content-center rounded-2" style="width:32px; height:32px; background: rgba(255,255,255,0.1);">
                        <i class="fas fa-folder-open" style="color: rgba(255,255,255,0.8); font-size: 14px;"></i>
                    </div>
                    <h5 class="modal-title mb-0 fw-500" style="color: #f8fafc; font-size: 15px;">Détails du courrier</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-0">
                {{-- Loading --}}
                <div id="showLoading" class="text-center py-5 d-none">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted small">Chargement des détails...</p>
                </div>

                {{-- Contenu principal --}}
                <div id="showContent" class="p-4">

                    {{-- En-tête résumé --}}
                    <div class="d-flex justify-content-between align-items-start pb-3 mb-3 border-bottom">
                        <div>
                            <p class="fw-500 mb-1" id="showReference" style="font-size: 20px;">REF-XXXX</p>
                            <span class="badge rounded-pill border" id="showNumero"
                                  style="font-size: 11px; font-weight: 500; background: #f8fafc; color: #64748b; border-color: #e2e8f0 !important;">
                                N° —
                            </span>
                        </div>
                        <div class="text-end">
                            <span class="badge rounded-pill px-3 py-2 d-block mb-1" id="showStatutBadge" style="font-size: 11px; font-weight: 500;">Statut</span>
                            <small class="text-muted" id="showDate">Le --/--/----</small>
                        </div>
                    </div>

                    {{-- Grille infos --}}
                    <div class="row g-3">

                        {{-- Colonne gauche --}}
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label class="text-muted mb-1 d-block" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.06em; font-weight: 500;">Objet</label>
                                <p class="mb-0 fw-500" id="showObjet" style="font-size: 15px;">—</p>
                            </div>

                            <div class="mb-3">
                                <label class="text-muted mb-1 d-block" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.06em; font-weight: 500;">Description</label>
                                <div id="showDescription" class="rounded-3 p-3"
                                     style="background: #f8fafc; font-size: 13px; color: #64748b; min-height: 80px; line-height: 1.6; border: 0.5px solid #e2e8f0;">
                                    Aucune description détaillée.
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="text-muted mb-1 d-block" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.06em; font-weight: 500;">Priorité</label>
                                    <div id="showPriorite">—</div>
                                </div>
                                <div class="col-6">
                                    <label class="text-muted mb-1 d-block" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.06em; font-weight: 500;">Type</label>
                                    <div id="showType">—</div>
                                </div>
                            </div>
                        </div>

                        {{-- Colonne droite --}}
                        <div class="col-md-5">
                            {{-- Acteurs --}}
                            <div class="rounded-3 p-3 mb-2" style="background: #f8fafc; border: 0.5px solid #e2e8f0;">
                                <p class="text-muted mb-3" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.06em; font-weight: 500;">
                                    <i class="fas fa-users me-1" style="font-size: 12px;"></i> Acteurs
                                </p>
                                <div class="mb-2">
                                    <small class="text-muted d-block" style="font-size: 11px;">Organisation</small>
                                    <span class="fw-500 text-dark" id="showOrganisation" style="font-size: 13px;">—</span>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted d-block" style="font-size: 11px;">Service destinataire</small>
                                    <span class="fw-500 text-dark" id="showService" style="font-size: 13px;">—</span>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px;">Agent affecté</small>
                                    <div class="d-flex align-items-center gap-2 mt-1" id="showAgentWrapper">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                                             style="width:26px; height:26px; background:#EEEDFE; font-size: 10px; font-weight: 500; color: #534AB7; flex-shrink: 0;">
                                            NA
                                        </div>
                                        <span class="fw-500" id="showAgent" style="font-size: 13px;">Non affecté</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Pièce jointe --}}
                            <div class="rounded-3 p-3" style="background: #f8fafc; border: 0.5px solid #e2e8f0;">
                                <p class="text-muted mb-3" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.06em; font-weight: 500;">
                                    <i class="fas fa-paperclip me-1" style="font-size: 12px;"></i> Pièce jointe
                                </p>
                                <div id="showFileContainer">
                                    <a href="#" target="_blank" id="showFileLink"
                                       class="d-flex align-items-center gap-2 p-2 rounded-2 text-decoration-none file-link"
                                       style="background: #fff; border: 0.5px solid #e2e8f0;">
                                        <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                             style="width: 34px; height: 34px; background: #FCEBEB;">
                                            <i class="fas fa-file-pdf" style="color: #A32D2D; font-size: 14px;"></i>
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <div class="text-truncate fw-500 text-dark" id="showFileName" style="font-size: 13px;">document.pdf</div>
                                            <div class="text-muted" id="showFileSize" style="font-size: 11px;">1.2 Mo</div>
                                        </div>
                                        <i class="fas fa-download text-muted" style="font-size: 12px;"></i>
                                    </a>
                                </div>
                                <div id="showNoFile" class="text-center text-muted py-2 d-none">
                                    <i class="fas fa-ban fa-lg mb-1 opacity-25 d-block"></i>
                                    <p class="mb-0" style="font-size: 12px;">Aucun fichier joint</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="modal-footer border-top px-4 py-3" style="background: #f8fafc; border-color: #e2e8f0 !important;">
                <button type="button" class="btn btn-sm btn-light text-muted px-3" data-bs-dismiss="modal"
                        style="font-size: 13px; border: 0.5px solid #e2e8f0; border-radius: 8px;">
                    Fermer
                </button>
                <button type="button" class="btn btn-sm px-3" id="btnPrintShow"
                        style="font-size: 13px; background: #E6F1FB; color: #185FA5; border: 0.5px solid #185FA5; border-radius: 8px;">
                    <i class="fas fa-print me-1" style="font-size: 12px;"></i> Imprimer
                </button>
            </div>

        </div>
    </div>
</div>

<style>
    .file-link { transition: box-shadow 0.15s ease; }
    .file-link:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .fw-500 { font-weight: 500; }
</style>
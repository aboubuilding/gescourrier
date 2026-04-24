<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
        <div>
            <h5 class="mb-0 text-primary fw-bold">
                <i class="fas fa-envelope-shield me-2"></i>Gestion du Courrier
            </h5>
            <small class="text-muted">
                {{ count($courriers) }} dossier(s) affiché(s)
            </small>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="courriersTable">
            <thead class="bg-light">
                <tr>
                    <th class="border-0 ps-3">Identification</th>
                    <th class="border-0">Objet & Expediteur</th>
                    <th class="border-0">Classification</th>
                    <th class="border-0">Description</th>
                    <th class="border-0">Assignation</th>
                    <th class="border-0">Pièce Jointe</th>
                    <th class="border-0 text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courriers as $c)
                @php
                    // États bloquants : 1=Affecté, 2=Traité, 3=Archivé
                    $isLocked = in_array($c['statut']['code'], [1, 2, 3]); 
                    $hasFile = !empty($c['fichier']);
                    
                    // Configuration des types de courrier
                    $typeConfig = [
                        '0' => ['label' => 'ARRIVE', 'icon' => 'fa-arrow-down', 'color' => '#3b82f6'],
                        '1' => ['label' => 'DEPART', 'icon' => 'fa-arrow-up', 'color' => '#10b981'],
                        '2' => ['label' => 'Interne', 'icon' => 'fa-arrow-right-left', 'color' => '#f59e0b'],
                    ];
                    $typeCode = $c['type']['code'] ?? $c['type'] ?? '0';
                    $type = $typeConfig[$typeCode] ?? $typeConfig['0'];
                    
                    // Configuration des statuts pour les couleurs
                    $statusColors = [
                        0 => '#64748b', // Non affecté
                        1 => '#3b82f6', // Affecté
                        2 => '#10b981', // Traité
                        3 => '#ef4444', // Archivé
                    ];
                    $statusCode = $c['statut']['code'] ?? 0;
                    $statusColor = $statusColors[$statusCode] ?? '#64748b';
                @endphp
                <tr>
                    {{-- COLONNE 1: IDENTIFICATION + TYPE BADGE --}}
                    <td class="ps-3">
                        <div class="d-flex align-items-center">
                            {{-- Badge Type élégant --}}
                            <div class="me-3">
                                <span class="d-inline-flex align-items-center gap-1 px-2.5 py-1 rounded-pill fw-semibold type-badge" 
                                      style="background: linear-gradient(135deg, {{ $type['color'] }}20 0%, {{ $type['color'] }}05 100%); 
                                             color: {{ $type['color'] }}; 
                                             font-size: 0.75rem; 
                                             border: 1px solid {{ $type['color'] }}40;
                                             box-shadow: 0 2px 4px {{ $type['color'] }}10;">
                                    <i class="fas {{ $type['icon'] }}" style="font-size: 0.7rem; opacity: 0.9;"></i>
                                    {{ $type['label'] }}
                                </span>
                            </div>
                            
                            {{-- Infos principales --}}
                            <div>
                                <div class="fw-bold text-dark">{{ $c['reference'] }}</div>
                                <div class="small text-muted">№ {{ $c['numero'] ?? '—' }}</div>
                                <div class="extra-small text-secondary mt-1">
                                    <i class="far fa-calendar-alt"></i> {{ $c['dates']['reception'] ?? $c['dates']['envoi'] ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- COLONNE 2: OBJET & ORGANISATION --}}
                    <td>
                        <div class="text-truncate fw-semibold" style="max-width: 250px;" title="{{ $c['objet'] }}">
                            {{ $c['objet'] }}
                        </div>
                        <div class="small text-primary mt-1">
                            <i class="fas fa-building me-1"></i>
                            {{ $c['acteurs']['organisation']['sigle'] ?? $c['acteurs']['organisation']['nom'] ?? 'Sans organisation' }}
                        </div>
                    </td>

                    {{-- COLONNE 3: CLASSIFICATION (Statut + Priorité) --}}
                    <td>
                        <div class="mb-1">
                            <span class="badge rounded-pill px-3 py-1 fw-semibold" 
                                  style="background: {{ $statusColor }}15; color: {{ $statusColor }}; border: 1px solid {{ $statusColor }}40; font-size: 0.75rem;">
                                {{ $c['statut']['libelle'] }}
                            </span>
                        </div>
                        <div class="extra-small fw-bold text-uppercase">
                            @php
                                $prioColor = $c['priorite']['libelle'] == 'Très urgente' ? '#ef4444' : 
                                            ($c['priorite']['libelle'] == 'Urgente' ? '#f59e0b' : '#10b981');
                            @endphp
                            <i class="fas fa-circle me-1" style="color: {{ $prioColor }}; font-size: 0.5rem;"></i>
                            <span style="color: {{ $prioColor }};">{{ $c['priorite']['libelle'] }}</span>
                        </div>
                    </td>

                    {{-- COLONNE 4: DESCRIPTION (description) //description des courier REUNION OU ATELIER--}} 

                    <td>
                        <div class="small text-dark text-truncate" style="max-width: 200px;" title="{{ $c['description'] }}">
                            {{ $c['description'] ?? '—' }}
                        </div>
                    </td>
                    {{-- COLONNE 5: ASSIGNATION (Service + Agent) --}}
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle me-2 bg-primary bg-opacity-10 text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 32px; height: 32px; font-size: 0.8rem;">
                                {{ substr($c['acteurs']['service']['nom'] ?? 'S', 0, 1) }}
                            </div>
                            <div>
                                <div class="small fw-bold text-dark">{{ $c['acteurs']['service']['nom'] ?? 'Non affecté' }}</div>
                                <div class="extra-small text-muted italic">{{ $c['acteurs']['agent']['nom_complet'] ?? $c['acteurs']['agent']['nom'] ?? 'En attente' }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- COLONNE 6: PIÈCE JOINTE --}}
                    <td>
                        @if($hasFile)
                            <a href="{{ $c['fichier']['url'] }}" target="_blank" 
                               class="d-flex align-items-center p-2 rounded border text-decoration-none file-card"
                               style="background: #f8fafc; transition: all 0.2s ease;">
                                <div class="bg-white rounded p-2 shadow-sm me-2 d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="far fa-file-pdf text-danger fa-lg"></i>
                                </div>
                                <div class="lh-1">
                                    <div class="small fw-bold text-dark text-truncate" style="max-width: 100px;">
                                        {{ $c['fichier']['nom_original'] ?? 'document.pdf' }}
                                    </div>
                                    <div class="extra-small text-muted">{{ $c['fichier']['taille_formatee'] ?? 'PDF' }}</div>
                                </div>
                            </a>
                        @else
                            <span class="text-muted extra-small italic d-flex align-items-center justify-content-center py-2" 
                                  style="min-height: 56px;">
                                <i class="fas fa-paperclip me-1 opacity-50"></i> Aucun fichier
                            </span>
                        @endif
                    </td>

                    {{-- COLONNE 7: ACTIONS (Dropdown) --}}
                    <td class="text-end pe-3">
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle border shadow-sm action-trigger" 
                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg" style="min-width: 200px;">
                                {{-- Voir le dossier --}}
                                <li>
                                    <button class="dropdown-item py-2 btn-view-dossier" data-id="{{ $c['id'] }}">
                                        <i class="fas fa-eye me-2 text-info"></i> Voir le dossier
                                    </button>
                                </li>
                                
                                {{-- Télécharger (si fichier présent) --}}
                                @if($hasFile)
                                    <li>
                                        <a class="dropdown-item py-2 text-primary" href="{{ $c['fichier']['url'] }}" download>
                                            <i class="fas fa-download me-2"></i> Télécharger la pièce
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif

                                {{-- Actions de gestion (si NON verrouillé) --}}
                                @if(!$isLocked)
                                    <li><button class="dropdown-item py-2 btn-edit" data-id="{{ $c['id'] }}"><i class="fas fa-pen me-2 text-warning"></i> Modifier</button></li>
                                    <li><button class="dropdown-item py-2 btn-affecter" data-id="{{ $c['id'] }}"><i class="fas fa-share-nodes me-2 text-success"></i> Affecter</button></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><button class="dropdown-item py-2 text-danger btn-delete" data-id="{{ $c['id'] }}"><i class="fas fa-trash me-2"></i> Supprimer</button></li>
                                @else
                                    {{-- État verrouillé --}}
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <span class="dropdown-item py-2 text-muted disabled" style="cursor: not-allowed;">
                                            <i class="fas fa-lock me-2"></i> Dossier verrouillé
                                        </span>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                {{-- État vide --}}
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 opacity-20"></i>
                            <p class="mb-0">Aucun courrier ne correspond à vos critères.</p>
                            <small class="text-muted">Essayez de modifier vos filtres de recherche.</small>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ========== CSS SPÉCIFIQUE AU TABLEAU ========== --}}
@push('css')
<style>
    /* Badges de type */
    .type-badge {
        transition: all 0.2s ease;
        cursor: default;
    }
    .type-badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
    }
    
    /* Carte fichier */
    .file-card {
        border: 1px solid #e2e8f0 !important;
    }
    .file-card:hover {
        background-color: #f1f5f9 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
        border-color: #cbd5e1 !important;
    }
    
    /* Avatar circle */
    .avatar-circle {
        flex-shrink: 0;
    }
    
    /* Dropdown actions */
    .dropdown-menu {
        border-radius: 12px !important;
        padding: 0.5rem 0 !important;
    }
    .dropdown-item {
        padding: 0.5rem 1rem !important;
        font-size: 0.9rem;
        transition: background 0.15s ease;
    }
    .dropdown-item:hover {
        background: #f8fafc !important;
    }
    .dropdown-item:active {
        background: #e2e8f0 !important;
    }
    .dropdown-item.disabled {
        opacity: 0.6;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .type-badge {
            font-size: 0.7rem !important;
            padding: 0.2rem 0.6rem !important;
        }
        .type-badge i {
            font-size: 0.6rem !important;
        }
        .file-card .small {
            font-size: 0.75rem !important;
        }
        .file-card .extra-small {
            font-size: 0.65rem !important;
        }
    }
</style>
@endpush
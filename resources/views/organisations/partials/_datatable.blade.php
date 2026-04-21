{{-- ============================================
     TABLEAU DATATABLES - ORGANISATIONS
============================================ --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
        <div>
            <h5 class="mb-0 text-primary fw-bold">
                <i class="fas fa-sitemap me-2"></i>Gestion des Organisations
            </h5>
            <small class="text-muted">
                <span id="tableCount">{{ $total }}</span> organisation(s) au total
            </small>
        </div>
        <div class="d-flex gap-2">
            <div class="input-group input-group-sm" style="width: 250px;">
                <span class="input-group-text bg-light border-0"><i class="fas fa-search"></i></span>
                <input type="text" id="tableSearch" class="form-control bg-light border-0" placeholder="Rechercher...">
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="organisationsTable">
            <thead class="bg-light">
                <tr>
                    <th class="border-0 ps-3">Organisation</th>
                    <th class="border-0">Contact</th>
                    <th class="border-0">Type</th>
                    <th class="border-0">Statistiques</th>
                    <th class="border-0 text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($organisations as $org)
                @php
                    $o = is_array($org) ? (object) $org : $org;
                    $typeVal = (int) ($o->type['code'] ?? $o->type ?? 0);
                    $etatVal = $o->etat ?? 'actif';
                    $statutClass = $etatVal === 'actif' ? 'actif' : 'inactif';
                    $statutLabel = $etatVal === 'actif' ? 'Actif' : 'Inactif';
                    $initials = strtoupper(substr($o->sigle ?? $o->nom ?? 'OR', 0, 2));
                    
                    // Configuration des types
                    $typeConfig = [
                        0 => ['label' => 'Externe', 'icon' => 'fa-handshake', 'color' => '#f59e0b'],
                        1 => ['label' => 'Interne', 'icon' => 'fa-building', 'color' => '#3b82f6'],
                        2 => ['label' => 'Gouvernementale', 'icon' => 'fa-landmark', 'color' => '#10b981'],
                        3 => ['label' => 'Privée', 'icon' => 'fa-briefcase', 'color' => '#ef4444'],
                        4 => ['label' => 'ONG', 'icon' => 'fa-hands-helping', 'color' => '#8b5cf6'],
                    ];
                    $type = $typeConfig[$typeVal] ?? $typeConfig[0];
                    
                    // Statistiques des courriers
                    $stats = $o->statistiques['courriers'] ?? ['total' => 0, 'entrants' => 0, 'sortants' => 0, 'internes' => 0, 'urgents' => 0];
                    $hasCourriers = $stats['total'] > 0;
                @endphp
                <tr data-id="{{ $o->id ?? '' }}"
                    data-type="{{ $typeVal }}"
                    data-etat="{{ $etatVal === 'actif' ? 1 : 0 }}"
                    data-search="{{ strtolower(($o->nom ?? '').' '.($o->sigle ?? '').' '.($o->contact['email'] ?? '').' '.($o->contact['adresse'] ?? '')) }}">
                    
                    {{-- COLONNE 1: ORGANISATION AVEC STATUT INTÉGRÉ --}}
                    <td class="ps-3">
                        <div class="d-flex align-items-center">
                            {{-- Logo / Avatar --}}
                            <div class="me-3 position-relative">
                                <div class="d-flex align-items-center justify-content-center rounded-circle shadow-sm" 
                                     style="width: 48px; height: 48px; background: linear-gradient(135deg, {{ $type['color'] }}20 0%, {{ $type['color'] }}10 100%); color: {{ $type['color'] }};">
                                    <span class="fw-bold" style="font-size: 1.1rem;">{{ $initials }}</span>
                                </div>
                                <div class="position-absolute bottom-0 end-0">
                                    <i class="fas fa-circle {{ $statutClass === 'actif' ? 'text-success' : 'text-secondary' }}" style="font-size: 10px;"></i>
                                </div>
                            </div>
                            
                            {{-- Infos principales --}}
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="fw-bold text-dark">{{ $o->nom ?? '—' }}</div>
                                    @if($statutClass === 'inactif')
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size: 0.6rem;">
                                            <i class="fas fa-pause-circle"></i> Inactif
                                        </span>
                                    @endif
                                </div>
                                @if(!empty($o->sigle))
                                    <div class="small text-muted">
                                        <i class="fas fa-tag me-1"></i>{{ $o->sigle }}
                                    </div>
                                @endif
                                <div class="extra-small text-secondary mt-1">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    Créée le {{ isset($o->created_at) ? \Carbon\Carbon::parse($o->created_at)->format('d/m/Y') : '—' }}
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- COLONNE 2: CONTACT --}}
                    <td>
                        @if(!empty($o->contact['email']) || !empty($o->contact['telephone']))
                            <div class="d-flex flex-column gap-2">
                                @if(!empty($o->contact['email']))
                                    <a href="mailto:{{ $o->contact['email'] }}" class="text-decoration-none d-flex align-items-center gap-2 p-1 rounded contact-link" 
                                       style="transition: all 0.2s ease;">
                                        <i class="fas fa-envelope text-muted" style="width: 20px;"></i>
                                        <span class="small text-truncate" style="max-width: 180px;">{{ $o->contact['email'] }}</span>
                                    </a>
                                @endif
                                @if(!empty($o->contact['telephone']))
                                    <a href="tel:{{ $o->contact['telephone'] }}" class="text-decoration-none d-flex align-items-center gap-2 p-1 rounded contact-link"
                                       style="transition: all 0.2s ease;">
                                        <i class="fas fa-phone text-muted" style="width: 20px;"></i>
                                        <span class="small">{{ $o->contact['telephone'] }}</span>
                                    </a>
                                @endif
                                @if(!empty($o->contact['adresse']))
                                    <div class="d-flex align-items-center gap-2 p-1">
                                        <i class="fas fa-map-marker-alt text-muted" style="width: 20px;"></i>
                                        <span class="small text-muted text-truncate" style="max-width: 180px;" title="{{ $o->contact['adresse'] }}">
                                            {{ Str::limit($o->contact['adresse'], 30) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <span class="text-muted small italic">— Aucun contact —</span>
                        @endif
                    </td>

                    {{-- COLONNE 3: TYPE --}}
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill fw-semibold type-badge" 
                                  style="background: linear-gradient(135deg, {{ $type['color'] }}20 0%, {{ $type['color'] }}05 100%); 
                                         color: {{ $type['color'] }}; 
                                         font-size: 0.75rem; 
                                         border: 1px solid {{ $type['color'] }}40;">
                                <i class="fas {{ $type['icon'] }}" style="font-size: 0.7rem;"></i>
                                {{ $type['label'] }}
                            </span>
                        </div>
                    </td>

                    {{-- COLONNE 4: STATISTIQUES COURRIERS --}}
                    <td>
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex align-items-center justify-content-between small">
                                <span class="text-muted">Total courriers:</span>
                                <span class="fw-bold {{ $hasCourriers ? 'text-primary' : 'text-muted' }}">
                                    {{ $stats['total'] }}
                                </span>
                            </div>
                            @if($hasCourriers)
                                <div class="d-flex gap-2 justify-content-start flex-wrap">
                                    @if($stats['entrants'] > 0)
                                        <span class="badge" style="background: #3b82f620; color: #3b82f6; font-size: 0.7rem;">
                                            <i class="fas fa-arrow-down"></i> {{ $stats['entrants'] }}
                                        </span>
                                    @endif
                                    @if($stats['sortants'] > 0)
                                        <span class="badge" style="background: #10b98120; color: #10b981; font-size: 0.7rem;">
                                            <i class="fas fa-arrow-up"></i> {{ $stats['sortants'] }}
                                        </span>
                                    @endif
                                    @if($stats['internes'] > 0)
                                        <span class="badge" style="background: #f59e0b20; color: #f59e0b; font-size: 0.7rem;">
                                            <i class="fas fa-arrow-right-left"></i> {{ $stats['internes'] }}
                                        </span>
                                    @endif
                                    @if($stats['urgents'] > 0)
                                        <span class="badge" style="background: #ef444420; color: #ef4444; font-size: 0.7rem;">
                                            <i class="fas fa-exclamation-triangle"></i> {{ $stats['urgents'] }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <div class="small text-muted italic">Aucun courrier lié</div>
                            @endif
                        </div>
                    </td>

                    {{-- COLONNE 5: ACTIONS --}}
                    <td class="text-end pe-3">
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle border shadow-sm action-trigger" 
                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg" style="min-width: 200px;">
                                {{-- Modifier uniquement --}}
                                <li>
                                    <button class="dropdown-item py-2 btn-edit" data-id="{{ $o->id ?? '' }}">
                                        <i class="fas fa-pen me-2 text-warning"></i> Modifier
                                    </button>
                                </li>
                                
                                {{-- Supprimer (uniquement si pas de courriers) --}}
                                @if(!$hasCourriers)
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <button class="dropdown-item py-2 text-danger btn-delete" data-id="{{ $o->id ?? '' }}">
                                            <i class="fas fa-trash-alt me-2"></i> Supprimer
                                        </button>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-sitemap fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">Aucune organisation enregistrée.</p>
                            <small class="text-muted">Cliquez sur "Nouvelle organisation" pour commencer.</small>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="card-footer bg-white border-top py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Dernière mise à jour: {{ now()->format('d/m/Y H:i:s') }}
                </small>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-primary" id="exportBtn">
                    <i class="fas fa-download"></i> Exporter
                </button>
                <button class="btn btn-sm btn-outline-secondary" id="refreshBtn">
                    <i class="fas fa-sync-alt"></i> Actualiser
                </button>
            </div>
        </div>
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
    
    /* Contact links */
    .contact-link {
        transition: all 0.2s ease;
    }
    .contact-link:hover {
        background: #f8fafc !important;
        transform: translateX(3px);
    }
    .contact-link:hover i {
        color: #009a44 !important;
    }
    
    /* Dropdown actions */
    .dropdown-menu {
        border-radius: 12px !important;
        padding: 0.5rem 0 !important;
        animation: fadeInDown 0.2s ease;
    }
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .dropdown-item {
        padding: 0.5rem 1rem !important;
        font-size: 0.85rem;
        transition: all 0.15s ease;
    }
    .dropdown-item:hover {
        background: #f8fafc !important;
        padding-left: 1.5rem !important;
    }
    .dropdown-item:active {
        background: #e2e8f0 !important;
    }
    
    /* Table hover effect */
    .table-hover tbody tr:hover {
        background: #f8fafc !important;
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
        .contact-link span {
            font-size: 0.7rem !important;
        }
    }
    
    /* Extra small text */
    .extra-small {
        font-size: 0.7rem;
    }
    .italic {
        font-style: italic;
    }
</style>
@endpush

{{-- ========== JAVASCRIPT SPÉCIFIQUE ========== --}}
@push('js')
<script>
$(document).ready(function() {
    // Recherche dans le tableau
    $('#tableSearch').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('#organisationsTable tbody tr').each(function() {
            const $row = $(this);
            const searchText = $row.data('search') || '';
            if (searchText.includes(searchTerm) || searchTerm === '') {
                $row.show();
            } else {
                $row.hide();
            }
        });
        updateVisibleCount();
    });
    
    function updateVisibleCount() {
        const visibleCount = $('#organisationsTable tbody tr:visible').length;
        const totalCount = {{ $total }};
        $('#tableCount').text(visibleCount === totalCount ? totalCount : `${visibleCount}/${totalCount}`);
    }
    
    // Export
    $('#exportBtn').on('click', function() {
        $('#modalExport').modal('show');
    });
    
    // Rafraîchir
    $('#refreshBtn').on('click', function() {
        location.reload();
    });
});
</script>
@endpush
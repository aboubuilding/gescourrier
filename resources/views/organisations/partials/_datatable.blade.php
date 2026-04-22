<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom flex-wrap gap-2">
        <div>
            <h5 class="mb-0 text-primary fw-bold">
                <i class="fas fa-building me-2"></i>Gestion des Organisations
            </h5>
            <small class="text-muted">
                <span id="organisationsCount">{{ count($organisations) }}</span> organisation(s) au total
            </small>
        </div>
        <div class="d-flex gap-2">
            <div class="input-group input-group-sm" style="width: 250px;">
                <span class="input-group-text bg-light border-0">
                    <i class="fas fa-search"></i>
                </span>
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
                    <th class="border-0">Classification</th>
                    <th class="border-0">Statistiques</th>
                    <th class="border-0">Dernier courrier</th>
                    <th class="border-0 text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($organisations as $org)
                @php
                    $typeConfig = [
                        0 => ['label' => 'Externe',         'icon' => 'fa-handshake',      'color' => '#f59e0b', 'bg' => '#fef3c7'],
                        1 => ['label' => 'Interne',         'icon' => 'fa-building',        'color' => '#3b82f6', 'bg' => '#dbeafe'],
                        2 => ['label' => 'Gouvernementale', 'icon' => 'fa-landmark',        'color' => '#10b981', 'bg' => '#d1fae5'],
                        3 => ['label' => 'Privée',          'icon' => 'fa-briefcase',       'color' => '#ef4444', 'bg' => '#fee2e2'],
                        4 => ['label' => 'ONG',             'icon' => 'fa-hands-helping',   'color' => '#8b5cf6', 'bg' => '#ede9fe'],
                    ];
                    $typeCode  = $org['type']['code']    ?? 0;
                    $type      = $typeConfig[$typeCode]  ?? $typeConfig[0];
                    $typeLabel = $org['type']['libelle'] ?? $type['label'];

                    $isActive    = $org['etat'] === 'actif';
                    $statusColor = $isActive ? '#10b981' : '#64748b';
                    $statusBg    = $isActive ? '#d1fae5' : '#f1f5f9';
                    $statusLabel = $isActive ? 'Actif'   : 'Inactif';
                    $statusIcon  = $isActive ? 'fa-check-circle' : 'fa-ban';

                    $initials = strtoupper(substr($org['sigle'] ?? $org['nom'] ?? 'OR', 0, 2));

                    $stats          = $org['statistiques']['courriers'] ?? [];
                    $totalCourriers = $stats['total']    ?? 0;
                    $entrants       = $stats['entrants'] ?? 0;
                    $sortants       = $stats['sortants'] ?? 0;
                    $internes       = $stats['internes'] ?? 0;
                    $urgents        = $stats['urgents']  ?? 0;

                    $contact    = $org['contact'] ?? [];
                    $canDelete  = $totalCourriers === 0;
                    $hasCourriers = $totalCourriers > 0;
                @endphp

                <tr data-id="{{ $org['id'] }}"
                    data-type="{{ $typeCode }}"
                    data-status="{{ $org['etat'] }}"
                    data-search="{{ strtolower(($org['nom'] ?? '').' '.($org['sigle'] ?? '').' '.($contact['email'] ?? '')) }}">

                    {{-- COLONNE 1 : ORGANISATION --}}
                    <td class="ps-3">
                        <div class="d-flex align-items-center">
                            <div class="me-3 position-relative">
                                <div class="d-flex align-items-center justify-content-center rounded-circle shadow-sm"
                                     style="width:48px;height:48px;background:linear-gradient(135deg,{{ $type['color'] }}20 0%,{{ $type['color'] }}08 100%);color:{{ $type['color'] }};">
                                    <span class="fw-bold" style="font-size:1.1rem;">{{ $initials }}</span>
                                </div>
                                <div class="position-absolute bottom-0 end-0">
                                    <i class="fas fa-circle {{ $isActive ? 'text-success' : 'text-secondary' }}" style="font-size:10px;"></i>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="fw-bold text-dark">{{ $org['nom'] ?? '—' }}</div>
                                    @if(!$isActive)
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size:0.6rem;">
                                            <i class="fas fa-pause-circle"></i> Inactif
                                        </span>
                                    @endif
                                </div>
                                @if(!empty($org['sigle']))
                                    <div class="small text-muted">
                                        <i class="fas fa-tag me-1"></i>{{ $org['sigle'] }}
                                    </div>
                                @endif
                                <div class="extra-small text-secondary mt-1">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    Créée le {{ isset($org['created_at']) ? \Carbon\Carbon::parse($org['created_at'])->format('d/m/Y') : '—' }}
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- COLONNE 2 : CONTACT --}}
                    <td>
                        <div class="d-flex flex-column gap-1">
                            @if(!empty($contact['email']))
                                <a href="mailto:{{ $contact['email'] }}" class="text-decoration-none d-flex align-items-center gap-2 small contact-link">
                                    <i class="fas fa-envelope text-muted" style="width:18px;"></i>
                                    <span class="text-truncate" style="max-width:160px;">{{ $contact['email'] }}</span>
                                </a>
                            @endif
                            @if(!empty($contact['telephone']))
                                <a href="tel:{{ $contact['telephone'] }}" class="text-decoration-none d-flex align-items-center gap-2 small contact-link">
                                    <i class="fas fa-phone text-muted" style="width:18px;"></i>
                                    <span>{{ $contact['telephone'] }}</span>
                                </a>
                            @endif
                            @if(!empty($contact['adresse']))
                                <div class="d-flex align-items-center gap-2 small text-muted">
                                    <i class="fas fa-map-marker-alt" style="width:18px;"></i>
                                    <span class="text-truncate" style="max-width:160px;" title="{{ $contact['adresse'] }}">
                                        {{ Str::limit($contact['adresse'], 30) }}
                                    </span>
                                </div>
                            @endif
                            @if(empty($contact['email']) && empty($contact['telephone']) && empty($contact['adresse']))
                                <span class="text-muted small fst-italic">— Aucun contact —</span>
                            @endif
                        </div>
                    </td>

                    {{-- COLONNE 3 : CLASSIFICATION --}}
                    <td>
                        <div class="mb-2">
                            <span class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill fw-semibold type-badge"
                                  style="background:{{ $type['color'] }}15;color:{{ $type['color'] }};border:1px solid {{ $type['color'] }}40;">
                                <i class="fas {{ $type['icon'] }}" style="font-size:0.7rem;"></i>
                                {{ $typeLabel }}
                            </span>
                        </div>
                        <div>
                            <span class="badge rounded-pill px-3 py-1 fw-semibold"
                                  style="background:{{ $statusBg }};color:{{ $statusColor }};border:1px solid {{ $statusColor }}40;font-size:0.7rem;">
                                <i class="fas {{ $statusIcon }} me-1" style="font-size:0.6rem;"></i>
                                {{ $statusLabel }}
                            </span>
                        </div>
                    </td>

                    {{-- COLONNE 4 : STATISTIQUES --}}
                    <td>
                        <div class="d-flex flex-column gap-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="small text-muted">Total courriers :</span>
                                <span class="fw-bold {{ $totalCourriers > 0 ? 'text-primary' : 'text-muted' }}">
                                    {{ $totalCourriers }}
                                </span>
                            </div>
                            @if($totalCourriers > 0)
                                <div class="d-flex gap-2 flex-wrap">
                                    @if($entrants > 0)
                                        <span class="stat-badge stat-entrant"><i class="fas fa-arrow-down"></i> {{ $entrants }}</span>
                                    @endif
                                    @if($sortants > 0)
                                        <span class="stat-badge stat-sortant"><i class="fas fa-arrow-up"></i> {{ $sortants }}</span>
                                    @endif
                                    @if($internes > 0)
                                        <span class="stat-badge stat-interne"><i class="fas fa-arrow-right-left"></i> {{ $internes }}</span>
                                    @endif
                                    @if($urgents > 0)
                                        <span class="stat-badge stat-urgent"><i class="fas fa-exclamation-triangle"></i> {{ $urgents }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted small fst-italic">Aucun courrier lié</span>
                            @endif
                        </div>
                    </td>

                    {{-- COLONNE 5 : DERNIER COURRIER --}}
                    <td>
                        @if($hasCourriers)
                            <div class="d-flex flex-column gap-1">
                                <div class="small fw-semibold text-dark">{{ $totalCourriers }} courrier(s)</div>
                                <div class="extra-small text-muted">
                                    <i class="fas fa-chart-line me-1"></i>
                                    {{ $entrants }} entrants | {{ $sortants }} sortants
                                </div>
                                @if($urgents > 0)
                                    <div class="extra-small text-danger">
                                        <i class="fas fa-exclamation-triangle"></i> {{ $urgents }} urgent(s)
                                    </div>
                                @endif
                            </div>
                        @else
                            <span class="text-muted small fst-italic d-flex align-items-center gap-1">
                                <i class="fas fa-inbox opacity-25"></i> Aucun courrier
                            </span>
                        @endif
                    </td>

                    {{-- COLONNE 6 : ACTIONS --}}
                    <td class="text-end pe-3">
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle border shadow-sm action-trigger"
                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg" style="min-width:200px;">

                                <li>
                                    <button class="dropdown-item py-2 btn-view-organisation"
                                            data-id="{{ $org['id'] }}">
                                        <i class="fas fa-eye me-2 text-info"></i> Voir détails
                                    </button>
                                </li>

                                <li>
                                    <button class="dropdown-item py-2 btn-edit-organisation"
                                            data-id="{{ $org['id'] }}">
                                        <i class="fas fa-pen me-2 text-warning"></i> Modifier
                                    </button>
                                </li>

                                @if($isActive)
                                    <li>
                                        <button class="dropdown-item py-2 btn-disable-organisation"
                                                data-id="{{ $org['id'] }}"
                                                data-nom="{{ $org['nom'] }}">
                                            <i class="fas fa-ban me-2 text-warning"></i> Désactiver
                                        </button>
                                    </li>
                                @else
                                    <li>
                                        <button class="dropdown-item py-2 btn-enable-organisation"
                                                data-id="{{ $org['id'] }}"
                                                data-nom="{{ $org['nom'] }}">
                                            <i class="fas fa-check-circle me-2 text-success"></i> Réactiver
                                        </button>
                                    </li>
                                @endif

                                <li><hr class="dropdown-divider"></li>

                                @if($canDelete)
                                    <li>
                                        <button class="dropdown-item py-2 text-danger btn-delete-organisation"
                                                data-id="{{ $org['id'] }}"
                                                data-nom="{{ $org['nom'] }}"
                                                data-has-courriers="false">
                                            <i class="fas fa-trash-alt me-2"></i> Supprimer
                                        </button>
                                    </li>
                                @else
                                    <li>
                                        <span class="dropdown-item py-2 text-muted disabled" style="cursor:not-allowed;">
                                            <i class="fas fa-lock me-2"></i> {{ $totalCourriers }} courrier(s) lié(s)
                                        </span>
                                    </li>
                                @endif

                            </ul>
                        </div>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-building fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">Aucune organisation enregistrée.</p>
                            <small>Cliquez sur "Nouvelle organisation" pour commencer.</small>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if(is_object($organisations) && method_exists($organisations, 'links'))
    <div class="card-footer bg-white border-top py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Page {{ $organisations->currentPage() }} sur {{ $organisations->lastPage() }}
            </small>
            <div>{{ $organisations->links() }}</div>
        </div>
    </div>
    @endif
</div>

{{-- CSS spécifique au tableau --}}
@push('css')
<style>
    .type-badge {
        transition: all 0.2s ease;
        cursor: default;
        font-size: 0.7rem;
    }
    .type-badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
    }
    .contact-link {
        transition: all 0.2s ease;
        padding: 2px 4px;
        border-radius: 6px;
    }
    .contact-link:hover {
        background-color: #f8fafc;
        transform: translateX(2px);
    }
    .stat-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.65rem;
        font-weight: 500;
    }
    .stat-entrant { background-color: #3b82f620; color: #3b82f6; }
    .stat-sortant { background-color: #10b98120; color: #10b981; }
    .stat-interne { background-color: #f59e0b20; color: #f59e0b; }
    .stat-urgent  { background-color: #ef444420; color: #ef4444; }
    .action-trigger {
        width: 32px;
        height: 32px;
        transition: all 0.2s ease;
    }
    .action-trigger:hover {
        background-color: #e2e8f0;
        transform: rotate(90deg);
    }
    .dropdown-menu {
        border-radius: 12px !important;
        padding: 0.5rem 0 !important;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1) !important;
    }
    .dropdown-item {
        padding: 0.5rem 1rem !important;
        font-size: 0.85rem;
        transition: all 0.15s ease;
    }
    .dropdown-item:hover {
        background: #f8fafc !important;
        transform: translateX(4px);
    }
    .dropdown-item.disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    tbody tr { transition: background-color 0.2s ease; }
    tbody tr:hover { background-color: #f8fafc; }
    .extra-small { font-size: 0.65rem; }

    @media (max-width: 768px) {
        .type-badge  { font-size: 0.65rem !important; padding: 0.2rem 0.6rem !important; }
        .stat-badge  { font-size: 0.6rem !important;  padding: 1px 6px !important; }
        .contact-link span { font-size: 0.7rem !important; }
    }
</style>
@endpush

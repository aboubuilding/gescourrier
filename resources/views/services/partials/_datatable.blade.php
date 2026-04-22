<div class="card shadow-sm border-0">

    {{-- ================= HEADER ================= --}}
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom flex-wrap gap-2">
        <div>
            <h5 class="mb-0 text-success fw-bold">
                <i class="fas fa-layer-group me-2"></i>Gestion des Services
            </h5>
            <small class="text-muted">
                <span id="servicesCount">{{ count($services) }}</span> service(s)
            </small>
        </div>

        {{-- SEARCH (Vanilla JS) --}}
        <div class="input-group input-group-sm" style="width: 260px;">
            <span class="input-group-text bg-light border-0">
                <i class="fas fa-search"></i>
            </span>
            <input type="text"
                   id="servicesSearch"
                   class="form-control bg-light border-0"
                   placeholder="Rechercher...">
        </div>
    </div>

    {{-- ================= TABLE ================= --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="servicesTable">
            <thead class="bg-light">
                <tr>
                    <th class="border-0 ps-3">Service</th>
                    <th class="border-0">Agents</th>
                    <th class="border-0">Courriers</th>
                    <th class="border-0">Activité</th>
                    <th class="border-0">Dates</th>
                    <th class="border-0 text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody id="servicesTableBody">
            @forelse($services as $service)
                @php
                    $id          = data_get($service, 'id', 0);
                    $nom         = data_get($service, 'nom', '—');
                    $sigle       = data_get($service, 'sigle', null);
                    $agents      = (int) (data_get($service, 'agents_count') ?? data_get($service, 'agents_lies') ?? 0);
                    $courriers   = (int) (data_get($service, 'total_courriers') ?? data_get($service, 'courriers_lies') ?? 0);
                    $initials    = !empty($nom) ? strtoupper(substr(strip_tags($nom), 0, 2)) : 'SV';
                    $hasActivity = ($agents > 0 || $courriers > 0);
                    $canDelete   = ($courriers === 0 && $agents === 0);
                    $createdAt   = data_get($service, 'created_at');
                    $updatedAt   = data_get($service, 'updated_at');
                    $dateCreated = $createdAt ? \Carbon\Carbon::parse($createdAt)->format('d/m/Y') : '—';
                    $dateUpdated = $updatedAt ? \Carbon\Carbon::parse($updatedAt)->format('d/m/Y') : null;
                @endphp

                {{-- ✅ Classe 'service-row' pour le filtrage JS --}}
                <tr class="service-row" 
                    data-id="{{ $id }}"
                    data-agents="{{ $agents }}"
                    data-courriers="{{ $courriers }}"
                    data-search="{{ strtolower(strip_tags($nom)) }}">
                    
                    <td class="ps-3">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="d-flex align-items-center justify-content-center rounded-circle shadow-sm"
                                     style="width:44px;height:44px;background:linear-gradient(135deg,#10b98120,#10b98108);color:#10b981;">
                                    <span class="fw-bold">{{ $initials }}</span>
                                </div>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">
                                    {{ e($nom) }}
                                    @if($sigle) <small class="text-muted fw-normal ms-1">({{ e($sigle) }})</small> @endif
                                </div>
                                <div class="text-muted extra-small">ID: #{{ $id }}</div>
                            </div>
                        </div>
                    </td>

                    <td>
                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-1">
                            <i class="fas fa-users me-1"></i> {{ $agents }}
                        </span>
                    </td>

                    <td>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1">
                            <i class="fas fa-envelope me-1"></i> {{ $courriers }}
                        </span>
                    </td>

                    <td>
                        @if($hasActivity)
                            <span class="badge bg-success"><i class="fas fa-chart-line me-1"></i> Actif</span>
                        @else
                            <span class="badge bg-secondary"><i class="fas fa-pause me-1"></i> Inactif</span>
                        @endif
                    </td>

                    <td>
                        <div class="small text-muted">
                            <div><i class="far fa-calendar me-1"></i> {{ $dateCreated }}</div>
                            @if($dateUpdated) <div class="extra-small">MAJ: {{ $dateUpdated }}</div> @endif
                        </div>
                    </td>

                    <td class="text-end pe-3">
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle border shadow-sm" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                                <li><button class="dropdown-item btn-view-service" data-id="{{ $id }}"><i class="fas fa-eye text-info me-2"></i> Voir</button></li>
                                <li><button class="dropdown-item btn-edit-service" data-id="{{ $id }}"><i class="fas fa-pen text-warning me-2"></i> Modifier</button></li>
                                <li><hr class="dropdown-divider"></li>
                                @if($canDelete)
                                    <li><button class="dropdown-item text-danger btn-delete-service" data-id="{{ $id }}" data-nom="{{ e($nom) }}"><i class="fas fa-trash me-2"></i> Supprimer</button></li>
                                @else
                                    <li><span class="dropdown-item text-muted disabled" style="cursor:not-allowed;"><i class="fas fa-lock me-2"></i> {{ $courriers > 0 ? "$courriers courrier(s)" : "Agents affectés" }}</span></li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-layer-group fa-3x opacity-25 mb-3"></i>
                            <p class="mb-0">Aucun service trouvé</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
        
        {{-- Message "Aucun résultat" (caché par défaut) --}}
        <div id="noResultsMessage" class="text-center py-5 d-none">
            <div class="text-muted">
                <i class="fas fa-search fa-3x opacity-25 mb-3"></i>
                <p class="mb-0">Aucun service ne correspond à votre recherche.</p>
            </div>
        </div>
    </div>
</div>

@push('css')
<style>
    .extra-small { font-size: 0.65rem; }
    tbody tr { transition: background-color 0.2s ease; }
    tbody tr:hover { background-color: #f8fafc; }
    /* Classe utilitaire pour cacher les lignes filtrées */
    .row-hidden { display: none !important; }
    .badge { font-size: 0.75rem; font-weight: 500; }
    .dropdown-menu { border-radius: 12px; padding: 0.4rem 0; }
    .dropdown-item.disabled { opacity: 0.6; cursor: not-allowed; }
</style>
@endpush
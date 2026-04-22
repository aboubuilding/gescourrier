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

        {{-- SEARCH --}}
        <div class="input-group input-group-sm" style="width: 260px;">
            <span class="input-group-text bg-light border-0">
                <i class="fas fa-search"></i>
            </span>
            <input type="text"
                   id="tableSearch"
                   class="form-control bg-light border-0"
                   placeholder="Rechercher un service...">
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

            <tbody>

            @forelse($services as $service)

                @php
                    $agents    = $service['agents_lies'] ?? 0;
                    $courriers = $service['courriers_lies'] ?? 0;

                    $initials = strtoupper(substr($service['nom'] ?? 'SV', 0, 2));

                    $hasActivity = ($agents > 0 || $courriers > 0);
                    $canDelete   = $courriers === 0;
                @endphp

                <tr data-id="{{ $service['id'] }}"
                    data-search="{{ strtolower($service['nom']) }}">

                    {{-- ================= SERVICE ================= --}}
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
                                    {{ $service['nom'] }}
                                </div>
                                <div class="text-muted extra-small">
                                    ID: #{{ $service['id'] }}
                                </div>
                            </div>

                        </div>
                    </td>

                    {{-- ================= AGENTS ================= --}}
                    <td>
                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-1">
                            <i class="fas fa-users me-1"></i>
                            {{ $agents }}
                        </span>
                    </td>

                    {{-- ================= COURRIERS ================= --}}
                    <td>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1">
                            <i class="fas fa-envelope me-1"></i>
                            {{ $courriers }}
                        </span>
                    </td>

                    {{-- ================= ACTIVITÉ ================= --}}
                    <td>
                        @if($hasActivity)
                            <span class="badge bg-success">
                                <i class="fas fa-chart-line me-1"></i> Actif
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                <i class="fas fa-pause me-1"></i> Inactif
                            </span>
                        @endif
                    </td>

                    {{-- ================= DATES ================= --}}
                    <td>
                        <div class="small text-muted">
                            <div>
                                <i class="far fa-calendar me-1"></i>
                                {{ \Carbon\Carbon::parse($service['created_at'])->format('d/m/Y') }}
                            </div>

                            @if(!empty($service['updated_at']))
                                <div class="extra-small">
                                    MAJ: {{ \Carbon\Carbon::parse($service['updated_at'])->format('d/m/Y') }}
                                </div>
                            @endif
                        </div>
                    </td>

                    {{-- ================= ACTIONS ================= --}}
                    <td class="text-end pe-3">

                        <div class="dropdown">

                            <button class="btn btn-light btn-sm rounded-circle border shadow-sm"
                                    data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">

                                {{-- VOIR --}}
                                <li>
                                    <button class="dropdown-item btn-view-service"
                                            data-id="{{ $service['id'] }}">
                                        <i class="fas fa-eye text-info me-2"></i> Voir
                                    </button>
                                </li>

                                {{-- MODIFIER --}}
                                <li>
                                    <button class="dropdown-item btn-edit-service"
                                            data-id="{{ $service['id'] }}">
                                        <i class="fas fa-pen text-warning me-2"></i> Modifier
                                    </button>
                                </li>

                                <li><hr class="dropdown-divider"></li>

                                {{-- SUPPRESSION CONDITIONNELLE --}}
                                @if($canDelete)
                                    <li>
                                        <button class="dropdown-item text-danger btn-delete-service"
                                                data-id="{{ $service['id'] }}">
                                            <i class="fas fa-trash me-2"></i> Supprimer
                                        </button>
                                    </li>
                                @else
                                    <li>
                                        <span class="dropdown-item text-muted disabled"
                                              style="cursor:not-allowed;">
                                            <i class="fas fa-lock me-2"></i>
                                            Suppression impossible (courriers liés)
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
                            <i class="fas fa-layer-group fa-3x opacity-25 mb-3"></i>
                            <p class="mb-0">Aucun service trouvé</p>
                        </div>
                    </td>
                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

</div>

{{-- ================= STYLE LOCAL ================= --}}
@push('css')
<style>
.extra-small { font-size: 0.65rem; }

tbody tr { transition: 0.2s ease; }
tbody tr:hover { background-color: #f8fafc; }

.dropdown-menu {
    border-radius: 12px;
    padding: 0.4rem 0;
}

.dropdown-item {
    font-size: 0.85rem;
    transition: 0.15s;
}

.dropdown-item:hover {
    transform: translateX(4px);
    background: #f8fafc;
}

.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

.btn-delete-service:disabled,
.disabled {
    opacity: 0.6;
}
</style>
@endpush
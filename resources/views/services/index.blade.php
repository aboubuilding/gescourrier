@extends('layouts.app')

@section('title', 'Services')
@section('page_title', 'Gestion des Services')
@section('page_icon', 'fa-layer-group')

@section('breadcrumb')
    <li><a href="{{ route('dashboard.index') }}">Accueil</a></li>
    <li>Administration</li>
    <li>Services</li>
@endsection

@section('page_actions')
    <div class="d-flex gap-2">
        <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalExport">
            <i class="fas fa-download"></i> <span class="d-none d-sm-inline">Exporter</span>
        </button>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="fas fa-plus-circle"></i> <span class="d-none d-sm-inline">Nouveau service</span>
        </button>
    </div>
@endsection

@section('css')
{{-- DataTables + Plugins --}}
<link rel="stylesheet" href="{{ asset('app/assets/plugins/datatables/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('app/assets/plugins/datatables/buttons.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('app/assets/plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<style>
    /* ═══════════════════════════════════════════
       🎨 VARIABLES & DESIGN SYSTEM
    ═══════════════════════════════════════════ */
    :root {
        --primaire:      #009a44;
        --primaire-deep: #007a35;
        --primaire-light:#00b850;
        --primaire-pale: #f0fdf4;
        --primaire-muted:rgba(0,154,68,0.08);
        
        --accent:        #c0392b;
        --accent-pale:   #fdf2f2;
        
        --gris-1:        #f8f9fb;
        --gris-2:        #f1f3f7;
        --gris-3:        #e2e8f0;
        --texte:         #1e293b;
        --texte-2:       #475569;
        --texte-3:       #94a3b8;
        
        --radius:        12px;
        --radius-lg:     16px;
        --tr:            all 0.22s cubic-bezier(0.4,0,0.2,1);

        /* États */
        --actif:         #16a34a;  --actif-pale:  #f0fdf4;
        --inactif:       #64748b;  --inactif-pale:#f8fafc;
    }

    /* ═══════════════════════════════════════════
       📊 KPI CARDS
    ═══════════════════════════════════════════ */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: var(--shadow-sm);
        transition: var(--tr);
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
        border-color: var(--primaire);
    }
    .stat-card::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 3px;
        background: var(--primaire);
        border-radius: 0 0 var(--radius-lg) var(--radius-lg);
        opacity: 0;
        transition: opacity 0.2s;
    }
    .stat-card:hover::after { opacity: 1; }
    .stat-card.actif::after   { background: var(--actif); }
    .stat-card.inactif::after { background: var(--inactif); }

    .stat-icon {
        width: 46px; height: 46px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }
    .stat-icon.primaire { background: var(--primaire-pale); color: var(--primaire-deep); }
    .stat-icon.actif    { background: var(--actif-pale);    color: var(--actif); }
    .stat-icon.inactif  { background: var(--inactif-pale);  color: var(--inactif); }

    .stat-value  { font-size: 24px; font-weight: 800; color: var(--texte); line-height: 1; }
    .stat-label  { font-size: 11px; color: var(--texte-3); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 3px; }

    /* ═══════════════════════════════════════════
       🔍 BARRE DE FILTRES
    ═══════════════════════════════════════════ */
    .filter-bar {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-sm);
    }
    .filter-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        color: var(--texte-3);
        margin-bottom: 8px;
        display: block;
    }
    .filter-input, .filter-select {
        width: 100%;
        background: var(--gris-1);
        border: 1.5px solid var(--gris-3);
        border-radius: 10px;
        padding: 11px 14px;
        font-size: 13px;
        color: var(--texte);
        outline: none;
        transition: var(--tr);
        font-family: inherit;
    }
    .filter-input:focus, .filter-select:focus {
        border-color: var(--primaire);
        background: #fff;
        box-shadow: 0 0 0 4px var(--primaire-muted);
    }
    .filter-select {
        appearance: none;
        background-image: url("image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 12px;
        padding-right: 36px;
        cursor: pointer;
    }
    .btn-filter-reset {
        background: var(--gris-1);
        border: 1.5px solid var(--gris-3);
        border-radius: 10px;
        padding: 11px 18px;
        font-size: 13px;
        font-weight: 600;
        color: var(--texte-2);
        cursor: pointer;
        transition: var(--tr);
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }
    .btn-filter-reset:hover {
        border-color: var(--primaire);
        color: var(--primaire);
        background: var(--primaire-pale);
    }

    /* ═══════════════════════════════════════════
       📋 TABLEAU DATATABLES
    ═══════════════════════════════════════════ */
    .table-panel {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }
    .table-panel-head {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--gris-1);
    }
    .table-panel-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--texte);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .table-panel-title i { color: var(--primaire); }
    .count-badge {
        background: var(--primaire-pale);
        color: var(--primaire-deep);
        border: 1px solid rgba(0,154,68,0.2);
        font-size: 12px;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 20px;
    }

    /* Styles DataTables personnalisés */
    .dataTable thead th {
        background: var(--gris-2) !important;
        color: var(--texte-3) !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.6px !important;
        border-bottom: 2px solid var(--primaire) !important;
        white-space: nowrap !important;
        cursor: pointer !important;
    }
    .dataTable tbody tr { transition: background 0.15s !important; }
    .dataTable tbody tr:hover { background: var(--primaire-muted) !important; }
    .dataTable tbody td {
        padding: 12px 16px !important;
        border-bottom: 1px solid var(--border) !important;
        color: var(--texte-2) !important;
        vertical-align: middle !important;
        font-size: 13px !important;
    }

    /* Cellule service */
    .service-cell { display: flex; align-items: center; gap: 12px; }
    .service-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        background: var(--primaire-pale);
        color: var(--primaire-deep);
        display: flex; align-items: center; justify-content: center;
        font-weight: 700;
        font-size: 14px;
        flex-shrink: 0;
    }
    .service-info .service-name {
        font-weight: 600;
        color: var(--texte);
        font-size: 13px;
    }
    .service-info .service-org {
        font-size: 11px;
        color: var(--texte-3);
        margin-top: 2px;
    }

    /* Badges */
    .badge-statut {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        white-space: nowrap;
    }
    .badge-statut.actif    { background: var(--actif-pale); color: var(--actif); }
    .badge-statut.inactif  { background: var(--inactif-pale); color: var(--inactif); }
    .badge-statut::before {
        content: '';
        width: 5px; height: 5px;
        border-radius: 50%;
        background: currentColor;
        margin-right: 4px;
    }

    /* Organisation chip */
    .org-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: var(--gris-2);
        color: var(--texte-2);
        font-size: 11.5px;
        font-weight: 500;
        padding: 4px 10px;
        border-radius: 8px;
        border: 1px solid var(--gris-3);
        max-width: 180px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Agents count badge */
    .agents-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--primaire-pale);
        color: var(--primaire-deep);
        font-size: 11px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 6px;
    }

    /* Actions dropdown */
    .action-dropdown { position: relative; display: inline-block; }
    .action-trigger {
        width: 34px; height: 34px;
        border-radius: 10px;
        background: #fff;
        border: 1.5px solid var(--gris-3);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        font-size: 14px;
        color: var(--texte-3);
        transition: var(--tr);
    }
    .action-trigger:hover,
    .action-dropdown.open .action-trigger {
        border-color: var(--primaire);
        color: var(--primaire);
        background: var(--primaire-pale);
    }
    .action-menu {
        position: absolute;
        right: 0;
        top: calc(100% + 4px);
        min-width: 190px;
        background: #fff;
        border-radius: 12px;
        box-shadow: var(--shadow-lg);
        padding: 6px;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-6px);
        transition: var(--tr);
        border-top: 3px solid var(--primaire);
        pointer-events: none;
    }
    .action-dropdown.open .action-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
        pointer-events: auto;
    }
    .action-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 500;
        color: var(--texte-2);
        text-decoration: none;
        cursor: pointer;
        transition: var(--tr);
        background: none;
        border: none;
        width: 100%;
        text-align: left;
    }
    .action-item i {
        width: 16px;
        text-align: center;
        color: var(--texte-3);
        font-size: 13px;
    }
    .action-item:hover {
        background: var(--primaire-pale);
        color: var(--primaire-deep);
    }
    .action-item:hover i { color: var(--primaire); }
    .action-item.danger { color: var(--accent); }
    .action-item.danger i { color: var(--accent); }
    .action-item.danger:hover { background: var(--accent-pale); }
    .action-divider {
        height: 1px;
        background: var(--gris-3);
        margin: 5px 0;
    }

    /* État vide */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--texte-3);
    }
    .empty-state i {
        font-size: 42px;
        opacity: 0.3;
        margin-bottom: 16px;
        display: block;
    }
    .empty-state p {
        font-size: 14px;
        margin: 0;
    }

    /* ═══════════════════════════════════════════
       🪟 MODALS
    ═══════════════════════════════════════════ */
    .modal-content {
        border: none;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
    }
    .modal-header {
        border-bottom: 1px solid var(--border);
        padding: 18px 24px;
    }
    .modal-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--texte);
    }
    .modal-body {
        padding: 24px;
    }
    .modal-footer {
        border-top: 1px solid var(--border);
        padding: 16px 24px;
    }
    .form-label {
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: var(--texte-3);
        margin-bottom: 6px;
    }
    .form-control, .form-select {
        border: 1.5px solid var(--gris-3);
        border-radius: 10px;
        padding: 11px 14px;
        font-size: 14px;
        color: var(--texte);
        transition: var(--tr);
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--primaire);
        box-shadow: 0 0 0 4px var(--primaire-muted);
    }
    .form-text {
        font-size: 11px;
        color: var(--texte-3);
        margin-top: 4px;
    }

    /* ═══════════════════════════════════════════
       📱 RESPONSIVE
    ═══════════════════════════════════════════ */
    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .filter-bar .row > div { margin-bottom: 12px; }
        .service-info .service-org { display: none; }
        .modal-dialog { margin: 16px; }
    }
    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('contenu')

@php
    // Stats depuis le controller
    $total      = $stats['total'] ?? 0;
    $actifs     = $stats['actifs'] ?? 0;
    $inactifs   = $stats['inactifs'] ?? 0;
    $totalAgents = $stats['total_agents'] ?? 0;
@endphp

{{-- ══════════════════════════════════════════
     KPI CARDS
══════════════════════════════════════════ --}}
<div class="stats-grid">
    <div class="stat-card" data-bs-toggle="modal" data-bs-target="#modalFilter" data-filter="all">
        <div class="stat-icon primaire"><i class="fas fa-layer-group"></i></div>
        <div>
            <div class="stat-value">{{ $total }}</div>
            <div class="stat-label">Total services</div>
        </div>
    </div>
    <div class="stat-card actif" data-bs-toggle="modal" data-bs-target="#modalFilter" data-filter="etat:1">
        <div class="stat-icon actif"><i class="fas fa-check-circle"></i></div>
        <div>
            <div class="stat-value">{{ $actifs }}</div>
            <div class="stat-label">Actifs</div>
        </div>
    </div>
    <div class="stat-card inactif" data-bs-toggle="modal" data-bs-target="#modalFilter" data-filter="etat:2">
        <div class="stat-icon inactif"><i class="fas fa-pause-circle"></i></div>
        <div>
            <div class="stat-value">{{ $inactifs }}</div>
            <div class="stat-label">Inactifs</div>
        </div>
    </div>
    <div class="stat-card" data-bs-toggle="modal" data-bs-target="#modalFilter" data-filter="agents">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div>
            <div class="stat-value">{{ $totalAgents }}</div>
            <div class="stat-label">Agents affectés</div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     BARRE DE FILTRES
══════════════════════════════════════════ --}}
<div class="filter-bar">
    <div class="row g-3 align-items-end">
        {{-- Recherche --}}
        <div class="col-md-4">
            <label class="filter-label">Rechercher</label>
            <input type="text" id="searchInput" class="filter-input" placeholder="Nom du service, organisation…">
        </div>

        {{-- Filtre organisation --}}
        <div class="col-md-3">
            <label class="filter-label">Organisation</label>
            <select id="filterOrganisation" class="filter-select">
                <option value="">Toutes</option>
                @foreach($organisations as $org)
                <option value="{{ $org->id }}">{{ $org->nom }} @if($org->sigle)({{ $org->sigle }})@endif</option>
                @endforeach
            </select>
        </div>

        {{-- Filtre état --}}
        <div class="col-md-2">
            <label class="filter-label">État</label>
            <select id="filterEtat" class="filter-select">
                <option value="">Tous</option>
                <option value="1">Actif</option>
                <option value="2">Inactif</option>
            </select>
        </div>

        {{-- Reset --}}
        <div class="col-md-2">
            <button type="button" id="btnResetFilters" class="btn-filter-reset w-100">
                <i class="fas fa-redo"></i> Réinitialiser
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     TABLEAU DATATABLES
══════════════════════════════════════════ --}}
<div class="table-panel">
    <div class="table-panel-head">
        <h5 class="table-panel-title">
            <i class="fas fa-list"></i> Liste des Services
        </h5>
        <span class="count-badge" id="tableCount">{{ $total }} service{{ $total != 1 ? 's' : '' }}</span>
    </div>

    <div class="table-responsive">
        <table id="servicesTable" class="dataTable w-100">
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Organisation</th>
                    <th>Agents</th>
                    <th>État</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                @php
                    $statutClass = $service->etat == 1 ? 'actif' : 'inactif';
                    $statutLabel = $service->etat == 1 ? 'Actif' : 'Inactif';
                @endphp
                <tr data-id="{{ $service->id }}"
                    data-etat="{{ $service->etat }}"
                    data-organisation="{{ $service->organisation_id }}"
                    data-search="{{ strtolower($service->nom.' '.($service->organisation?->nom ?? '').' '.($service->organisation?->sigle ?? '')) }}">
                    
                    {{-- Service --}}
                    <td>
                        <div class="service-cell">
                            <div class="service-icon">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <div class="service-info">
                                <div class="service-name">{{ $service->nom }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Organisation --}}
                    <td>
                        @if($service->organisation)
                            <span class="org-chip" title="{{ $service->organisation->nom }}">
                                <i class="fas fa-sitemap"></i>
                                {{ $service->organisation->nom }}
                                @if($service->organisation->sigle)
                                <span class="text-muted">({{ $service->organisation->sigle }})</span>
                                @endif
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    {{-- Agents affectés --}}
                    <td>
                        @if($service->agents_count > 0)
                            <span class="agents-badge">
                                <i class="fas fa-users"></i> {{ $service->agents_count }}
                            </span>
                        @else
                            <span class="text-muted small">Aucun</span>
                        @endif
                    </td>

                    {{-- État --}}
                    <td><span class="badge-statut {{ $statutClass }}">{{ $statutLabel }}</span></td>

                    {{-- Actions --}}
                    <td class="text-center">
                        <div class="action-dropdown">
                            <button class="action-trigger"><i class="fas fa-ellipsis-v"></i></button>
                            <div class="action-menu">
                                <button class="action-item btn-edit" data-id="{{ $service->id }}">
                                    <i class="fas fa-pen"></i> Modifier
                                </button>
                                <button class="action-item btn-agents" data-id="{{ $service->id }}">
                                    <i class="fas fa-users"></i> Voir les agents
                                </button>
                                <div class="action-divider"></div>
                                @if($service->etat == 1)
                                <button class="action-item danger btn-suspend" data-id="{{ $service->id }}">
                                    <i class="fas fa-pause"></i> Désactiver
                                </button>
                                @else
                                <button class="action-item btn-restore" data-id="{{ $service->id }}">
                                    <i class="fas fa-play"></i> Réactiver
                                </button>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5"><div class="empty-state"><i class="fas fa-layer-group"></i><p>Aucun service enregistré</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ══════════════════════════════════════════
     MODAL CRÉATION
══════════════════════════════════════════ --}}
<div class="modal fade" id="modalCreate" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formCreate" action="{{ route('services.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle text-success"></i> Nouveau service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Nom --}}
                        <div class="col-12">
                            <label class="form-label">Nom du service *</label>
                            <input type="text" name="nom" class="form-control" required placeholder="Ex: Ressources Humaines, Courrier & Archives…">
                            <div class="form-text">Le nom doit être unique au sein de l'organisation</div>
                        </div>

                        {{-- Organisation --}}
                        <div class="col-12">
                            <label class="form-label">Organisation de rattachement *</label>
                            <select name="organisation_id" class="form-select select2" required>
                                <option value="">Sélectionner une organisation</option>
                                @foreach($organisations as $org)
                                <option value="{{ $org->id }}">{{ $org->nom }} @if($org->sigle)({{ $org->sigle }})@endif</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success" id="btnSubmitCreate">
                        <span class="spinner-border spinner-border-sm d-none" id="spinnerCreate"></span>
                        Créer le service
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     MODAL MODIFICATION
══════════════════════════════════════════ --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formEdit" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-pen text-warning"></i> Modifier le service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editId">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nom du service *</label>
                            <input type="text" name="nom" id="editNom" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Organisation *</label>
                            <select name="organisation_id" id="editOrganisation" class="form-select select2" required>
                                @foreach($organisations as $org)
                                <option value="{{ $org->id }}">{{ $org->nom }} @if($org->sigle)({{ $org->sigle }})@endif</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning" id="btnSubmitEdit">
                        <span class="spinner-border spinner-border-sm d-none" id="spinnerEdit"></span>
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     MODAL EXPORT
══════════════════════════════════════════ --}}
<div class="modal fade" id="modalExport" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-download text-success"></i> Exporter la liste</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <p class="mb-4">Sélectionnez le format d'export :</p>
                <div class="d-grid gap-3">
                    <a href="#" class="btn btn-outline-success btn-lg" id="exportExcel">
                        <i class="fas fa-file-excel fa-2x mb-2"></i><br>Excel (.xlsx)
                    </a>
                    <a href="#" class="btn btn-outline-danger btn-lg" id="exportPDF">
                        <i class="fas fa-file-pdf fa-2x mb-2"></i><br>PDF (.pdf)
                    </a>
                    <a href="#" class="btn btn-outline-primary btn-lg" id="exportCSV">
                        <i class="fas fa-file-csv fa-2x mb-2"></i><br>CSV (.csv)
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
{{-- Scripts requis --}}
<script src="{{ asset('app/assets/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('app/assets/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('app/assets/plugins/datatables/buttons.dataTables.min.js') }}"></script>
<script src="{{ asset('app/assets/plugins/datatables/buttons.bootstrap5.min.js') }}"></script>
<script src="{{ asset('app/assets/plugins/datatables/jszip.min.js') }}"></script>
<script src="{{ asset('app/assets/plugins/datatables/pdfmake.min.js') }}"></script>
<script src="{{ asset('app/assets/plugins/datatables/vfs_fonts.js') }}"></script>
<script src="{{ asset('app/assets/plugins/select2/js/select2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(document).ready(function() {
    
    // ═══════════════════════════════════════
    // ⚙️ CONFIG
    // ═══════════════════════════════════════
    const CSRF = $('meta[name="csrf-token"]').attr('content');
    toastr.options = { progressBar: true, positionClass: 'toast-top-right', timeOut: 3500, closeButton: true };
    
    // ═══════════════════════════════════════
    // 📊 DATATABLES INIT
    // ═══════════════════════════════════════
    const table = $('#servicesTable').DataTable({
        responsive: true,
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json' },
        pageLength: 15,
        lengthMenu: [[15, 30, 50, -1], [15, 30, 50, 'Tous']],
        order: [[0, 'asc']],
        columnDefs: [{ orderable: false, targets: -1 }],
        dom: '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
        buttons: [
            { extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn-success btn-sm', exportOptions: { columns: [0,1,2,3] } },
            { extend: 'pdf', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn-danger btn-sm', exportOptions: { columns: [0,1,2,3] } },
            { extend: 'csv', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn-primary btn-sm', exportOptions: { columns: [0,1,2,3] } }
        ]
    });
    
    // ═══════════════════════════════════════
    // 🔍 FILTRES
    // ═══════════════════════════════════════
    function applyFilters() {
        const search = $('#searchInput').val().toLowerCase();
        const organisation = $('#filterOrganisation').val();
        const etat = $('#filterEtat').val();
        
        // Recherche globale
        table.search(search).draw();
        
        // Filtres personnalisés
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            const row = $(table.row(dataIndex).node());
            const matchOrg = !organisation || row.data('organisation') == organisation;
            const matchEtat = !etat || row.data('etat') == etat;
            return matchOrg && matchEtat;
        });
        
        table.draw();
        updateCount();
    }
    
    function updateCount() {
        const count = table.rows({ search: 'applied' }).count();
        $('#tableCount').text(`${count} service${count != 1 ? 's' : ''}`);
    }
    
    // Événements filtres
    $('#searchInput').on('keyup', applyFilters);
    $('#filterOrganisation, #filterEtat').on('change', applyFilters);
    $('#btnResetFilters').on('click', function() {
        $('#searchInput').val('');
        $('#filterOrganisation, #filterEtat').val('');
        $.fn.dataTable.ext.search = [];
        table.search('').columns().search('').draw();
        updateCount();
    });
    
    // ═══════════════════════════════════════
    // 🪟 MODALS & FORMS
    // ═══════════════════════════════════════
    
    // Création AJAX
    $('#formCreate').on('submit', function(e) {
        e.preventDefault();
        const $btn = $('#btnSubmitCreate');
        const $spinner = $('#spinnerCreate');
        
        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            headers: { 'X-CSRF-TOKEN': CSRF },
            success: function(res) {
                toastr.success(res.message);
                $('#modalCreate').modal('hide');
                setTimeout(() => location.reload(), 800);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.values(errors).flat().forEach(msg => toastr.error(msg));
                } else {
                    toastr.error(xhr.responseJSON?.message || 'Erreur lors de la création');
                }
            },
            complete: function() {
                $btn.prop('disabled', false);
                $spinner.addClass('d-none');
            }
        });
    });
    
    // Modification : ouvrir modal avec données
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        $.get(`/services/${id}/edit`, function(data) {
            $('#editId').val(data.id);
            $('#editNom').val(data.nom);
            $('#editOrganisation').val(data.organisation_id).trigger('change');
            $('#formEdit').attr('action', `/services/${id}`);
            $('#modalEdit').modal('show');
        });
    });
    
    // Modification AJAX
    $('#formEdit').on('submit', function(e) {
        e.preventDefault();
        const $btn = $('#btnSubmitEdit');
        const $spinner = $('#spinnerEdit');
        
        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            headers: { 'X-CSRF-TOKEN': CSRF },
            success: function(res) {
                toastr.success(res.message);
                $('#modalEdit').modal('hide');
                setTimeout(() => location.reload(), 800);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.values(errors).flat().forEach(msg => toastr.error(msg));
                } else {
                    toastr.error(xhr.responseJSON?.message || 'Erreur lors de la mise à jour');
                }
            },
            complete: function() {
                $btn.prop('disabled', false);
                $spinner.addClass('d-none');
            }
        });
    });
    
    // Voir les agents d'un service
    $(document).on('click', '.btn-agents', function() {
        const id = $(this).data('id');
        window.location.href = `/agents?service_id=${id}`;
    });
    
    // Suspendre / Réactiver
    $(document).on('click', '.btn-suspend', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Désactiver ce service ?',
            text: 'Les agents liés resteront accessibles mais le service sera masqué des listes.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#64748b',
            confirmButtonText: 'Désactiver',
            cancelButtonText: 'Annuler'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/services/${id}`,
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF },
                    success: function(res) {
                        toastr.success(res.message);
                        setTimeout(() => location.reload(), 800);
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Erreur');
                    }
                });
            }
        });
    });
    
    $(document).on('click', '.btn-restore', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Réactiver ce service ?',
            text: 'Le service réapparaîtra dans les listes et sélections.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#009a44',
            confirmButtonText: 'Réactiver',
            cancelButtonText: 'Annuler'
        }).then(result => {
            if (result.isConfirmed) {
                $.post(`/services/${id}/restaurer`, { _token: CSRF }, function(res) {
                    toastr.success(res.message);
                    setTimeout(() => location.reload(), 800);
                }).fail(xhr => toastr.error(xhr.responseJSON?.message || 'Erreur'));
            }
        });
    });
    
    // Export
    $('#exportExcel').on('click', function(e) {
        e.preventDefault();
        window.location.href = `/services/export?format=xlsx&filters=${encodeURIComponent(JSON.stringify(getActiveFilters()))}`;
    });
    $('#exportPDF').on('click', function(e) {
        e.preventDefault();
        window.location.href = `/services/export?format=pdf&filters=${encodeURIComponent(JSON.stringify(getActiveFilters()))}`;
    });
    $('#exportCSV').on('click', function(e) {
        e.preventDefault();
        window.location.href = `/services/export?format=csv&filters=${encodeURIComponent(JSON.stringify(getActiveFilters()))}`;
    });
    
    function getActiveFilters() {
        return {
            search: $('#searchInput').val(),
            organisation: $('#filterOrganisation').val(),
            etat: $('#filterEtat').val()
        };
    }
    
    // ═══════════════════════════════════════
    // 🎨 UI & INTERACTIONS
    // ═══════════════════════════════════════
    
    // Dropdown actions
    $(document).on('click', '.action-trigger', function(e) {
        e.stopPropagation();
        const $dropdown = $(this).closest('.action-dropdown');
        const isOpen = $dropdown.hasClass('open');
        $('.action-dropdown').removeClass('open');
        if (!isOpen) $dropdown.addClass('open');
    });
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.action-dropdown').length) {
            $('.action-dropdown').removeClass('open');
        }
    });
    
    // Select2
    $('.select2').select2({ width: '100%', dropdownParent: $(document.body) });
    
    // Flash messages Blade
    @if(session('success')) toastr.success("{{ session('success') }}"); @endif
    @if(session('error')) toastr.error("{{ session('error') }}"); @endif
});
</script>
@endsection
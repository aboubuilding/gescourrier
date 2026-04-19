@extends('layouts.app')

@section('title', 'Agents')
@section('page_title', 'Gestion des Agents')
@section('page_icon', 'fa-user-tie')

@section('breadcrumb')
    <li><a href="{{ route('dashboard.index') }}">Accueil</a></li>
    <li>Administration</li>
    <li>Agents</li>
@endsection

@section('page_actions')
    <div class="d-flex gap-2">
        <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalExport">
            <i class="fas fa-download"></i> <span class="d-none d-sm-inline">Exporter</span>
        </button>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="fas fa-plus-circle"></i> <span class="d-none d-sm-inline">Nouvel agent</span>
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

        /* Statuts agents */
        --actif:         #16a34a;  --actif-pale:  #f0fdf4;
        --inactif:       #64748b;  --inactif-pale:#f8fafc;
        --suspendu:      #dc2626;  --suspendu-pale:#fef2f2;
        
        /* Fonctions */
        --chef:          #7c3aed;  --chef-pale:  #f5f3ff;
        --secretaire:    #0891b2;  --secretaire-pale:#ecfeff;
        --gestionnaire:  #ea580c;  --gestionnaire-pale:#fff7ed;
        --agent:         #2563eb;  --agent-pale:  #eff6ff;
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
    .stat-card.actif::after    { background: var(--actif); }
    .stat-card.inactif::after  { background: var(--inactif); }
    .stat-card.chef::after     { background: var(--chef); }
    .stat-card.service::after  { background: var(--agent); }

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
    .stat-icon.chef     { background: var(--chef-pale);     color: var(--chef); }
    .stat-icon.service  { background: var(--agent-pale);    color: var(--agent); }

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

    /* Pills de fonction */
    .function-pills { display: flex; flex-wrap: wrap; gap: 8px; }
    .function-pill {
        padding: 7px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        border: 1.5px solid var(--gris-3);
        background: var(--gris-1);
        color: var(--texte-2);
        transition: var(--tr);
        user-select: none;
    }
    .function-pill:hover {
        border-color: var(--primaire);
        color: var(--primaire);
        background: var(--primaire-pale);
    }
    .function-pill.active {
        background: var(--primaire);
        color: #fff;
        border-color: var(--primaire);
        box-shadow: 0 4px 12px rgba(0,154,68,0.25);
    }
    .function-pill[data-function="chef"].active      { background: var(--chef); border-color: var(--chef); }
    .function-pill[data-function="secretaire"].active { background: var(--secretaire); border-color: var(--secretaire); }
    .function-pill[data-function="gestionnaire"].active { background: var(--gestionnaire); border-color: var(--gestionnaire); }
    .function-pill[data-function="agent"].active      { background: var(--agent); border-color: var(--agent); }

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

    /* Cellule agent */
    .agent-cell { display: flex; align-items: center; gap: 12px; }
    .agent-avatar {
        width: 40px; height: 40px;
        border-radius: 10px;
        background: var(--primaire-pale);
        color: var(--primaire-deep);
        display: flex; align-items: center; justify-content: center;
        font-weight: 700;
        font-size: 14px;
        flex-shrink: 0;
        text-transform: uppercase;
    }
    .agent-info .agent-name {
        font-weight: 600;
        color: var(--texte);
        font-size: 13px;
    }
    .agent-info .agent-email {
        font-size: 11px;
        color: var(--texte-3);
        margin-top: 2px;
    }

    /* Badges */
    .badge-function, .badge-statut {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        white-space: nowrap;
    }
    .badge-function.chef         { background: var(--chef-pale); color: var(--chef); }
    .badge-function.secretaire   { background: var(--secretaire-pale); color: var(--secretaire); }
    .badge-function.gestionnaire { background: var(--gestionnaire-pale); color: var(--gestionnaire); }
    .badge-function.agent        { background: var(--agent-pale); color: var(--agent); }
    
    .badge-statut.actif    { background: var(--actif-pale); color: var(--actif); }
    .badge-statut.inactif  { background: var(--inactif-pale); color: var(--inactif); }
    .badge-statut.suspendu { background: var(--suspendu-pale); color: var(--suspendu); }
    .badge-statut::before {
        content: '';
        width: 5px; height: 5px;
        border-radius: 50%;
        background: currentColor;
        margin-right: 4px;
    }

    /* Service chip */
    .service-chip {
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
        .agent-info .agent-email { display: none; }
        .modal-dialog { margin: 16px; }
    }
    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr; }
        .function-pills { justify-content: center; }
    }
</style>
@endsection

@section('contenu')

@php
    // Stats depuis le controller
    $total      = $stats['total'] ?? 0;
    $actifs     = $stats['actifs'] ?? 0;
    $inactifs   = $stats['inactifs'] ?? 0;
    $chefs      = $stats['chefs'] ?? 0;
    $sansUser   = $stats['sans_user'] ?? 0;
@endphp

{{-- ══════════════════════════════════════════
     KPI CARDS
══════════════════════════════════════════ --}}
<div class="stats-grid">
    <div class="stat-card" data-bs-toggle="modal" data-bs-target="#modalFilter" data-filter="all">
        <div class="stat-icon primaire"><i class="fas fa-users"></i></div>
        <div>
            <div class="stat-value">{{ $total }}</div>
            <div class="stat-label">Total agents</div>
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
    <div class="stat-card chef" data-bs-toggle="modal" data-bs-target="#modalFilter" data-filter="fonction:chef">
        <div class="stat-icon chef"><i class="fas fa-crown"></i></div>
        <div>
            <div class="stat-value">{{ $chefs }}</div>
            <div class="stat-label">Chefs de service</div>
        </div>
    </div>
    <div class="stat-card service" data-bs-toggle="modal" data-bs-target="#modalFilter" data-filter="sans_user:1">
        <div class="stat-icon service"><i class="fas fa-user-times"></i></div>
        <div>
            <div class="stat-value">{{ $sansUser }}</div>
            <div class="stat-label">Sans compte</div>
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
            <input type="text" id="searchInput" class="filter-input" placeholder="Nom, prénom, email, fonction…">
        </div>

        {{-- Filtre service --}}
        <div class="col-md-2">
            <label class="filter-label">Service</label>
            <select id="filterService" class="filter-select">
                <option value="">Tous</option>
                @foreach($services as $service)
                <option value="{{ $service->id }}">{{ $service->nom }}</option>
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

        {{-- Pills fonction --}}
        <div class="col-md-3">
            <label class="filter-label">Fonction</label>
            <div class="function-pills">
                <span class="function-pill active" data-function="">Tous</span>
                <span class="function-pill" data-function="chef">Chefs</span>
                <span class="function-pill" data-function="secretaire">Secrétaires</span>
                <span class="function-pill" data-function="gestionnaire">Gestionnaires</span>
                <span class="function-pill" data-function="agent">Agents</span>
            </div>
        </div>

        {{-- Reset --}}
        <div class="col-md-1">
            <button type="button" id="btnResetFilters" class="btn-filter-reset w-100">
                <i class="fas fa-redo"></i>
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
            <i class="fas fa-list"></i> Liste des Agents
        </h5>
        <span class="count-badge" id="tableCount">{{ $total }} agent{{ $total != 1 ? 's' : '' }}</span>
    </div>

    <div class="table-responsive">
        <table id="agentsTable" class="dataTable w-100">
            <thead>
                <tr>
                    <th>Agent</th>
                    <th>Contact</th>
                    <th>Fonction</th>
                    <th>Service</th>
                    <th>Compte</th>
                    <th>État</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($agents as $agent)
                @php
                    $functionMap = [
                        'chef' => ['label'=>'Chef de service','class'=>'chef','icon'=>'fa-crown'],
                        'secretaire' => ['label'=>'Secrétaire','class'=>'secretaire','icon'=>'fa-keyboard'],
                        'gestionnaire' => ['label'=>'Gestionnaire','class'=>'gestionnaire','icon'=>'fa-folder-open'],
                        'agent' => ['label'=>'Agent','class'=>'agent','icon'=>'fa-user'],
                    ];
                    $f = $functionMap[$agent->fonction ?? 'agent'] ?? $functionMap['agent'];
                    $statutClass = $agent->etat == 1 ? 'actif' : ($agent->etat == 2 ? 'inactif' : 'suspendu');
                    $statutLabel = $agent->etat == 1 ? 'Actif' : ($agent->etat == 2 ? 'Inactif' : 'Suspendu');
                    $initials = strtoupper(substr($agent->prenom ?? 'A', 0, 1) . substr($agent->nom ?? 'Z', 0, 1));
                @endphp
                <tr data-id="{{ $agent->id }}"
                    data-fonction="{{ $agent->fonction }}"
                    data-etat="{{ $agent->etat }}"
                    data-service="{{ $agent->service_id }}"
                    data-search="{{ strtolower($agent->nom.' '.$agent->prenom.' '.$agent->email.' '.$agent->fonction) }}">
                    
                    {{-- Agent --}}
                    <td>
                        <div class="agent-cell">
                            <div class="agent-avatar">{{ $initials }}</div>
                            <div class="agent-info">
                                <div class="agent-name">{{ $agent->nom }} {{ $agent->prenom }}</div>
                                <div class="agent-email">{{ $agent->email ?? '—' }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Contact --}}
                    <td>
                        @if($agent->telephone)
                            <a href="tel:{{ $agent->telephone }}" class="text-decoration-none" title="Appeler">
                                <i class="fas fa-phone text-muted me-1"></i>
                                <span class="text-muted small">{{ $agent->telephone }}</span>
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    {{-- Fonction --}}
                    <td><span class="badge-function {{ $f['class'] }}"><i class="fas {{ $f['icon'] }}"></i>{{ $f['label'] }}</span></td>

                    {{-- Service --}}
                    <td>
                        @if($agent->service)
                            <span class="service-chip" title="{{ $agent->service->nom }}">
                                <i class="fas fa-network-wired"></i>
                                {{ $agent->service->nom }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    {{-- Compte utilisateur --}}
                    <td>
                        @if($agent->user)
                            <span class="badge bg-success-subtle text-success">
                                <i class="fas fa-check"></i> Lié
                            </span>
                        @else
                            <button class="btn btn-sm btn-outline-primary btn-link-user" data-id="{{ $agent->id }}">
                                <i class="fas fa-link"></i> Lier
                            </button>
                        @endif
                    </td>

                    {{-- État --}}
                    <td><span class="badge-statut {{ $statutClass }}">{{ $statutLabel }}</span></td>

                    {{-- Actions --}}
                    <td class="text-center">
                        <div class="action-dropdown">
                            <button class="action-trigger"><i class="fas fa-ellipsis-v"></i></button>
                            <div class="action-menu">
                                <button class="action-item btn-edit" data-id="{{ $agent->id }}">
                                    <i class="fas fa-pen"></i> Modifier
                                </button>
                                @if(!$agent->user)
                                <button class="action-item btn-link-user" data-id="{{ $agent->id }}">
                                    <i class="fas fa-user-plus"></i> Lier un compte
                                </button>
                                @endif
                                @if($agent->service_id)
                                <button class="action-item btn-reassign" data-id="{{ $agent->id }}">
                                    <i class="fas fa-share-alt"></i> Réassigner service
                                </button>
                                @endif
                                <div class="action-divider"></div>
                                @if($agent->etat == 1)
                                <button class="action-item danger btn-suspend" data-id="{{ $agent->id }}">
                                    <i class="fas fa-pause"></i> Suspendre
                                </button>
                                @else
                                <button class="action-item btn-restore" data-id="{{ $agent->id }}">
                                    <i class="fas fa-play"></i> Réactiver
                                </button>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><div class="empty-state"><i class="fas fa-user-slash"></i><p>Aucun agent enregistré</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ══════════════════════════════════════════
     MODAL CRÉATION
══════════════════════════════════════════ --}}
<div class="modal fade" id="modalCreate" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formCreate" action="{{ route('agents.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle text-success"></i> Nouvel agent</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Identité --}}
                        <div class="col-md-6">
                            <label class="form-label">Nom *</label>
                            <input type="text" name="nom" class="form-control" required placeholder="Nom de famille">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Prénom *</label>
                            <input type="text" name="prenom" class="form-control" required placeholder="Prénom">
                        </div>

                        {{-- Contact --}}
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="email@exemple.tg">
                            <div class="form-text">Optionnel si l'agent n'a pas de compte</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" name="telephone" class="form-control" placeholder="+228 XX XX XX XX">
                        </div>

                        {{-- Fonction & Service --}}
                        <div class="col-md-6">
                            <label class="form-label">Fonction *</label>
                            <select name="fonction" class="form-select" required>
                                <option value="">Sélectionner</option>
                                <option value="chef">Chef de service</option>
                                <option value="secretaire">Secrétaire</option>
                                <option value="gestionnaire">Gestionnaire courrier</option>
                                <option value="agent">Agent de saisie</option>
                                <option value="charge_mission">Chargé de mission</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Service de rattachement *</label>
                            <select name="service_id" class="form-select select2" required>
                                <option value="">Sélectionner un service</option>
                                @foreach($services as $service)
                                <option value="{{ $service->id }}">{{ $service->nom }} @if($service->organisation)({{ $service->organisation->sigle }})@endif</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Compte utilisateur (optionnel) --}}
                        <div class="col-12">
                            <label class="form-label">Compte utilisateur (optionnel)</label>
                            <select name="user_id" class="form-select select2">
                                <option value="">Aucun compte (agent sans accès)</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            <div class="form-text">Lier un compte permet à l'agent de se connecter au système</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success" id="btnSubmitCreate">
                        <span class="spinner-border spinner-border-sm d-none" id="spinnerCreate"></span>
                        Créer l'agent
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
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formEdit" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-pen text-warning"></i> Modifier l'agent</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nom *</label>
                            <input type="text" name="nom" id="editNom" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Prénom *</label>
                            <input type="text" name="prenom" id="editPrenom" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="editEmail" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" name="telephone" id="editTelephone" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fonction *</label>
                            <select name="fonction" id="editFonction" class="form-select" required>
                                <option value="chef">Chef de service</option>
                                <option value="secretaire">Secrétaire</option>
                                <option value="gestionnaire">Gestionnaire courrier</option>
                                <option value="agent">Agent de saisie</option>
                                <option value="charge_mission">Chargé de mission</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Service *</label>
                            <select name="service_id" id="editService" class="form-select select2" required>
                                @foreach($services as $service)
                                <option value="{{ $service->id }}">{{ $service->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Compte utilisateur</label>
                            <select name="user_id" id="editUser" class="form-select select2">
                                <option value="">Aucun compte</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
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
     MODAL LIER COMPTE
══════════════════════════════════════════ --}}
<div class="modal fade" id="modalLinkUser" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formLinkUser" method="POST">
                @csrf
                <input type="hidden" name="agent_id" id="linkAgentId">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-plus text-primary"></i> Lier un compte utilisateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Sélectionnez le compte utilisateur à associer à cet agent.</p>
                    <div class="mb-3">
                        <label class="form-label">Compte utilisateur *</label>
                        <select name="user_id" class="form-select select2" required>
                            <option value="">Sélectionner un compte</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} — {{ $user->email }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle"></i>
                        L'agent pourra se connecter avec les identifiants de ce compte.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm d-none" id="spinnerLink"></span>
                        Lier le compte
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     MODAL RÉASSIGNER SERVICE
══════════════════════════════════════════ --}}
<div class="modal fade" id="modalReassign" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formReassign" method="POST">
                @csrf
                <input type="hidden" name="agent_id" id="reassignAgentId">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-share-alt text-info"></i> Réassigner le service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Changer le service de rattachement de l'agent.</p>
                    <div class="mb-3">
                        <label class="form-label">Nouveau service *</label>
                        <select name="service_id" class="form-select select2" required>
                            <option value="">Sélectionner un service</option>
                            @foreach($services as $service)
                            <option value="{{ $service->id }}">{{ $service->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-info">
                        <span class="spinner-border spinner-border-sm d-none" id="spinnerReassign"></span>
                        Réassigner
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
                <h5 class="modal-title"><i class="fas fa-download text-success"></i> Exporter la liste des agents</h5>
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
    const table = $('#agentsTable').DataTable({
        responsive: true,
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json' },
        pageLength: 15,
        lengthMenu: [[15, 30, 50, -1], [15, 30, 50, 'Tous']],
        order: [[0, 'asc']],
        columnDefs: [{ orderable: false, targets: -1 }],
        dom: '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
        buttons: [
            { extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn-success btn-sm', exportOptions: { columns: [0,1,2,3,4,5] } },
            { extend: 'pdf', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn-danger btn-sm', exportOptions: { columns: [0,1,2,3,4,5] } },
            { extend: 'csv', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn-primary btn-sm', exportOptions: { columns: [0,1,2,3,4,5] } }
        ]
    });
    
    // ═══════════════════════════════════════
    // 🔍 FILTRES
    // ═══════════════════════════════════════
    function applyFilters() {
        const search = $('#searchInput').val().toLowerCase();
        const fonction = $('.function-pill.active').data('function');
        const service = $('#filterService').val();
        const etat = $('#filterEtat').val();
        
        // Recherche globale
        table.search(search).draw();
        
        // Filtres personnalisés via API DataTables
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            const row = $(table.row(dataIndex).node());
            const matchFonction = !fonction || row.data('fonction') == fonction;
            const matchService = !service || row.data('service') == service;
            const matchEtat = !etat || row.data('etat') == etat;
            return matchFonction && matchService && matchEtat;
        });
        
        table.draw();
        updateCount();
    }
    
    function updateCount() {
        const count = table.rows({ search: 'applied' }).count();
        $('#tableCount').text(`${count} agent${count != 1 ? 's' : ''}`);
    }
    
    // Événements filtres
    $('#searchInput').on('keyup', applyFilters);
    $('.function-pill').on('click', function() {
        $('.function-pill').removeClass('active');
        $(this).addClass('active');
        applyFilters();
    });
    $('#filterService, #filterEtat').on('change', applyFilters);
    $('#btnResetFilters').on('click', function() {
        $('#searchInput').val('');
        $('.function-pill').removeClass('active').first().addClass('active');
        $('#filterService, #filterEtat').val('');
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
        $.get(`/agents/${id}/edit`, function(data) {
            $('#editId').val(data.id);
            $('#editNom').val(data.nom);
            $('#editPrenom').val(data.prenom);
            $('#editEmail').val(data.email);
            $('#editTelephone').val(data.telephone);
            $('#editFonction').val(data.fonction);
            $('#editService').val(data.service_id).trigger('change');
            $('#editUser').val(data.user_id).trigger('change');
            $('#formEdit').attr('action', `/agents/${id}`);
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
    
    // Lier compte utilisateur
    $(document).on('click', '.btn-link-user', function() {
        $('#linkAgentId').val($(this).data('id'));
        $('#modalLinkUser').modal('show');
    });
    
    $('#formLinkUser').on('submit', function(e) {
        e.preventDefault();
        const $btn = $(this).find('button[type="submit"]');
        const $spinner = $('#spinnerLink');
        const id = $('#linkAgentId').val();
        
        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');
        
        $.ajax({
            url: `/agents/${id}/lier-user`,
            method: 'POST',
            data: $(this).serialize(),
            headers: { 'X-CSRF-TOKEN': CSRF },
            success: function(res) {
                toastr.success(res.message);
                $('#modalLinkUser').modal('hide');
                setTimeout(() => location.reload(), 800);
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Erreur lors de la liaison');
            },
            complete: function() {
                $btn.prop('disabled', false);
                $spinner.addClass('d-none');
            }
        });
    });
    
    // Réassigner service
    $(document).on('click', '.btn-reassign', function() {
        $('#reassignAgentId').val($(this).data('id'));
        $('#modalReassign').modal('show');
    });
    
    $('#formReassign').on('submit', function(e) {
        e.preventDefault();
        const $btn = $(this).find('button[type="submit"]');
        const $spinner = $('#spinnerReassign');
        const id = $('#reassignAgentId').val();
        
        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');
        
        $.ajax({
            url: `/agents/${id}/reassigner-service`,
            method: 'POST',
            data: $(this).serialize(),
            headers: { 'X-CSRF-TOKEN': CSRF },
            success: function(res) {
                toastr.success(res.message);
                $('#modalReassign').modal('hide');
                setTimeout(() => location.reload(), 800);
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Erreur lors de la réassignation');
            },
            complete: function() {
                $btn.prop('disabled', false);
                $spinner.addClass('d-none');
            }
        });
    });
    
    // Suspendre / Réactiver
    $(document).on('click', '.btn-suspend', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Suspendre cet agent ?',
            text: 'L\'agent ne pourra plus accéder au système.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#64748b',
            confirmButtonText: 'Suspendre',
            cancelButtonText: 'Annuler'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/agents/${id}`,
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
            title: 'Réactiver cet agent ?',
            text: 'L\'agent retrouvera un accès actif au système.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#009a44',
            confirmButtonText: 'Réactiver',
            cancelButtonText: 'Annuler'
        }).then(result => {
            if (result.isConfirmed) {
                $.post(`/agents/${id}/restaurer`, { _token: CSRF }, function(res) {
                    toastr.success(res.message);
                    setTimeout(() => location.reload(), 800);
                }).fail(xhr => toastr.error(xhr.responseJSON?.message || 'Erreur'));
            }
        });
    });
    
    // Export
    $('#exportExcel').on('click', function(e) {
        e.preventDefault();
        window.location.href = `/agents/export?format=xlsx&filters=${encodeURIComponent(JSON.stringify(getActiveFilters()))}`;
    });
    $('#exportPDF').on('click', function(e) {
        e.preventDefault();
        window.location.href = `/agents/export?format=pdf&filters=${encodeURIComponent(JSON.stringify(getActiveFilters()))}`;
    });
    $('#exportCSV').on('click', function(e) {
        e.preventDefault();
        window.location.href = `/agents/export?format=csv&filters=${encodeURIComponent(JSON.stringify(getActiveFilters()))}`;
    });
    
    function getActiveFilters() {
        return {
            search: $('#searchInput').val(),
            fonction: $('.function-pill.active').data('function'),
            service: $('#filterService').val(),
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
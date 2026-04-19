@extends('layouts.app')

@section('title', 'Utilisateurs')
@section('page_title', 'Gestion des Utilisateurs')
@section('page_icon', 'fa-users')

@section('breadcrumb')
    <li><a href="{{ route('dashboard.index') }}">Accueil</a></li>
    <li>Administration</li>
    <li>Utilisateurs</li>
@endsection

@section('page_actions')
    <div class="d-flex gap-2">
        <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalExport">
            <i class="fas fa-download"></i> <span class="d-none d-sm-inline">Exporter</span>
        </button>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="fas fa-plus-circle"></i> <span class="d-none d-sm-inline">Nouvel utilisateur</span>
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

        /* Rôles */
        --admin:         #7c3aed;   --admin-pale:  #f5f3ff;
        --chef:          #0891b2;   --chef-pale:   #ecfeff;
        --secretaire:    #ea580c;   --secretaire-pale:#fff7ed;
        --agent:         #2563eb;   --agent-pale:  #eff6ff;
        
        /* États */
        --actif:         #16a34a;   --actif-pale:  #f0fdf4;
        --suspendu:      #dc2626;   --suspendu-pale:#fef2f2;
        --email-verifie: #10b981;   --email-verifie-pale:#ecfdf5;
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
    .stat-card.admin::after      { background: var(--admin); }
    .stat-card.chef::after       { background: var(--chef); }
    .stat-card.secretaire::after { background: var(--secretaire); }
    .stat-card.agent::after      { background: var(--agent); }
    .stat-card.actif::after      { background: var(--actif); }
    .stat-card.suspendu::after   { background: var(--suspendu); }

    .stat-icon {
        width: 46px; height: 46px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }
    .stat-icon.primaire  { background: var(--primaire-pale); color: var(--primaire-deep); }
    .stat-icon.admin     { background: var(--admin-pale);    color: var(--admin); }
    .stat-icon.chef      { background: var(--chef-pale);     color: var(--chef); }
    .stat-icon.secretaire{ background: var(--secretaire-pale);color: var(--secretaire); }
    .stat-icon.agent     { background: var(--agent-pale);    color: var(--agent); }
    .stat-icon.actif     { background: var(--actif-pale);    color: var(--actif); }
    .stat-icon.suspendu  { background: var(--suspendu-pale); color: var(--suspendu); }

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

    /* Pills de rôle */
    .role-pills { display: flex; flex-wrap: wrap; gap: 8px; }
    .role-pill {
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
    .role-pill:hover {
        border-color: var(--primaire);
        color: var(--primaire);
        background: var(--primaire-pale);
    }
    .role-pill.active {
        background: var(--primaire);
        color: #fff;
        border-color: var(--primaire);
        box-shadow: 0 4px 12px rgba(0,154,68,0.25);
    }
    .role-pill[data-role="admin"].active      { background: var(--admin); border-color: var(--admin); }
    .role-pill[data-role="chef_service"].active { background: var(--chef); border-color: var(--chef); }
    .role-pill[data-role="secretaire"].active  { background: var(--secretaire); border-color: var(--secretaire); }
    .role-pill[data-role="agent"].active       { background: var(--agent); border-color: var(--agent); }

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

    /* Cellule utilisateur */
    .user-cell { display: flex; align-items: center; gap: 12px; }
    .user-avatar {
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
    .user-info .user-name {
        font-weight: 600;
        color: var(--texte);
        font-size: 13px;
    }
    .user-info .user-email {
        font-size: 11px;
        color: var(--texte-3);
        margin-top: 2px;
    }

    /* Badges */
    .badge-role, .badge-statut, .badge-email {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        white-space: nowrap;
    }
    .badge-role.admin      { background: var(--admin-pale); color: var(--admin); }
    .badge-role.chef_service { background: var(--chef-pale); color: var(--chef); }
    .badge-role.secretaire { background: var(--secretaire-pale); color: var(--secretaire); }
    .badge-role.agent      { background: var(--agent-pale); color: var(--agent); }
    
    .badge-statut.actif    { background: var(--actif-pale); color: var(--actif); }
    .badge-statut.suspendu { background: var(--suspendu-pale); color: var(--suspendu); }
    .badge-statut::before {
        content: '';
        width: 5px; height: 5px;
        border-radius: 50%;
        background: currentColor;
        margin-right: 4px;
    }
    
    .badge-email.verifie { background: var(--email-verifie-pale); color: var(--email-verifie); }
    .badge-email::before {
        content: '✓';
        font-size: 9px;
        margin-right: 3px;
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
        min-width: 210px;
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
    .password-strength {
        height: 4px;
        border-radius: 2px;
        background: var(--gris-3);
        margin-top: 6px;
        overflow: hidden;
    }
    .password-strength-bar {
        height: 100%;
        width: 0%;
        transition: width 0.3s, background 0.3s;
    }
    .password-strength-bar.weak { width: 33%; background: var(--suspendu); }
    .password-strength-bar.medium { width: 66%; background: var(--secretaire); }
    .password-strength-bar.strong { width: 100%; background: var(--actif); }

    /* ═══════════════════════════════════════════
       📱 RESPONSIVE
    ═══════════════════════════════════════════ */
    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .filter-bar .row > div { margin-bottom: 12px; }
        .user-info .user-email { display: none; }
        .modal-dialog { margin: 16px; }
    }
    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr; }
        .role-pills { justify-content: center; }
    }
</style>
@endsection

@section('contenu')

@php
    // Stats depuis le controller
    $total      = $stats['total'] ?? 0;
    $actifs     = $stats['actifs'] ?? 0;
    $suspendus  = $stats['suspendus'] ?? 0;
    $admins     = $stats['admins'] ?? 0;
    $chefs      = $stats['chefs'] ?? 0;
    $agents     = $stats['agents'] ?? 0;
    $emailVerifies = $stats['email_verifies'] ?? 0;
@endphp

{{-- ══════════════════════════════════════════
     KPI CARDS
══════════════════════════════════════════ --}}
<div class="stats-grid">
    <div class="stat-card" data-bs-toggle="modal" data-bs-target="#modalFilter" data-filter="all">
        <div class="stat-icon primaire"><i class="fas fa-users"></i></div>
        <div>
            <div class="stat-value">{{ $total }}</div>
            <div class="stat-label">Total utilisateurs</div>
        </div>
    </div>
    <div class="stat-card actif" data-bs-toggle="modal" data-bs-target="#modalFilter" data-filter="etat:1">
        <div class="stat-icon actif"><i class="fas fa-check-circle"></i></div>
        <div>
            <div class="stat-value">{{ $actifs }}</div>
            <div class="stat-label">Actifs</div>
        </div>
    </div>
    <div class="stat-card suspendu" data-bs-toggle="modal" data-bs-target="#modalFilter" data-filter="etat:2">
        <div class="stat-icon suspendu"><i class="fas fa-pause-circle"></i></div>
        <div>
            <div class="stat-value">{{ $suspendus }}</div>
            <div class="stat-label">Suspendus</div>
        </div>
    </div>
    <div class="stat-card admin" data-bs-toggle="modal" data-bs-target="#modalFilter" data-filter="role:admin">
        <div class="stat-icon admin"><i class="fas fa-crown"></i></div>
        <div>
            <div class="stat-value">{{ $admins }}</div>
            <div class="stat-label">Administrateurs</div>
        </div>
    </div>
    <div class="stat-card chef" data-bs-toggle="modal" data-bs-target="#modalFilter" data-filter="role:chef_service">
        <div class="stat-icon chef"><i class="fas fa-user-tie"></i></div>
        <div>
            <div class="stat-value">{{ $chefs }}</div>
            <div class="stat-label">Chefs de service</div>
        </div>
    </div>
    <div class="stat-card agent" data-bs-toggle="modal" data-bs-target="#modalFilter" data-filter="role:agent">
        <div class="stat-icon agent"><i class="fas fa-user"></i></div>
        <div>
            <div class="stat-value">{{ $agents }}</div>
            <div class="stat-label">Agents</div>
        </div>
    </div>
    <div class="stat-card" data-bs-toggle="modal" data-bs-target="#modalFilter" data-filter="email_verified:1">
        <div class="stat-icon"><i class="fas fa-envelope-circle-check"></i></div>
        <div>
            <div class="stat-value">{{ $emailVerifies }}</div>
            <div class="stat-label">Emails vérifiés</div>
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
            <input type="text" id="searchInput" class="filter-input" placeholder="Nom, email, rôle…">
        </div>

        {{-- Filtre rôle --}}
        <div class="col-md-2">
            <label class="filter-label">Rôle</label>
            <select id="filterRole" class="filter-select">
                <option value="">Tous</option>
                <option value="admin">Administrateur</option>
                <option value="chef_service">Chef de service</option>
                <option value="secretaire">Secrétaire</option>
                <option value="agent">Agent</option>
            </select>
        </div>

        {{-- Filtre état --}}
        <div class="col-md-2">
            <label class="filter-label">État</label>
            <select id="filterEtat" class="filter-select">
                <option value="">Tous</option>
                <option value="1">Actif</option>
                <option value="2">Suspendu</option>
            </select>
        </div>

        {{-- Pills rôle --}}
        <div class="col-md-3">
            <label class="filter-label">Filtrer par rôle</label>
            <div class="role-pills">
                <span class="role-pill active" data-role="">Tous</span>
                <span class="role-pill" data-role="admin">Admins</span>
                <span class="role-pill" data-role="chef_service">Chefs</span>
                <span class="role-pill" data-role="secretaire">Secrétaires</span>
                <span class="role-pill" data-role="agent">Agents</span>
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
            <i class="fas fa-list"></i> Liste des Utilisateurs
        </h5>
        <span class="count-badge" id="tableCount">{{ $total }} utilisateur{{ $total != 1 ? 's' : '' }}</span>
    </div>

    <div class="table-responsive">
        <table id="usersTable" class="dataTable w-100">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Rôle</th>
                    <th>Email</th>
                    <th>Dernière connexion</th>
                    <th>État</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                @php
                    $roleMap = [
                        'admin' => ['label'=>'Administrateur','class'=>'admin','icon'=>'fa-crown'],
                        'chef_service' => ['label'=>'Chef de service','class'=>'chef_service','icon'=>'fa-user-tie'],
                        'secretaire' => ['label'=>'Secrétaire','class'=>'secretaire','icon'=>'fa-keyboard'],
                        'agent' => ['label'=>'Agent','class'=>'agent','icon'=>'fa-user'],
                    ];
                    $r = $roleMap[$user->role] ?? ['label'=>'Utilisateur','class'=>'agent','icon'=>'fa-user'];
                    $statutClass = $user->etat == 1 ? 'actif' : 'suspendu';
                    $statutLabel = $user->etat == 1 ? 'Actif' : 'Suspendu';
                    $initials = strtoupper(substr($user->name, 0, 1));
                @endphp
                <tr data-id="{{ $user->id }}"
                    data-role="{{ $user->role }}"
                    data-etat="{{ $user->etat }}"
                    data-email-verified="{{ $user->email_verified_at ? 1 : 0 }}"
                    data-search="{{ strtolower($user->name.' '.$user->email.' '.$user->role) }}">
                    
                    {{-- Utilisateur --}}
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar">{{ $initials }}</div>
                            <div class="user-info">
                                <div class="user-name">{{ $user->name }}</div>
                                @if($user->telephone)
                                <div class="user-email"><i class="fas fa-phone me-1"></i>{{ $user->telephone }}</div>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Rôle --}}
                    <td><span class="badge-role {{ $r['class'] }}"><i class="fas {{ $r['icon'] }}"></i>{{ $r['label'] }}</span></td>

                    {{-- Email --}}
                    <td>
                        <div class="d-flex flex-column">
                            <a href="mailto:{{ $user->email }}" class="text-decoration-none">{{ $user->email }}</a>
                            @if($user->email_verified_at)
                            <span class="badge-email verifie small">Vérifié</span>
                            @endif
                        </div>
                    </td>

                    {{-- Dernière connexion --}}
                    <td>
                        @if($user->derniere_connexion)
                            <span title="{{ $user->derniere_connexion->format('d/m/Y H:i') }}">
                                {{ $user->derniere_connexion->diffForHumans() }}
                            </span>
                        @else
                            <span class="text-muted">Jamais</span>
                        @endif
                    </td>

                    {{-- État --}}
                    <td><span class="badge-statut {{ $statutClass }}">{{ $statutLabel }}</span></td>

                    {{-- Actions --}}
                    <td class="text-center">
                        <div class="action-dropdown">
                            <button class="action-trigger"><i class="fas fa-ellipsis-v"></i></button>
                            <div class="action-menu">
                                <button class="action-item btn-edit" data-id="{{ $user->id }}">
                                    <i class="fas fa-pen"></i> Modifier
                                </button>
                                <button class="action-item btn-role" data-id="{{ $user->id }}">
                                    <i class="fas fa-user-tag"></i> Changer le rôle
                                </button>
                                <button class="action-item btn-password" data-id="{{ $user->id }}">
                                    <i class="fas fa-key"></i> Réinitialiser MDP
                                </button>
                                <div class="action-divider"></div>
                                @if($user->etat == 1)
                                <button class="action-item danger btn-suspend" data-id="{{ $user->id }}">
                                    <i class="fas fa-pause"></i> Suspendre
                                </button>
                                @else
                                <button class="action-item btn-restore" data-id="{{ $user->id }}">
                                    <i class="fas fa-play"></i> Réactiver
                                </button>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6"><div class="empty-state"><i class="fas fa-users-slash"></i><p>Aucun utilisateur enregistré</p></div></td></tr>
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
            <form id="formCreate" action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle text-success"></i> Nouvel utilisateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Identité --}}
                        <div class="col-md-6">
                            <label class="form-label">Nom complet *</label>
                            <input type="text" name="name" class="form-control" required placeholder="Nom et prénom">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" required placeholder="email@exemple.tg">
                        </div>

                        {{-- Mot de passe --}}
                        <div class="col-md-6">
                            <label class="form-label">Mot de passe *</label>
                            <input type="password" name="password" id="createPassword" class="form-control" required minlength="8">
                            <div class="password-strength">
                                <div class="password-strength-bar" id="createStrength"></div>
                            </div>
                            <div class="form-text">Minimum 8 caractères</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirmer le mot de passe *</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>

                        {{-- Rôle & Contact --}}
                        <div class="col-md-6">
                            <label class="form-label">Rôle *</label>
                            <select name="role" class="form-select" required>
                                <option value="agent">Agent</option>
                                <option value="secretaire">Secrétaire</option>
                                <option value="chef_service">Chef de service</option>
                                <option value="admin">Administrateur</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" name="telephone" class="form-control" placeholder="+228 XX XX XX XX">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success" id="btnSubmitCreate">
                        <span class="spinner-border spinner-border-sm d-none" id="spinnerCreate"></span>
                        Créer l'utilisateur
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
                    <h5 class="modal-title"><i class="fas fa-pen text-warning"></i> Modifier l'utilisateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nom complet *</label>
                            <input type="text" name="name" id="editName" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" id="editEmail" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Rôle *</label>
                            <select name="role" id="editRole" class="form-select" required>
                                <option value="agent">Agent</option>
                                <option value="secretaire">Secrétaire</option>
                                <option value="chef_service">Chef de service</option>
                                <option value="admin">Administrateur</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" name="telephone" id="editTelephone" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Nouveau mot de passe (laisser vide pour conserver)</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••">
                            <div class="form-text">Remplissez uniquement pour changer le mot de passe</div>
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
     MODAL CHANGER RÔLE
══════════════════════════════════════════ --}}
<div class="modal fade" id="modalRole" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formRole" method="POST">
                @csrf
                <input type="hidden" name="user_id" id="roleUserId">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-tag text-primary"></i> Changer le rôle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Sélectionnez le nouveau rôle pour cet utilisateur.</p>
                    <div class="mb-3">
                        <label class="form-label">Nouveau rôle *</label>
                        <select name="role" class="form-select" required>
                            <option value="agent">Agent</option>
                            <option value="secretaire">Secrétaire</option>
                            <option value="chef_service">Chef de service</option>
                            <option value="admin">Administrateur</option>
                        </select>
                    </div>
                    <div class="alert alert-warning small">
                        <i class="fas fa-exclamation-triangle"></i>
                        Le changement de rôle peut modifier les permissions d'accès de l'utilisateur.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm d-none" id="spinnerRole"></span>
                        Mettre à jour le rôle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     MODAL RÉINITIALISER MOT DE PASSE
══════════════════════════════════════════ --}}
<div class="modal fade" id="modalPassword" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formPassword" method="POST">
                @csrf
                <input type="hidden" name="user_id" id="passwordUserId">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-key text-warning"></i> Réinitialiser le mot de passe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Définissez un nouveau mot de passe pour l'utilisateur.</p>
                    <div class="mb-3">
                        <label class="form-label">Nouveau mot de passe *</label>
                        <input type="password" name="password" id="resetPassword" class="form-control" required minlength="8">
                        <div class="password-strength">
                            <div class="password-strength-bar" id="resetStrength"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirmer le mot de passe *</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle"></i>
                        L'utilisateur devra se reconnecter avec ce nouveau mot de passe.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">
                        <span class="spinner-border spinner-border-sm d-none" id="spinnerPassword"></span>
                        Réinitialiser
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
    const table = $('#usersTable').DataTable({
        responsive: true,
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json' },
        pageLength: 15,
        lengthMenu: [[15, 30, 50, -1], [15, 30, 50, 'Tous']],
        order: [[0, 'asc']],
        columnDefs: [{ orderable: false, targets: -1 }],
        dom: '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
        buttons: [
            { extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn-success btn-sm', exportOptions: { columns: [0,1,2,3,4] } },
            { extend: 'pdf', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn-danger btn-sm', exportOptions: { columns: [0,1,2,3,4] } },
            { extend: 'csv', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn-primary btn-sm', exportOptions: { columns: [0,1,2,3,4] } }
        ]
    });
    
    // ═══════════════════════════════════════
    // 🔍 FILTRES
    // ═══════════════════════════════════════
    function applyFilters() {
        const search = $('#searchInput').val().toLowerCase();
        const role = $('.role-pill.active').data('role');
        const filterRole = $('#filterRole').val();
        const etat = $('#filterEtat').val();
        
        // Recherche globale
        table.search(search).draw();
        
        // Filtres personnalisés
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            const row = $(table.row(dataIndex).node());
            const matchRole = (!role && !filterRole) || row.data('role') == role || row.data('role') == filterRole;
            const matchEtat = !etat || row.data('etat') == etat;
            return matchRole && matchEtat;
        });
        
        table.draw();
        updateCount();
    }
    
    function updateCount() {
        const count = table.rows({ search: 'applied' }).count();
        $('#tableCount').text(`${count} utilisateur${count != 1 ? 's' : ''}`);
    }
    
    // Événements filtres
    $('#searchInput').on('keyup', applyFilters);
    $('.role-pill').on('click', function() {
        $('.role-pill').removeClass('active');
        $(this).addClass('active');
        applyFilters();
    });
    $('#filterRole, #filterEtat').on('change', applyFilters);
    $('#btnResetFilters').on('click', function() {
        $('#searchInput').val('');
        $('.role-pill').removeClass('active').first().addClass('active');
        $('#filterRole, #filterEtat').val('');
        $.fn.dataTable.ext.search = [];
        table.search('').columns().search('').draw();
        updateCount();
    });
    
    // ═══════════════════════════════════════
    // 🔐 PASSWORD STRENGTH
    // ═══════════════════════════════════════
    function checkPasswordStrength(password, $bar) {
        let strength = 0;
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]+/)) strength++;
        if (password.match(/[A-Z]+/)) strength++;
        if (password.match(/[0-9]+/)) strength++;
        if (password.match(/[^a-zA-Z0-9]+/)) strength++;
        
        $bar.removeClass('weak medium strong');
        if (strength <= 2) $bar.addClass('weak');
        else if (strength <= 4) $bar.addClass('medium');
        else $bar.addClass('strong');
    }
    
    $('#createPassword, #resetPassword').on('input', function() {
        const id = $(this).attr('id');
        const $bar = id === 'createPassword' ? $('#createStrength') : $('#resetStrength');
        checkPasswordStrength($(this).val(), $bar);
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
             $(this).serialize(),
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
        $.get(`/users/${id}/edit`, function(data) {
            $('#editId').val(data.id);
            $('#editName').val(data.name);
            $('#editEmail').val(data.email);
            $('#editRole').val(data.role);
            $('#editTelephone').val(data.telephone);
            $('#formEdit').attr('action', `/users/${id}`);
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
             $(this).serialize(),
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
    
    // Changer rôle
    $(document).on('click', '.btn-role', function() {
        $('#roleUserId').val($(this).data('id'));
        $('#modalRole').modal('show');
    });
    
    $('#formRole').on('submit', function(e) {
        e.preventDefault();
        const $btn = $(this).find('button[type="submit"]');
        const $spinner = $('#spinnerRole');
        const id = $('#roleUserId').val();
        
        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');
        
        $.ajax({
            url: `/users/${id}/role`,
            method: 'POST',
             $(this).serialize(),
            headers: { 'X-CSRF-TOKEN': CSRF },
            success: function(res) {
                toastr.success(res.message);
                $('#modalRole').modal('hide');
                setTimeout(() => location.reload(), 800);
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Erreur lors du changement de rôle');
            },
            complete: function() {
                $btn.prop('disabled', false);
                $spinner.addClass('d-none');
            }
        });
    });
    
    // Réinitialiser mot de passe
    $(document).on('click', '.btn-password', function() {
        $('#passwordUserId').val($(this).data('id'));
        $('#modalPassword').modal('show');
    });
    
    $('#formPassword').on('submit', function(e) {
        e.preventDefault();
        const $btn = $(this).find('button[type="submit"]');
        const $spinner = $('#spinnerPassword');
        const id = $('#passwordUserId').val();
        
        // Validation côté client
        const pwd = $('input[name="password"]').val();
        const confirm = $('input[name="password_confirmation"]').val();
        if (pwd !== confirm) {
            toastr.error('Les mots de passe ne correspondent pas');
            return;
        }
        
        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');
        
        $.ajax({
            url: `/users/${id}/password`,
            method: 'POST',
             $(this).serialize(),
            headers: { 'X-CSRF-TOKEN': CSRF },
            success: function(res) {
                toastr.success(res.message);
                $('#modalPassword').modal('hide');
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Erreur lors de la réinitialisation');
            },
            complete: function() {
                $btn.prop('disabled', false);
                $spinner.addClass('d-none');
                $('input[name="password"], input[name="password_confirmation"]').val('');
                $('#resetStrength').removeClass('weak medium strong').css('width', '0%');
            }
        });
    });
    
    // Suspendre / Réactiver
    $(document).on('click', '.btn-suspend', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Suspendre cet utilisateur ?',
            text: 'L\'utilisateur ne pourra plus se connecter au système.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#64748b',
            confirmButtonText: 'Suspendre',
            cancelButtonText: 'Annuler'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/users/${id}`,
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
            title: 'Réactiver cet utilisateur ?',
            text: 'L\'utilisateur retrouvera un accès actif au système.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#009a44',
            confirmButtonText: 'Réactiver',
            cancelButtonText: 'Annuler'
        }).then(result => {
            if (result.isConfirmed) {
                $.post(`/users/${id}/restaurer`, { _token: CSRF }, function(res) {
                    toastr.success(res.message);
                    setTimeout(() => location.reload(), 800);
                }).fail(xhr => toastr.error(xhr.responseJSON?.message || 'Erreur'));
            }
        });
    });
    
    // Export
    $('#exportExcel').on('click', function(e) {
        e.preventDefault();
        window.location.href = `/users/export?format=xlsx&filters=${encodeURIComponent(JSON.stringify(getActiveFilters()))}`;
    });
    $('#exportPDF').on('click', function(e) {
        e.preventDefault();
        window.location.href = `/users/export?format=pdf&filters=${encodeURIComponent(JSON.stringify(getActiveFilters()))}`;
    });
    $('#exportCSV').on('click', function(e) {
        e.preventDefault();
        window.location.href = `/users/export?format=csv&filters=${encodeURIComponent(JSON.stringify(getActiveFilters()))}`;
    });
    
    function getActiveFilters() {
        return {
            search: $('#searchInput').val(),
            role: $('.role-pill.active').data('role') || $('#filterRole').val(),
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
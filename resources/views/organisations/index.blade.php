@extends('layouts.app')

@section('title', 'Organisations')
@section('page_title', 'Gestion des Organisations')
@section('page_icon', 'fa-sitemap')

@section('breadcrumb')
    <li><a href="{{ route('dashboard.index') }}">Accueil</a></li>
    <li>Administration</li>
    <li>Organisations</li>
@endsection

@section('page_actions')
    <div class="d-flex gap-2">
        <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalExport">
            <i class="fas fa-download"></i> <span class="d-none d-sm-inline">Exporter</span>
        </button>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="fas fa-plus-circle"></i> <span class="d-none d-sm-inline">Nouvelle organisation</span>
        </button>
    </div>
@endsection

@push('css')
{{-- DataTables + Plugins --}}
<link rel="stylesheet" href="{{ asset('app/assets/plugins/datatables/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('app/assets/plugins/datatables/buttons.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('app/assets/plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<style>
    /* ✅ Hérite des variables de layout.css — pas de :root personnalisé */
    .organisations-page .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 16px; margin-bottom: 24px;
    }
    .organisations-page .stat-card {
        background: var(--bg-card); border: 1px solid var(--border);
        border-radius: var(--radius-lg); padding: 18px 20px;
        display: flex; align-items: center; gap: 14px;
        box-shadow: var(--shadow-sm); transition: var(--transition);
        position: relative; overflow: hidden; cursor: pointer;
    }
    .organisations-page .stat-card:hover {
        transform: translateY(-3px); box-shadow: var(--shadow-md);
        border-color: var(--success);
    }
    .organisations-page .stat-card::after {
        content: ''; position: absolute; bottom: 0; left: 0; right: 0;
        height: 3px; background: var(--success);
        border-radius: 0 0 var(--radius-lg) var(--radius-lg);
        opacity: 0; transition: opacity 0.2s;
    }
    .organisations-page .stat-card:hover::after { opacity: 1; }
    .organisations-page .stat-card.interne::after    { background: var(--info); }
    .organisations-page .stat-card.externe::after    { background: var(--warning); }
    .organisations-page .stat-card.gouvernement::after { background: var(--success); }
    .organisations-page .stat-card.prive::after      { background: var(--danger); }
    .organisations-page .stat-card.ong::after        { background: var(--info); }
    .organisations-page .stat-card.actif::after      { background: var(--success); }

    .organisations-page .stat-icon {
        width: 46px; height: 46px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; flex-shrink: 0;
        background: var(--success); color: #fff;
    }
    .organisations-page .stat-icon.primaire    { background: var(--success); color: #fff; }
    .organisations-page .stat-icon.interne     { background: var(--info); color: #fff; }
    .organisations-page .stat-icon.externe     { background: var(--warning); color: #fff; }
    .organisations-page .stat-icon.gouvernement{ background: var(--success); color: #fff; }
    .organisations-page .stat-icon.prive       { background: var(--danger); color: #fff; }
    .organisations-page .stat-icon.ong         { background: var(--info); color: #fff; }
    .organisations-page .stat-icon.actif       { background: var(--success); color: #fff; }

    .organisations-page .stat-value  { font-size: 24px; font-weight: 800; color: var(--text-primary); line-height: 1; }
    .organisations-page .stat-label  { font-size: 11px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 3px; }

    .organisations-page .filter-bar {
        background: var(--bg-card); border: 1px solid var(--border);
        border-radius: var(--radius-lg); padding: 20px; margin-bottom: 24px; box-shadow: var(--shadow-sm);
    }
    .organisations-page .filter-label {
        font-size: 11px; font-weight: 700; letter-spacing: 0.8px;
        text-transform: uppercase; color: var(--text-muted); margin-bottom: 8px; display: block;
    }
    .organisations-page .filter-input, .organisations-page .filter-select {
        width: 100%; background: var(--bg-hover); border: 1.5px solid var(--border);
        border-radius: 10px; padding: 11px 14px; font-size: 13px; color: var(--text-primary);
        outline: none; transition: var(--transition); font-family: inherit;
    }
    .organisations-page .filter-input:focus, .organisations-page .filter-select:focus {
        border-color: var(--success); background: var(--bg-card); box-shadow: 0 0 0 4px var(--success);
    }
    .organisations-page .filter-select {
        appearance: none;
        background-image: url("image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat; background-position: right 12px center; background-size: 12px; padding-right: 36px; cursor: pointer;
    }
    .organisations-page .btn-filter-reset {
        background: var(--bg-hover); border: 1.5px solid var(--border); border-radius: 10px;
        padding: 11px 18px; font-size: 13px; font-weight: 600; color: var(--text-secondary);
        cursor: pointer; transition: var(--transition); display: flex; align-items: center; gap: 6px; white-space: nowrap;
    }
    .organisations-page .btn-filter-reset:hover { border-color: var(--success); color: var(--success); background: var(--success); }

    .organisations-page .type-pills { display: flex; flex-wrap: wrap; gap: 8px; }
    .organisations-page .type-pill {
        padding: 7px 16px; border-radius: 20px; font-size: 12px; font-weight: 600;
        cursor: pointer; border: 1.5px solid var(--border); background: var(--bg-hover);
        color: var(--text-secondary); transition: var(--transition); user-select: none;
    }
    .organisations-page .type-pill:hover { border-color: var(--success); color: var(--success); background: var(--success); }
    .organisations-page .type-pill.active { background: var(--success); color: #fff; border-color: var(--success); box-shadow: 0 4px 12px var(--success); }
    .organisations-page .type-pill[data-type="0"].active { background: var(--warning); border-color: var(--warning); }
    .organisations-page .type-pill[data-type="1"].active { background: var(--info); border-color: var(--info); }
    .organisations-page .type-pill[data-type="2"].active { background: var(--success); border-color: var(--success); }
    .organisations-page .type-pill[data-type="3"].active { background: var(--danger); border-color: var(--danger); }
    .organisations-page .type-pill[data-type="4"].active { background: var(--info); border-color: var(--info); }

    .organisations-page .table-panel {
        background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm); overflow: hidden;
    }
    .organisations-page .table-panel-head {
        padding: 16px 20px; border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between; background: var(--bg-hover);
    }
    .organisations-page .table-panel-title { font-size: 14px; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 8px; }
    .organisations-page .table-panel-title i { color: var(--success); }
    .organisations-page .count-badge {
        background: var(--success); color: #fff; border: 1px solid var(--success);
        font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 20px;
    }
    .organisations-page .dataTable thead th {
        background: var(--bg-hover) !important; color: var(--text-muted) !important; font-size: 11px !important;
        font-weight: 700 !important; text-transform: uppercase !important; letter-spacing: 0.6px !important;
        border-bottom: 2px solid var(--success) !important; white-space: nowrap !important; cursor: pointer !important;
    }
    .organisations-page .dataTable tbody tr { transition: background 0.15s !important; }
    .organisations-page .dataTable tbody tr:hover { background: var(--success) !important; }
    .organisations-page .dataTable tbody td {
        padding: 12px 16px !important; border-bottom: 1px solid var(--border) !important;
        color: var(--text-secondary) !important; vertical-align: middle !important; font-size: 13px !important;
    }
    .organisations-page .org-cell { display: flex; align-items: center; gap: 12px; }
    .organisations-page .org-logo {
        width: 40px; height: 40px; border-radius: 10px;
        background: var(--success); color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 14px; flex-shrink: 0;
        text-transform: uppercase;
    }
    .organisations-page .org-info .org-name {
        font-weight: 600; color: var(--text-primary); font-size: 13px;
    }
    .organisations-page .org-info .org-sigle {
        font-size: 11px; color: var(--text-muted); margin-top: 2px; font-family: monospace;
    }
    .organisations-page .badge-type, .organisations-page .badge-statut {
        display: inline-flex; align-items: center; gap: 5px; font-size: 11px;
        font-weight: 600; padding: 4px 10px; border-radius: 20px; white-space: nowrap;
    }
    .organisations-page .badge-type.interne      { background: var(--info); color: #fff; }
    .organisations-page .badge-type.externe      { background: var(--warning); color: #fff; }
    .organisations-page .badge-type.gouvernement { background: var(--success); color: #fff; }
    .organisations-page .badge-type.prive        { background: var(--danger); color: #fff; }
    .organisations-page .badge-type.ong          { background: var(--info); color: #fff; }
    .organisations-page .badge-statut.actif    { background: var(--success); color: #fff; }
    .organisations-page .badge-statut.inactif  { background: var(--text-muted); color: #fff; }
    .organisations-page .badge-statut::before {
        content: ''; width: 5px; height: 5px; border-radius: 50%;
        background: currentColor; margin-right: 4px;
    }
    .organisations-page .contact-chip {
        display: inline-flex; align-items: center; gap: 5px;
        background: var(--bg-hover); color: var(--text-secondary);
        font-size: 11.5px; font-weight: 500; padding: 4px 10px;
        border-radius: 8px; border: 1px solid var(--border);
        max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .organisations-page .action-dropdown { position: relative; display: inline-block; }
    .organisations-page .action-trigger {
        width: 34px; height: 34px; border-radius: 10px;
        background: var(--bg-card); border: 1.5px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; font-size: 14px; color: var(--text-muted);
        transition: var(--transition);
    }
    .organisations-page .action-trigger:hover,
    .organisations-page .action-dropdown.open .action-trigger {
        border-color: var(--success); color: var(--success); background: var(--success);
    }
    .organisations-page .action-menu {
        position: absolute; right: 0; top: calc(100% + 4px);
        min-width: 190px; background: var(--bg-card); border-radius: 12px;
        box-shadow: var(--shadow-lg); padding: 6px; z-index: 1000;
        opacity: 0; visibility: hidden; transform: translateY(-6px);
        transition: var(--transition); border-top: 3px solid var(--success);
        pointer-events: none;
    }
    .organisations-page .action-dropdown.open .action-menu {
        opacity: 1; visibility: visible; transform: translateY(0); pointer-events: auto;
    }
    .organisations-page .action-item {
        display: flex; align-items: center; gap: 10px; padding: 10px 14px;
        border-radius: 10px; font-size: 13px; font-weight: 500;
        color: var(--text-secondary); text-decoration: none; cursor: pointer;
        transition: var(--transition); background: none; border: none;
        width: 100%; text-align: left;
    }
    .organisations-page .action-item i {
        width: 16px; text-align: center; color: var(--text-muted); font-size: 13px;
    }
    .organisations-page .action-item:hover {
        background: var(--success); color: #fff;
    }
    .organisations-page .action-item:hover i { color: #fff; }
    .organisations-page .action-item.danger { color: var(--danger); }
    .organisations-page .action-item.danger i { color: var(--danger); }
    .organisations-page .action-item.danger:hover { background: var(--danger); }
    .organisations-page .action-divider { height: 1px; background: var(--border); margin: 5px 0; }
    .organisations-page .empty-state { text-align: center; padding: 60px 20px; color: var(--text-muted); }
    .organisations-page .empty-state i { font-size: 42px; opacity: 0.3; margin-bottom: 16px; display: block; }
    .organisations-page .empty-state p { font-size: 14px; margin: 0; }
    .organisations-page .modal-content { border: none; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); }
    .organisations-page .modal-header { border-bottom: 1px solid var(--border); padding: 18px 24px; }
    .organisations-page .modal-title { font-size: 16px; font-weight: 700; color: var(--text-primary); }
    .organisations-page .modal-body { padding: 24px; }
    .organisations-page .modal-footer { border-top: 1px solid var(--border); padding: 16px 24px; }
    .organisations-page .form-label { font-size: 12px; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px; }
    .organisations-page .form-control, .organisations-page .form-select { border: 1.5px solid var(--border); border-radius: 10px; padding: 11px 14px; font-size: 14px; color: var(--text-primary); transition: var(--transition); }
    .organisations-page .form-control:focus, .organisations-page .form-select:focus { border-color: var(--success); box-shadow: 0 0 0 4px var(--success); }
    @media (max-width: 768px) { .organisations-page .stats-grid { grid-template-columns: repeat(2, 1fr); } .organisations-page .filter-bar .row > div { margin-bottom: 12px; } .organisations-page .org-info .org-sigle { display: none; } .organisations-page .modal-dialog { margin: 16px; } }
    @media (max-width: 480px) { .organisations-page .stats-grid { grid-template-columns: 1fr; } .organisations-page .type-pills { justify-content: center; } }
</style>
@endpush

@section('contenu')
@php
    // Stats depuis le controller
    $total        = $stats['total'] ?? 0;
    $internes     = $stats['internes'] ?? 0;
    $externes     = $stats['externes'] ?? 0;
    $gouvernement = $stats['gouvernement'] ?? 0;
    $prive        = $stats['prive'] ?? 0;
    $ong          = $stats['ong'] ?? 0;
    $actifs       = $stats['actifs'] ?? 0;
    
    // Référence au modèle pour les constantes
    $O = \App\Models\Organisation::class;
@endphp

<div class="organisations-page">

{{-- ══════════════════════════════════════════
     KPI CARDS
══════════════════════════════════════════ --}}
<div class="stats-grid">
    <div class="stat-card"><div class="stat-icon primaire"><i class="fas fa-sitemap"></i></div><div><div class="stat-value">{{ $total }}</div><div class="stat-label">Total organisations</div></div></div>
    <div class="stat-card interne"><div class="stat-icon interne"><i class="fas fa-building"></i></div><div><div class="stat-value">{{ $internes }}</div><div class="stat-label">Internes</div></div></div>
    <div class="stat-card externe"><div class="stat-icon externe"><i class="fas fa-handshake"></i></div><div><div class="stat-value">{{ $externes }}</div><div class="stat-label">Externes</div></div></div>
    <div class="stat-card gouvernement"><div class="stat-icon gouvernement"><i class="fas fa-landmark"></i></div><div><div class="stat-value">{{ $gouvernement }}</div><div class="stat-label">Gouvernementales</div></div></div>
    <div class="stat-card prive"><div class="stat-icon prive"><i class="fas fa-briefcase"></i></div><div><div class="stat-value">{{ $prive }}</div><div class="stat-label">Privées</div></div></div>
    <div class="stat-card ong"><div class="stat-icon ong"><i class="fas fa-hands-helping"></i></div><div><div class="stat-value">{{ $ong }}</div><div class="stat-label">ONG</div></div></div>
    <div class="stat-card actif"><div class="stat-icon actif"><i class="fas fa-check-circle"></i></div><div><div class="stat-value">{{ $actifs }}</div><div class="stat-label">Actives</div></div></div>
</div>

{{-- ══════════════════════════════════════════
     BARRE DE FILTRES
══════════════════════════════════════════ --}}
<div class="filter-bar">
    <div class="row g-3 align-items-end">
        <div class="col-md-4"><label class="filter-label">Rechercher</label><input type="text" id="searchInput" class="filter-input" placeholder="Nom, sigle, email, téléphone…"></div>
        <div class="col-md-2"><label class="filter-label">Type</label><select id="filterType" class="filter-select"><option value="">Tous</option><option value="0">Externe</option><option value="1">Interne</option><option value="2">Gouvernementale</option><option value="3">Privée</option><option value="4">ONG</option></select></div>
        <div class="col-md-2"><label class="filter-label">État</label><select id="filterEtat" class="filter-select"><option value="">Tous</option><option value="1">Actif</option><option value="2">Inactif</option></select></div>
        <div class="col-md-3"><label class="filter-label">Catégorie</label><div class="type-pills"><span class="type-pill active" data-type="">Tous</span><span class="type-pill" data-type="0">Externes</span><span class="type-pill" data-type="1">Internes</span><span class="type-pill" data-type="2">Gouv.</span><span class="type-pill" data-type="3">Privées</span><span class="type-pill" data-type="4">ONG</span></div></div>
        <div class="col-md-1"><button type="button" id="btnResetFilters" class="btn-filter-reset w-100"><i class="fas fa-redo"></i></button></div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     TABLEAU DATATABLES
══════════════════════════════════════════ --}}
<div class="table-panel">
    <div class="table-panel-head">
        <h5 class="table-panel-title"><i class="fas fa-list"></i> Liste des Organisations</h5>
        <span class="count-badge" id="tableCount">{{ $total }} organisation{{ $total != 1 ? 's' : '' }}</span>
    </div>
    <div class="table-responsive">
        <table id="organisationsTable" class="dataTable w-100">
            <thead>
                <tr><th>Organisation</th><th>Contact</th><th>Type</th><th>Services</th><th>État</th><th class="text-center">Actions</th></tr>
            </thead>
            <tbody>
                @forelse($organisations as $org)
                @php
                    // 🔁 SOLUTION : Conversion sécurisée array → objet
                    $o = is_array($org) ? (object) $org : $org;
                    
                    // ✅ Cast en int pour éviter "Illegal offset type"
                    $typeVal = (int) ($o->type ?? 0);
                    $etatVal = (int) ($o->etat ?? $O::ETAT_ACTIF);
                    
                    // Maps pour l'affichage (avec constantes du modèle)
                    $typeMap = [
                        0 => ['label'=>'Externe','class'=>'externe','icon'=>'fa-handshake'],
                        1 => ['label'=>'Interne','class'=>'interne','icon'=>'fa-building'],
                        2 => ['label'=>'Gouvernementale','class'=>'gouvernement','icon'=>'fa-landmark'],
                        3 => ['label'=>'Privée','class'=>'prive','icon'=>'fa-briefcase'],
                        4 => ['label'=>'ONG','class'=>'ong','icon'=>'fa-hands-helping'],
                    ];
                    $t = $typeMap[$typeVal] ?? $typeMap[0];
                    $statutClass = $etatVal == $O::ETAT_ACTIF ? 'actif' : 'inactif';
                    $statutLabel = $etatVal == $O::ETAT_ACTIF ? 'Actif' : 'Inactif';
                    $initials = strtoupper(substr($o->sigle ?? $o->nom ?? 'OR', 0, 2));
                @endphp
                <tr data-id="{{ $o->id ?? '' }}"
                    data-type="{{ $typeVal }}"
                    data-etat="{{ $etatVal }}"
                    data-search="{{ strtolower(($o->nom ?? '').' '.($o->sigle ?? '').' '.($o->email ?? '').' '.($o->adresse ?? '')) }}">
                    
                    {{-- Organisation --}}
                    <td>
                        <div class="org-cell">
                            <div class="org-logo">{{ $initials }}</div>
                            <div class="org-info">
                                <div class="org-name">{{ $o->nom ?? '—' }}</div>
                                @if(!empty($o->sigle))<div class="org-sigle">{{ $o->sigle }}</div>@endif
                            </div>
                        </div>
                    </td>

                    {{-- Contact --}}
                    <td>
                        @if(!empty($o->email) || !empty($o->telephone))
                            <div class="contact-chip">
                                @if(!empty($o->email))<a href="mailto:{{ $o->email }}" class="text-decoration-none" title="Email"><i class="fas fa-envelope text-muted"></i></a>@endif
                                @if(!empty($o->telephone))<a href="tel:{{ $o->telephone }}" class="text-decoration-none ms-1" title="Téléphone"><i class="fas fa-phone text-muted"></i></a>@endif
                            </div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    {{-- Type --}}
                    <td><span class="badge-type {{ $t['class'] }}"><i class="fas {{ $t['icon'] }}"></i>{{ $t['label'] }}</span></td>

                    {{-- Services liés --}}
                    <td>
                        @if(isset($o->services_count) && $o->services_count > 0)
                            <span class="badge bg-success-subtle text-success"><i class="fas fa-layer-group"></i> {{ $o->services_count }}</span>
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
                                <button class="action-item btn-edit" data-id="{{ $o->id ?? '' }}"><i class="fas fa-pen"></i> Modifier</button>
                                <button class="action-item btn-services" data-id="{{ $o->id ?? '' }}"><i class="fas fa-list"></i> Voir les services</button>
                                <div class="action-divider"></div>
                                @if($etatVal == $O::ETAT_ACTIF)
                                <button class="action-item danger btn-suspend" data-id="{{ $o->id ?? '' }}"><i class="fas fa-pause"></i> Désactiver</button>
                                @else
                                <button class="action-item btn-restore" data-id="{{ $o->id ?? '' }}"><i class="fas fa-play"></i> Réactiver</button>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6"><div class="empty-state"><i class="fas fa-sitemap"></i><p>Aucune organisation enregistrée</p></div></td></tr>
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
            <form id="formCreate" action="{{ route('organisations.store') }}" method="POST">@csrf
                <div class="modal-header"><h5 class="modal-title"><i class="fas fa-plus-circle text-success"></i> Nouvelle organisation</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body"><div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Nom *</label><input type="text" name="nom" class="form-control" required placeholder="Nom complet"></div>
                    <div class="col-md-6"><label class="form-label">Sigle</label><input type="text" name="sigle" class="form-control" placeholder="Ex: DGA" maxlength="20"><div class="form-text">Acronyme officiel</div></div>
                    <div class="col-md-6"><label class="form-label">Type *</label><select name="type" class="form-select" required><option value="">Sélectionner</option><option value="1">Interne</option><option value="0">Externe</option><option value="2">Gouvernementale</option><option value="3">Privée</option><option value="4">ONG</option></select></div>
                    <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" placeholder="contact@exemple.tg"></div>
                    <div class="col-md-6"><label class="form-label">Téléphone</label><input type="tel" name="telephone" class="form-control" placeholder="+228 XX XX XX XX"></div>
                    <div class="col-md-6"><label class="form-label">Adresse</label><input type="text" name="adresse" class="form-control" placeholder="Adresse postale"></div>
                </div></div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-success" id="btnSubmitCreate"><span class="spinner-border spinner-border-sm d-none" id="spinnerCreate"></span> Créer</button></div>
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
            <form id="formEdit" method="POST">@csrf @method('PUT')
                <div class="modal-header"><h5 class="modal-title"><i class="fas fa-pen text-warning"></i> Modifier</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body"><input type="hidden" name="id" id="editId"><div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Nom *</label><input type="text" name="nom" id="editNom" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label">Sigle</label><input type="text" name="sigle" id="editSigle" class="form-control" maxlength="20"></div>
                    <div class="col-md-6"><label class="form-label">Type *</label><select name="type" id="editType" class="form-select" required><option value="0">Externe</option><option value="1">Interne</option><option value="2">Gouvernementale</option><option value="3">Privée</option><option value="4">ONG</option></select></div>
                    <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" id="editEmail" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">Téléphone</label><input type="tel" name="telephone" id="editTelephone" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">Adresse</label><input type="text" name="adresse" id="editAdresse" class="form-control"></div>
                </div></div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-warning" id="btnSubmitEdit"><span class="spinner-border spinner-border-sm d-none" id="spinnerEdit"></span> Mettre à jour</button></div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     MODAL EXPORT
══════════════════════════════════════════ --}}
<div class="modal fade" id="modalExport" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="fas fa-download text-success"></i> Exporter</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body text-center py-4"><p class="mb-4">Format d'export :</p><div class="d-grid gap-3">
            <a href="#" class="btn btn-outline-success btn-lg" id="exportExcel"><i class="fas fa-file-excel fa-2x mb-2"></i><br>Excel (.xlsx)</a>
            <a href="#" class="btn btn-outline-danger btn-lg" id="exportPDF"><i class="fas fa-file-pdf fa-2x mb-2"></i><br>PDF (.pdf)</a>
            <a href="#" class="btn btn-outline-primary btn-lg" id="exportCSV"><i class="fas fa-file-csv fa-2x mb-2"></i><br>CSV (.csv)</a>
        </div></div>
    </div></div>
</div>

</div> {{-- /.organisations-page --}}
@endsection

@push('js')
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
    const CSRF = $('meta[name="csrf-token"]').attr('content');
    toastr.options = { progressBar: true, positionClass: 'toast-top-right', timeOut: 3500, closeButton: true };
    
    const table = $('#organisationsTable').DataTable({
        responsive: true,
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json' },
        pageLength: 15, lengthMenu: [[15, 30, 50, -1], [15, 30, 50, 'Tous']],
        order: [[0, 'asc']], columnDefs: [{ orderable: false, targets: -1 }],
        dom: '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
        buttons: [
            { extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn-success btn-sm', exportOptions: { columns: [0,1,2,3,4] } },
            { extend: 'pdf', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn-danger btn-sm', exportOptions: { columns: [0,1,2,3,4] } },
            { extend: 'csv', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn-primary btn-sm', exportOptions: { columns: [0,1,2,3,4] } }
        ]
    });
    
    function applyFilters() {
        const search = $('#searchInput').val().toLowerCase();
        const type = $('.type-pill.active').data('type');
        const filterType = $('#filterType').val();
        const etat = $('#filterEtat').val();
        
        $.fn.dataTable.ext.search = [];
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            const row = $(table.row(dataIndex).node());
            const matchType = (!type && !filterType) || String(row.data('type')) === String(type) || String(row.data('type')) === String(filterType);
            const matchEtat = !etat || String(row.data('etat')) === String(etat);
            return matchType && matchEtat;
        });
        
        table.search(search).draw();
        updateCount();
    }
    
    function updateCount() {
        const count = table.rows({ search: 'applied' }).count();
        $('#tableCount').text(`${count} organisation${count !== 1 ? 's' : ''}`);
    }
    
    $('#searchInput').on('keyup', applyFilters);
    $('.type-pill').on('click', function() { $('.type-pill').removeClass('active'); $(this).addClass('active'); applyFilters(); });
    $('#filterType, #filterEtat').on('change', applyFilters);
    $('#btnResetFilters').on('click', function() {
        $('#searchInput').val(''); $('.type-pill').removeClass('active').first().addClass('active');
        $('#filterType, #filterEtat').val(''); $.fn.dataTable.ext.search = [];
        table.search('').columns().search('').draw(); updateCount();
    });
    
    $('#formCreate').on('submit', function(e) {
        e.preventDefault(); const $btn = $('#btnSubmitCreate'), $spinner = $('#spinnerCreate');
        $btn.prop('disabled', true); $spinner.removeClass('d-none');
        $.ajax({ url: $(this).attr('action'), method: 'POST', data: $(this).serialize(), headers: { 'X-CSRF-TOKEN': CSRF },
            success: res => { toastr.success(res.message); $('#modalCreate').modal('hide'); setTimeout(() => location.reload(), 800); },
            error: xhr => { Object.values(xhr.responseJSON?.errors || {}).flat().forEach(msg => toastr.error(msg)); toastr.error(xhr.responseJSON?.message || 'Erreur'); },
            complete: () => { $btn.prop('disabled', false); $spinner.addClass('d-none'); }
        });
    });
    
    $(document).on('click', '.btn-edit', function() {
        $.get(`/organisations/${$(this).data('id')}/edit`, function(data) {
            $('#editId').val(data.id); $('#editNom').val(data.nom); $('#editSigle').val(data.sigle);
            $('#editType').val(data.type); $('#editEmail').val(data.email); $('#editTelephone').val(data.telephone);
            $('#editAdresse').val(data.adresse); $('#formEdit').attr('action', `/organisations/${data.id}`);
            $('#modalEdit').modal('show');
        });
    });
    $('#formEdit').on('submit', function(e) {
        e.preventDefault(); const $btn = $('#btnSubmitEdit'), $spinner = $('#spinnerEdit');
        $btn.prop('disabled', true); $spinner.removeClass('d-none');
        $.ajax({ url: $(this).attr('action'), method: 'POST', data: $(this).serialize(), headers: { 'X-CSRF-TOKEN': CSRF },
            success: res => { toastr.success(res.message); $('#modalEdit').modal('hide'); setTimeout(() => location.reload(), 800); },
            error: xhr => { Object.values(xhr.responseJSON?.errors || {}).flat().forEach(msg => toastr.error(msg)); toastr.error(xhr.responseJSON?.message || 'Erreur'); },
            complete: () => { $btn.prop('disabled', false); $spinner.addClass('d-none'); }
        });
    });
    
    $(document).on('click', '.btn-services', function() { window.location.href = `/services?organisation_id=${$(this).data('id')}`; });
    
    $(document).on('click', '.btn-suspend', function() {
        const id = $(this).data('id');
        Swal.fire({ title: 'Désactiver ?', text: 'L\'organisation sera masquée des listes.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#64748b', confirmButtonText: 'Désactiver', cancelButtonText: 'Annuler' }).then(res => {
            if (res.isConfirmed) $.ajax({ url: `/organisations/${id}`, method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF }, success: r => { toastr.success(r.message); setTimeout(() => location.reload(), 800); }, error: x => toastr.error(x.responseJSON?.message || 'Erreur') });
        });
    });
    
    $(document).on('click', '.btn-restore', function() {
        const id = $(this).data('id');
        Swal.fire({ title: 'Réactiver ?', text: 'L\'organisation réapparaîtra dans les listes.', icon: 'question', showCancelButton: true, confirmButtonColor: '#009a44', confirmButtonText: 'Réactiver', cancelButtonText: 'Annuler' }).then(res => {
            if (res.isConfirmed) $.post(`/organisations/${id}/restaurer`, { _token: CSRF }, r => { toastr.success(r.message); setTimeout(() => location.reload(), 800); }).fail(x => toastr.error(x.responseJSON?.message || 'Erreur'));
        });
    });
    
    $('#exportExcel, #exportPDF, #exportCSV').on('click', function(e) {
        e.preventDefault();
        const format = this.id.replace('export', '').toLowerCase();
        const filters = { search: $('#searchInput').val(), type: $('.type-pill.active').data('type') || $('#filterType').val(), etat: $('#filterEtat').val() };
        window.location.href = `/organisations/export?format=${format}&filters=${encodeURIComponent(JSON.stringify(filters))}`;
    });
    
    $(document).on('click', '.action-trigger', function(e) { e.stopPropagation(); const $d = $(this).closest('.action-dropdown'), open = $d.hasClass('open'); $('.action-dropdown').removeClass('open'); if (!open) $d.addClass('open'); });
    $(document).on('click', function(e) { if (!$(e.target).closest('.action-dropdown').length) $('.action-dropdown').removeClass('open'); });
    $('.select2').select2({ width: '100%', dropdownParent: $(document.body) });
    
    @if(session('success')) toastr.success("{{ session('success') }}"); @endif
    @if(session('error')) toastr.error("{{ session('error') }}"); @endif
});
</script>
@endpush
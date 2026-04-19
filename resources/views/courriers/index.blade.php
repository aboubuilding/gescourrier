@extends('layouts.app')

@section('title', 'Courriers')
@section('page_title', 'Gestion des Courriers')
@section('page_icon', 'fa-envelope')

@section('breadcrumb')
    <li><a href="{{ route('dashboard.index') }}">Accueil</a></li>
    <li>Administration</li>
    <li>Courriers</li>
@endsection

@section('page_actions')
    <div class="d-flex gap-2">
        <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalExport">
            <i class="fas fa-download"></i> <span class="d-none d-sm-inline">Exporter</span>
        </button>
        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="fas fa-plus-circle"></i> <span class="d-none d-sm-inline">Nouveau courrier</span>
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

        /* Types */
        --entrant:       #2563eb;
        --entrant-pale:  #eff6ff;
        --sortant:       #ea580c;
        --sortant-pale:  #fff7ed;
        --interne:       #7c3aed;
        --interne-pale:  #f5f3ff;

        /* Priorités */
        --normale:       #16a34a;
        --normale-pale:  #f0fdf4;
        --urgente:       #d97706;
        --urgente-pale:  #fffbeb;
        --tres-urgente:  #dc2626;
        --tres-urgente-pale: #fef2f2;

        /* Statuts */
        --enregistre:    #0891b2;
        --enregistre-pale:#ecfeff;
        --affecte:       #7c3aed;
        --affecte-pale:  #f5f3ff;
        --archive:       #64748b;
        --archive-pale:  #f8fafc;
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
    .stat-card.entrant::after  { background: var(--entrant); }
    .stat-card.sortant::after  { background: var(--sortant); }
    .stat-card.interne::after  { background: var(--interne); }
    .stat-card.urgente::after  { background: var(--tres-urgente); }
    .stat-card.archive::after  { background: var(--archive); }

    .stat-icon {
        width: 46px; height: 46px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }
    .stat-icon.primaire { background: var(--primaire-pale); color: var(--primaire-deep); }
    .stat-icon.entrant  { background: var(--entrant-pale);  color: var(--entrant); }
    .stat-icon.sortant  { background: var(--sortant-pale);  color: var(--sortant); }
    .stat-icon.interne  { background: var(--interne-pale);  color: var(--interne); }
    .stat-icon.urgente  { background: var(--tres-urgente-pale); color: var(--tres-urgente); }
    .stat-icon.archive  { background: var(--archive-pale);  color: var(--archive); }

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
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
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

    /* Pills de type */
    .type-pills { display: flex; flex-wrap: wrap; gap: 8px; }
    .type-pill {
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
    .type-pill:hover {
        border-color: var(--primaire);
        color: var(--primaire);
        background: var(--primaire-pale);
    }
    .type-pill.active {
        background: var(--primaire);
        color: #fff;
        border-color: var(--primaire);
        box-shadow: 0 4px 12px rgba(0,154,68,0.25);
    }
    .type-pill[data-type="1"].active { background: var(--entrant); border-color: var(--entrant); }
    .type-pill[data-type="2"].active { background: var(--sortant); border-color: var(--sortant); }
    .type-pill[data-type="3"].active { background: var(--interne); border-color: var(--interne); }

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
    .dataTable tbody tr {
        transition: background 0.15s !important;
    }
    .dataTable tbody tr:hover {
        background: var(--primaire-muted) !important;
    }
    .dataTable tbody td {
        padding: 12px 16px !important;
        border-bottom: 1px solid var(--border) !important;
        color: var(--texte-2) !important;
        vertical-align: middle !important;
        font-size: 13px !important;
    }

    /* Cellule référence */
    .ref-cell { display: flex; align-items: center; gap: 10px; }
    .ref-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px;
        flex-shrink: 0;
    }
    .ref-icon.entrant  { background: var(--entrant-pale);  color: var(--entrant); }
    .ref-icon.sortant  { background: var(--sortant-pale);  color: var(--sortant); }
    .ref-icon.interne  { background: var(--interne-pale);  color: var(--interne); }
    .ref-code {
        font-weight: 700;
        color: var(--texte);
        font-size: 12.5px;
        font-family: 'SF Mono', 'Fira Code', monospace;
        letter-spacing: 0.3px;
    }
    .ref-date {
        font-size: 11px;
        color: var(--texte-3);
        margin-top: 2px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Objet */
    .objet-text {
        font-weight: 500;
        color: var(--texte);
        font-size: 13px;
        max-width: 240px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .objet-exp {
        font-size: 11px;
        color: var(--texte-3);
        margin-top: 2px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* Badges */
    .badge-type, .badge-prio, .badge-statut {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        white-space: nowrap;
    }
    .badge-type.entrant { background: var(--entrant-pale);  color: var(--entrant); }
    .badge-type.sortant { background: var(--sortant-pale);  color: var(--sortant); }
    .badge-type.interne { background: var(--interne-pale);  color: var(--interne); }
    
    .badge-prio.normale      { background: var(--normale-pale);       color: var(--normale); }
    .badge-prio.urgente      { background: var(--urgente-pale);       color: var(--urgente); }
    .badge-prio.tres-urgente { background: var(--tres-urgente-pale);  color: var(--tres-urgente); }
    
    .badge-statut.enregistre { background: var(--enregistre-pale); color: var(--enregistre); }
    .badge-statut.affecte    { background: var(--affecte-pale);    color: var(--affecte); }
    .badge-statut.archive    { background: var(--archive-pale);    color: var(--archive); }
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

    /* Upload file */
    .file-upload {
        border: 2px dashed var(--gris-3);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: var(--tr);
        background: var(--gris-1);
    }
    .file-upload:hover,
    .file-upload.dragover {
        border-color: var(--primaire);
        background: var(--primaire-pale);
    }
    .file-upload i {
        font-size: 24px;
        color: var(--primaire);
        margin-bottom: 8px;
    }
    .file-upload input[type="file"] {
        display: none;
    }
    .file-name {
        font-size: 12px;
        color: var(--texte-2);
        margin-top: 8px;
        font-weight: 500;
    }

    /* ═══════════════════════════════════════════
       📱 RESPONSIVE
    ═══════════════════════════════════════════ */
    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .filter-bar .row > div { margin-bottom: 12px; }
        .objet-text { max-width: 140px; }
        .modal-dialog { margin: 16px; }
    }
    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr; }
        .type-pills { justify-content: center; }
    }
</style>
@endsection

@section('contenu')

@php
    // KPI depuis le controller
    $total        = $stats['total'] ?? 0;
    $nbEntrants   = $stats['entrants'] ?? 0;
    $nbSortants   = $stats['sortants'] ?? 0;
    $nbInternes   = $stats['internes'] ?? 0;
    $nbTresUrgent = $stats['tres_urgents'] ?? 0;
    $nbArchives   = $stats['archives'] ?? 0;
    
    // Constants pour référence rapide
    $C = \App\Models\Courrier::class;
@endphp

{{-- ══════════════════════════════════════════
     KPI CARDS
══════════════════════════════════════════ --}}
<div class="stats-grid">
    <div class="stat-card" data-bs-toggle="modal" data-bs-target="#modalFilter" data-filter="all">
        <div class="stat-icon primaire"><i class="fas fa-envelope"></i></div>
        <div>
            <div class="stat-value">{{ $total }}</div>
            <div class="stat-label">Total</div>
        </div>
    </div>
    <div class="stat-card entrant" data-bs-toggle="modal" data-bs-target="#modalFilter" data-filter="type:{{ $C::TYPE_ENTRANT }}">
        <div class="stat-icon entrant"><i class="fas fa-arrow-down-left"></i></div>
        <div>
            <div class="stat-value">{{ $nbEntrants }}</div>
            <div class="stat-label">Entrants</div>
        </div>
    </div>
    <div class="stat-card sortant" data-bs-toggle="modal" data-bs-target="#modalFilter" data-filter="type:{{ $C::TYPE_SORTANT }}">
        <div class="stat-icon sortant"><i class="fas fa-arrow-up-right"></i></div>
        <div>
            <div class="stat-value">{{ $nbSortants }}</div>
            <div class="stat-label">Sortants</div>
        </div>
    </div>
    <div class="stat-card interne" data-bs-toggle="modal" data-bs-target="#modalFilter" data-filter="type:{{ $C::TYPE_INTERNE }}">
        <div class="stat-icon interne"><i class="fas fa-arrows-left-right"></i></div>
        <div>
            <div class="stat-value">{{ $nbInternes }}</div>
            <div class="stat-label">Internes</div>
        </div>
    </div>
    <div class="stat-card urgente" data-bs-toggle="modal" data-bs-target="#modalFilter" data-filter="priorite:{{ $C::PRIORITE_TRES_URGENTE }}">
        <div class="stat-icon urgente"><i class="fas fa-bolt"></i></div>
        <div>
            <div class="stat-value">{{ $nbTresUrgent }}</div>
            <div class="stat-label">Très urgents</div>
        </div>
    </div>
    <div class="stat-card archive" data-bs-toggle="modal" data-bs-target="#modalFilter" data-filter="statut:{{ $C::STATUT_ARCHIVE }}">
        <div class="stat-icon archive"><i class="fas fa-box-archive"></i></div>
        <div>
            <div class="stat-value">{{ $nbArchives }}</div>
            <div class="stat-label">Archivés</div>
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
            <input type="text" id="searchInput" class="filter-input" placeholder="Référence, objet, expéditeur…">
        </div>

        {{-- Filtre statut --}}
        <div class="col-md-2">
            <label class="filter-label">Statut</label>
            <select id="filterStatut" class="filter-select">
                <option value="">Tous</option>
                <option value="{{ $C::STATUT_ENREGISTRE }}">Enregistré</option>
                <option value="{{ $C::STATUT_AFFECTE }}">Affecté</option>
                <option value="{{ $C::STATUT_ARCHIVE }}">Archivé</option>
            </select>
        </div>

        {{-- Filtre priorité --}}
        <div class="col-md-2">
            <label class="filter-label">Priorité</label>
            <select id="filterPriorite" class="filter-select">
                <option value="">Toutes</option>
                <option value="{{ $C::PRIORITE_NORMALE }}">Normale</option>
                <option value="{{ $C::PRIORITE_URGENTE }}">Urgente</option>
                <option value="{{ $C::PRIORITE_TRES_URGENTE }}">Très urgente</option>
            </select>
        </div>

        {{-- Pills type --}}
        <div class="col-md-3">
            <label class="filter-label">Type</label>
            <div class="type-pills">
                <span class="type-pill active" data-type="">Tous</span>
                <span class="type-pill" data-type="{{ $C::TYPE_ENTRANT }}">Entrants</span>
                <span class="type-pill" data-type="{{ $C::TYPE_SORTANT }}">Sortants</span>
                <span class="type-pill" data-type="{{ $C::TYPE_INTERNE }}">Internes</span>
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
            <i class="fas fa-list"></i> Liste des Courriers
        </h5>
        <span class="count-badge" id="tableCount">{{ $total }} courrier{{ $total != 1 ? 's' : '' }}</span>
    </div>

    <div class="table-responsive">
        <table id="courriersTable" class="dataTable w-100">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Objet / Expéditeur</th>
                    <th>Type</th>
                    <th>Priorité</th>
                    <th>Service</th>
                    <th>Statut</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courriers as $courrier)
                @php
                    // 🔁 SOLUTION 2 : Conversion sécurisée array → objet
                    $c = is_array($courrier) ? (object) $courrier : $courrier;
                    
                    // Gestion des relations qui pourraient aussi être des arrays
                    $service = $c->service ?? null;
                    if (is_array($service)) $service = (object) $service;
                    
                    // ✅ FIX: Forcer les valeurs en entier pour éviter "Illegal offset type"
                    $typeVal = (int) ($c->type ?? $C::TYPE_INTERNE);
                    $prioVal = (int) ($c->priorite ?? $C::PRIORITE_NORMALE);
                    $statutVal = (int) ($c->statut ?? $C::STATUT_ENREGISTRE);
                    
                    // Maps pour l'affichage (avec constantes du modèle)
                    $typeMap = [
                        $C::TYPE_ENTRANT  => ['label'=>'Entrant','class'=>'entrant','icon'=>'fa-arrow-down-left'], 
                        $C::TYPE_SORTANT  => ['label'=>'Sortant','class'=>'sortant','icon'=>'fa-arrow-up-right'], 
                        $C::TYPE_INTERNE  => ['label'=>'Interne','class'=>'interne','icon'=>'fa-arrows-left-right']
                    ];
                    $prioMap = [
                        $C::PRIORITE_NORMALE    => ['label'=>'Normale','class'=>'normale','icon'=>'fa-circle-check'], 
                        $C::PRIORITE_URGENTE    => ['label'=>'Urgente','class'=>'urgente','icon'=>'fa-triangle-exclamation'], 
                        $C::PRIORITE_TRES_URGENTE => ['label'=>'Très urgente','class'=>'tres-urgente','icon'=>'fa-bolt']
                    ];
                    $statutMap = [
                        $C::STATUT_ENREGISTRE => ['label'=>'Enregistré','class'=>'enregistre'], 
                        $C::STATUT_AFFECTE    => ['label'=>'Affecté','class'=>'affecte'], 
                        $C::STATUT_TRAITE     => ['label'=>'Traité','class'=>'traite'],
                        $C::STATUT_ARCHIVE    => ['label'=>'Archivé','class'=>'archive']
                    ];
                    
                    // Récupération sécurisée avec fallback
                    $t = $typeMap[$typeVal] ?? ['label'=>'—','class'=>'interne','icon'=>'fa-envelope'];
                    $p = $prioMap[$prioVal] ?? ['label'=>'Normale','class'=>'normale','icon'=>'fa-circle-check'];
                    $s = $statutMap[$statutVal] ?? ['label'=>'—','class'=>'enregistre'];
                    
                    // Date de référence selon le type
                    $isEntrant = $typeVal === $C::TYPE_ENTRANT;
                    $dateRef = $isEntrant ? ($c->date_reception ?? null) : ($c->date_envoi ?? null);
                @endphp
                <tr data-id="{{ $c->id ?? '' }}"
                    data-type="{{ $typeVal }}"
                    data-priorite="{{ $prioVal }}"
                    data-statut="{{ $statutVal }}"
                    data-search="{{ strtolower(($c->reference ?? '').' '.($c->objet ?? '').' '.($c->expediteur ?? '')) }}">
                    
                    {{-- Référence --}}
                    <td>
                        <div class="ref-cell">
                            <div class="ref-icon {{ $t['class'] }}">
                                <i class="fas {{ $t['icon'] }}"></i>
                            </div>
                            <div>
                                <div class="ref-code">{{ $c->reference ?? '—' }}</div>
                                <div class="ref-date">
                                    <i class="fas fa-calendar"></i>
                                    {{ $dateRef ? \Carbon\Carbon::parse($dateRef)->format('d/m/Y') : '—' }}
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- Objet --}}
                    <td>
                        <div class="objet-text" title="{{ $c->objet ?? '' }}">{{ $c->objet ?? '—' }}</div>
                        <div class="objet-exp">
                            <i class="fas fa-user"></i>
                            {{ $c->expediteur ?? '—' }}
                        </div>
                    </td>

                    {{-- Type --}}
                    <td><span class="badge-type {{ $t['class'] }}"><i class="fas {{ $t['icon'] }}"></i>{{ $t['label'] }}</span></td>

                    {{-- Priorité --}}
                    <td><span class="badge-prio {{ $p['class'] }}"><i class="fas {{ $p['icon'] }}"></i>{{ $p['label'] }}</span></td>

                    {{-- Service --}}
                    <td>
                        @if($service && !empty($service->nom))
                            <span class="service-chip" title="{{ $service->nom ?? '' }}">
                                <i class="fas fa-network-wired"></i>
                                {{ $service->nom ?? '—' }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    {{-- Statut --}}
                    <td><span class="badge-statut {{ $s['class'] }}">{{ $s['label'] }}</span></td>

                    {{-- Actions --}}
                    <td class="text-center">
                        <div class="action-dropdown">
                            <button class="action-trigger"><i class="fas fa-ellipsis-v"></i></button>
                            <div class="action-menu">
                                <a href="{{ route('courriers.show', $c->id ?? '#') }}" class="action-item">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                                @if($statutVal == $C::STATUT_ENREGISTRE)
                                <button class="action-item btn-edit" data-id="{{ $c->id ?? '' }}">
                                    <i class="fas fa-pen"></i> Modifier
                                </button>
                                <button class="action-item btn-affecter" data-id="{{ $c->id ?? '' }}">
                                    <i class="fas fa-share"></i> Affecter
                                </button>
                                @endif
                                @if($statutVal != $C::STATUT_ARCHIVE)
                                <button class="action-item btn-archiver" data-id="{{ $c->id ?? '' }}">
                                    <i class="fas fa-box-archive"></i> Archiver
                                </button>
                                @endif
                                <div class="action-divider"></div>
                                @if($statutVal == $C::STATUT_ENREGISTRE)
                                <button class="action-item danger btn-delete" data-id="{{ $c->id ?? '' }}">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><div class="empty-state"><i class="fas fa-inbox"></i><p>Aucun courrier enregistré</p></div></td></tr>
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
            <form id="formCreate" action="{{ route('courriers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle text-danger"></i> Nouveau courrier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Type & Priorité --}}
                        <div class="col-md-6">
                            <label class="form-label">Type de courrier *</label>
                            <select name="type" class="form-select" required>
                                <option value="">Sélectionner</option>
                                <option value="{{ $C::TYPE_ENTRANT }}">Entrant</option>
                                <option value="{{ $C::TYPE_SORTANT }}">Sortant</option>
                                <option value="{{ $C::TYPE_INTERNE }}">Interne</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Priorité *</label>
                            <select name="priorite" class="form-select" required>
                                <option value="{{ $C::PRIORITE_NORMALE }}">Normale</option>
                                <option value="{{ $C::PRIORITE_URGENTE }}">Urgente</option>
                                <option value="{{ $C::PRIORITE_TRES_URGENTE }}">Très urgente</option>
                            </select>
                        </div>

                        {{-- Référence & Numéro --}}
                        <div class="col-md-6">
                            <label class="form-label">Référence</label>
                            <input type="text" name="reference" class="form-control" placeholder="Ex: REF-2024-001">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Numéro</label>
                            <input type="text" name="numero" class="form-control" placeholder="Ex: N°12345">
                        </div>

                        {{-- Objet --}}
                        <div class="col-12">
                            <label class="form-label">Objet *</label>
                            <input type="text" name="objet" class="form-control" required placeholder="Objet du courrier">
                        </div>

                        {{-- Description --}}
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Description détaillée…"></textarea>
                        </div>

                        {{-- Expéditeur / Destinataire --}}
                        <div class="col-md-6">
                            <label class="form-label">Expéditeur</label>
                            <input type="text" name="expediteur" class="form-control" placeholder="Nom de l'expéditeur">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Destinataire</label>
                            <input type="text" name="destinataire" class="form-control" placeholder="Nom du destinataire">
                        </div>

                        {{-- Dates --}}
                        <div class="col-md-6">
                            <label class="form-label">Date de réception *</label>
                            <input type="date" name="date_reception" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date d'envoi</label>
                            <input type="date" name="date_envoi" class="form-control">
                        </div>

                        {{-- Service & Organisation --}}
                        <div class="col-md-6">
                            <label class="form-label">Service destinataire</label>
                            <select name="service_id" class="form-select select2">
                                <option value="">Sélectionner</option>
                                @foreach($services as $service)
                                <option value="{{ $service->id }}">{{ $service->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Organisation</label>
                            <select name="organisation_id" class="form-select select2">
                                <option value="">Sélectionner</option>
                                @foreach($organisations as $org)
                                <option value="{{ $org->id }}">{{ $org->nom }} @if($org->sigle)({{ $org->sigle }})@endif</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Fichier scanné --}}
                        <div class="col-12">
                            <label class="form-label">Fichier scanné</label>
                            <label class="file-upload" id="fileDrop">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <div>Glissez-déposez ou cliquez pour sélectionner</div>
                                <div class="form-text">PDF, JPG, PNG • Max 10 Mo</div>
                                <input type="file" name="fichier" id="fileInput" accept=".pdf,.jpg,.jpeg,.png">
                                <div class="file-name" id="fileName"></div>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger" id="btnSubmitCreate">
                        <span class="spinner-border spinner-border-sm d-none" id="spinnerCreate"></span>
                        Créer le courrier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     MODAL MODIFICATION (identique à création, pré-rempli)
══════════════════════════════════════════ --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formEdit" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-pen text-warning"></i> Modifier le courrier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- Même structure que modalCreate, avec champs pré-remplis via JS --}}
                    <div class="row g-3">
                        <input type="hidden" name="id" id="editId">
                        <div class="col-md-6">
                            <label class="form-label">Type *</label>
                            <select name="type" id="editType" class="form-select" required>
                                <option value="{{ $C::TYPE_ENTRANT }}">Entrant</option>
                                <option value="{{ $C::TYPE_SORTANT }}">Sortant</option>
                                <option value="{{ $C::TYPE_INTERNE }}">Interne</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Priorité *</label>
                            <select name="priorite" id="editPriorite" class="form-select" required>
                                <option value="{{ $C::PRIORITE_NORMALE }}">Normale</option>
                                <option value="{{ $C::PRIORITE_URGENTE }}">Urgente</option>
                                <option value="{{ $C::PRIORITE_TRES_URGENTE }}">Très urgente</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Référence</label>
                            <input type="text" name="reference" id="editReference" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Numéro</label>
                            <input type="text" name="numero" id="editNumero" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Objet *</label>
                            <input type="text" name="objet" id="editObjet" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="editDescription" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Expéditeur</label>
                            <input type="text" name="expediteur" id="editExpediteur" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Destinataire</label>
                            <input type="text" name="destinataire" id="editDestinataire" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date de réception *</label>
                            <input type="date" name="date_reception" id="editDateReception" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date d'envoi</label>
                            <input type="date" name="date_envoi" id="editDateEnvoi" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Service</label>
                            <select name="service_id" id="editService" class="form-select select2">
                                <option value="">Sélectionner</option>
                                @foreach($services as $service)
                                <option value="{{ $service->id }}">{{ $service->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Organisation</label>
                            <select name="organisation_id" id="editOrganisation" class="form-select select2">
                                <option value="">Sélectionner</option>
                                @foreach($organisations as $org)
                                <option value="{{ $org->id }}">{{ $org->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Fichier actuel</label>
                            <div id="editFileCurrent" class="text-muted small mb-2">Aucun fichier</div>
                            <label class="file-upload">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <div>Remplacer le fichier</div>
                                <input type="file" name="fichier" id="editFileInput" accept=".pdf,.jpg,.jpeg,.png">
                            </label>
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
     MODAL AFFECTATION
══════════════════════════════════════════ --}}
<div class="modal fade" id="modalAffecter" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formAffecter" method="POST">
                @csrf
                <input type="hidden" name="courrier_id" id="affecterCourrierId">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-share text-primary"></i> Affecter le courrier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Sélectionnez l'agent et le service destinataire.</p>
                    <div class="mb-3">
                        <label class="form-label">Agent responsable *</label>
                        <select name="agent_id" class="form-select select2" required>
                            <option value="">Sélectionner un agent</option>
                            @foreach($agents as $agent)
                            <option value="{{ $agent->user_id ?? '' }}">{{ $agent->nom ?? '' }} {{ $agent->prenom ?? '' }} @if(!empty($agent->fonction))({{ $agent->fonction }})@endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Service *</label>
                        <select name="service_id" class="form-select select2" required>
                            <option value="">Sélectionner un service</option>
                            @foreach($services as $service)
                            <option value="{{ $service->id }}">{{ $service->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note d'affectation</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Instructions complémentaires…"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm d-none" id="spinnerAffecter"></span>
                        Affecter
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
                <h5 class="modal-title"><i class="fas fa-download text-success"></i> Exporter les courriers</h5>
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
    const table = $('#courriersTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
        },
        pageLength: 15,
        lengthMenu: [[15, 30, 50, -1], [15, 30, 50, 'Tous']],
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: -1 } // Désactiver tri sur Actions
        ],
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
        const type = $('.type-pill.active').data('type');
        const statut = $('#filterStatut').val();
        const priorite = $('#filterPriorite').val();
        
        table.columns().every(function() {
            const col = this;
            if (col.index() === 0) { // Référence + Objet
                col.search(search).draw();
            }
        });
        
        // Filtres personnalisés
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            const row = $(table.row(dataIndex).node());
            const matchType = !type || row.data('type') == type;
            const matchStatut = !statut || row.data('statut') == statut;
            const matchPriorite = !priorite || row.data('priorite') == priorite;
            return matchType && matchStatut && matchPriorite;
        });
        
        table.draw();
        updateCount();
    }
    
    function updateCount() {
        const count = table.rows({ search: 'applied' }).count();
        $('#tableCount').text(`${count} courrier${count != 1 ? 's' : ''}`);
    }
    
    // Événements filtres
    $('#searchInput').on('keyup', applyFilters);
    $('.type-pill').on('click', function() {
        $('.type-pill').removeClass('active');
        $(this).addClass('active');
        applyFilters();
    });
    $('#filterStatut, #filterPriorite').on('change', applyFilters);
    $('#btnResetFilters').on('click', function() {
        $('#searchInput').val('');
        $('.type-pill').removeClass('active').first().addClass('active');
        $('#filterStatut, #filterPriorite').val('');
        $.fn.dataTable.ext.search = [];
        table.search('').columns().search('').draw();
        updateCount();
    });
    
    // ═══════════════════════════════════════
    // 🪟 MODALS & FORMS
    // ═══════════════════════════════════════
    
    // Upload file drag & drop
    const $fileDrop = $('#fileDrop');
    const $fileInput = $('#fileInput');
    const $fileName = $('#fileName');
    
    $fileDrop.on('click', () => $fileInput.click());
    $fileInput.on('change', function() {
        if (this.files[0]) {
            $fileName.text(this.files[0].name);
            $fileDrop.addClass('dragover');
        }
    });
    $fileDrop.on('dragover dragenter', e => {
        e.preventDefault();
        $fileDrop.addClass('dragover');
    }).on('dragleave dragend drop', e => {
        e.preventDefault();
        $fileDrop.removeClass('dragover');
    }).on('drop', function(e) {
        const files = e.originalEvent.dataTransfer.files;
        if (files[0]) {
            $fileInput[0].files = files;
            $fileName.text(files[0].name);
        }
    });
    
    // Création AJAX
    $('#formCreate').on('submit', function(e) {
        e.preventDefault();
        const $btn = $('#btnSubmitCreate');
        const $spinner = $('#spinnerCreate');
        
        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');
        
        const formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
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
        $.get(`/courriers/${id}/edit`, function(data) {
            $('#editId').val(data.id);
            $('#editType').val(data.type);
            $('#editPriorite').val(data.priorite);
            $('#editReference').val(data.reference);
            $('#editNumero').val(data.numero);
            $('#editObjet').val(data.objet);
            $('#editDescription').val(data.description);
            $('#editExpediteur').val(data.expediteur);
            $('#editDestinataire').val(data.destinataire);
            $('#editDateReception').val(data.date_reception);
            $('#editDateEnvoi').val(data.date_envoi);
            $('#editService').val(data.service_id).trigger('change');
            $('#editOrganisation').val(data.organisation_id).trigger('change');
            $('#editFileCurrent').text(data.fichier_nom_original || 'Aucun fichier');
            $('#formEdit').attr('action', `/courriers/${id}`);
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
        
        const formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
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
    
    // Affectation
    $(document).on('click', '.btn-affecter', function() {
        $('#affecterCourrierId').val($(this).data('id'));
        $('#modalAffecter').modal('show');
    });
    
    $('#formAffecter').on('submit', function(e) {
        e.preventDefault();
        const $btn = $(this).find('button[type="submit"]');
        const $spinner = $('#spinnerAffecter');
        const id = $('#affecterCourrierId').val();
        
        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');
        
        $.ajax({
            url: `/courriers/${id}/affecter`,
            method: 'POST',
            data: $(this).serialize(),
            headers: { 'X-CSRF-TOKEN': CSRF },
            success: function(res) {
                toastr.success(res.message);
                $('#modalAffecter').modal('hide');
                setTimeout(() => location.reload(), 800);
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Erreur lors de l\'affectation');
            },
            complete: function() {
                $btn.prop('disabled', false);
                $spinner.addClass('d-none');
            }
        });
    });
    
    // Archivage
    $(document).on('click', '.btn-archiver', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Archiver ce courrier ?',
            text: 'Cette action est irréversible.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#64748b',
            confirmButtonText: 'Archiver',
            cancelButtonText: 'Annuler'
        }).then(result => {
            if (result.isConfirmed) {
                $.post(`/courriers/${id}/archiver`, { _token: CSRF }, function(res) {
                    toastr.success(res.message);
                    setTimeout(() => location.reload(), 800);
                }).fail(xhr => toastr.error(xhr.responseJSON?.message || 'Erreur'));
            }
        });
    });
    
    // Suppression
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Supprimer définitivement ?',
            text: 'Cette action est irréversible.',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#c0392b',
            confirmButtonText: 'Supprimer',
            cancelButtonText: 'Annuler'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/courriers/${id}`,
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF },
                    success: function(res) {
                        toastr.success(res.message);
                        table.row($(`tr[data-id="${id}"]`)).remove().draw();
                        updateCount();
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Erreur lors de la suppression');
                    }
                });
            }
        });
    });
    
    // Export
    $('#exportExcel').on('click', function(e) {
        e.preventDefault();
        window.location.href = `/courriers/export?format=xlsx&filters=${encodeURIComponent(JSON.stringify(getActiveFilters()))}`;
    });
    $('#exportPDF').on('click', function(e) {
        e.preventDefault();
        window.location.href = `/courriers/export?format=pdf&filters=${encodeURIComponent(JSON.stringify(getActiveFilters()))}`;
    });
    $('#exportCSV').on('click', function(e) {
        e.preventDefault();
        window.location.href = `/courriers/export?format=csv&filters=${encodeURIComponent(JSON.stringify(getActiveFilters()))}`;
    });
    
    function getActiveFilters() {
        return {
            search: $('#searchInput').val(),
            type: $('.type-pill.active').data('type'),
            statut: $('#filterStatut').val(),
            priorite: $('#filterPriorite').val()
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
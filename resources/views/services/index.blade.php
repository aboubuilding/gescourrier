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

@push('css')
<link rel="stylesheet" href="{{ asset('app/assets/plugins/datatables/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('app/assets/plugins/datatables/buttons.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('app/assets/plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<style>
    /* ✅ Hérite des variables de layout.css — pas de :root personnalisé */
    .services-page .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 16px; margin-bottom: 24px; }
    .services-page .stat-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 18px 20px; display: flex; align-items: center; gap: 14px; box-shadow: var(--shadow-sm); transition: var(--transition); position: relative; overflow: hidden; cursor: pointer; }
    .services-page .stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); border-color: var(--success); }
    .services-page .stat-card::after { content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: var(--success); border-radius: 0 0 var(--radius-lg) var(--radius-lg); opacity: 0; transition: opacity 0.2s; }
    .services-page .stat-card:hover::after { opacity: 1; }
    .services-page .stat-card.actif::after { background: var(--success); }
    .services-page .stat-card.inactif::after { background: var(--text-muted); }
    .services-page .stat-icon { width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; background: var(--success); color: #fff; }
    .services-page .stat-icon.primaire { background: var(--success); color: #fff; }
    .services-page .stat-icon.actif { background: var(--success); color: #fff; }
    .services-page .stat-icon.inactif { background: var(--text-muted); color: #fff; }
    .services-page .stat-value { font-size: 24px; font-weight: 800; color: var(--text-primary); line-height: 1; }
    .services-page .stat-label { font-size: 11px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 3px; }
    .services-page .filter-bar { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 20px; margin-bottom: 24px; box-shadow: var(--shadow-sm); }
    .services-page .filter-label { font-size: 11px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase; color: var(--text-muted); margin-bottom: 8px; display: block; }
    .services-page .filter-input, .services-page .filter-select { width: 100%; background: var(--bg-hover); border: 1.5px solid var(--border); border-radius: 10px; padding: 11px 14px; font-size: 13px; color: var(--text-primary); outline: none; transition: var(--transition); font-family: inherit; }
    .services-page .filter-input:focus, .services-page .filter-select:focus { border-color: var(--success); background: var(--bg-card); box-shadow: 0 0 0 4px var(--success); }
    .services-page .filter-select { appearance: none; background-image: url("image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e"); background-repeat: no-repeat; background-position: right 12px center; background-size: 12px; padding-right: 36px; cursor: pointer; }
    .services-page .btn-filter-reset { background: var(--bg-hover); border: 1.5px solid var(--border); border-radius: 10px; padding: 11px 18px; font-size: 13px; font-weight: 600; color: var(--text-secondary); cursor: pointer; transition: var(--transition); display: flex; align-items: center; gap: 6px; white-space: nowrap; }
    .services-page .btn-filter-reset:hover { border-color: var(--success); color: var(--success); background: var(--success); }
    .services-page .table-panel { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); overflow: hidden; }
    .services-page .table-panel-head { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; background: var(--bg-hover); }
    .services-page .table-panel-title { font-size: 14px; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 8px; }
    .services-page .table-panel-title i { color: var(--success); }
    .services-page .count-badge { background: var(--success); color: #fff; border: 1px solid var(--success); font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 20px; }
    .services-page .dataTable thead th { background: var(--bg-hover) !important; color: var(--text-muted) !important; font-size: 11px !important; font-weight: 700 !important; text-transform: uppercase !important; letter-spacing: 0.6px !important; border-bottom: 2px solid var(--success) !important; white-space: nowrap !important; cursor: pointer !important; }
    .services-page .dataTable tbody tr { transition: background 0.15s !important; }
    .services-page .dataTable tbody tr:hover { background: var(--success) !important; }
    .services-page .dataTable tbody td { padding: 12px 16px !important; border-bottom: 1px solid var(--border) !important; color: var(--text-secondary) !important; vertical-align: middle !important; font-size: 13px !important; }
    .services-page .service-cell { display: flex; align-items: center; gap: 12px; }
    .services-page .service-icon { width: 40px; height: 40px; border-radius: 10px; background: var(--success); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; flex-shrink: 0; }
    .services-page .service-info .service-name { font-weight: 600; color: var(--text-primary); font-size: 13px; }
    .services-page .service-info .service-org { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
    .services-page .badge-statut { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px; white-space: nowrap; }
    .services-page .badge-statut.actif { background: var(--success); color: #fff; }
    .services-page .badge-statut.inactif { background: var(--text-muted); color: #fff; }
    .services-page .badge-statut::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: currentColor; margin-right: 4px; }
    .services-page .org-chip { display: inline-flex; align-items: center; gap: 5px; background: var(--bg-hover); color: var(--text-secondary); font-size: 11.5px; font-weight: 500; padding: 4px 10px; border-radius: 8px; border: 1px solid var(--border); max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .services-page .agents-badge { display: inline-flex; align-items: center; gap: 4px; background: var(--success); color: #fff; font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 6px; }
    .services-page .action-dropdown { position: relative; display: inline-block; }
    .services-page .action-trigger { width: 34px; height: 34px; border-radius: 10px; background: var(--bg-card); border: 1.5px solid var(--border); display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 14px; color: var(--text-muted); transition: var(--transition); }
    .services-page .action-trigger:hover, .services-page .action-dropdown.open .action-trigger { border-color: var(--success); color: var(--success); background: var(--success); }
    .services-page .action-menu { position: absolute; right: 0; top: calc(100% + 4px); min-width: 190px; background: var(--bg-card); border-radius: 12px; box-shadow: var(--shadow-lg); padding: 6px; z-index: 1000; opacity: 0; visibility: hidden; transform: translateY(-6px); transition: var(--transition); border-top: 3px solid var(--success); pointer-events: none; }
    .services-page .action-dropdown.open .action-menu { opacity: 1; visibility: visible; transform: translateY(0); pointer-events: auto; }
    .services-page .action-item { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 10px; font-size: 13px; font-weight: 500; color: var(--text-secondary); text-decoration: none; cursor: pointer; transition: var(--transition); background: none; border: none; width: 100%; text-align: left; }
    .services-page .action-item i { width: 16px; text-align: center; color: var(--text-muted); font-size: 13px; }
    .services-page .action-item:hover { background: var(--success); color: #fff; }
    .services-page .action-item:hover i { color: #fff; }
    .services-page .action-item.danger { color: var(--danger); }
    .services-page .action-item.danger i { color: var(--danger); }
    .services-page .action-item.danger:hover { background: var(--danger); }
    .services-page .action-divider { height: 1px; background: var(--border); margin: 5px 0; }
    .services-page .empty-state { text-align: center; padding: 60px 20px; color: var(--text-muted); }
    .services-page .empty-state i { font-size: 42px; opacity: 0.3; margin-bottom: 16px; display: block; }
    .services-page .empty-state p { font-size: 14px; margin: 0; }
    .services-page .modal-content { border: none; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); }
    .services-page .modal-header { border-bottom: 1px solid var(--border); padding: 18px 24px; }
    .services-page .modal-title { font-size: 16px; font-weight: 700; color: var(--text-primary); }
    .services-page .modal-body { padding: 24px; }
    .services-page .modal-footer { border-top: 1px solid var(--border); padding: 16px 24px; }
    .services-page .form-label { font-size: 12px; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px; }
    .services-page .form-control, .services-page .form-select { border: 1.5px solid var(--border); border-radius: 10px; padding: 11px 14px; font-size: 14px; color: var(--text-primary); transition: var(--transition); }
    .services-page .form-control:focus, .services-page .form-select:focus { border-color: var(--success); box-shadow: 0 0 0 4px var(--success); }
    @media (max-width: 768px) { .services-page .stats-grid { grid-template-columns: repeat(2, 1fr); } .services-page .filter-bar .row > div { margin-bottom: 12px; } .services-page .service-info .service-org { display: none; } .services-page .modal-dialog { margin: 16px; } }
    @media (max-width: 480px) { .services-page .stats-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('contenu')
@php
    // ✅ Définition CENTRALISÉE des variables
    $total      = $stats['total'] ?? 0;
    $actifs     = $stats['actifs'] ?? 0;
    $inactifs   = $stats['inactifs'] ?? 0;
    $totalAgents = $stats['total_agents'] ?? 0;
    $S = \App\Models\Service::class;
@endphp

<div class="services-page">
    @include('services.partials._stats-filters', compact('total', 'actifs', 'inactifs', 'totalAgents', 'organisations', 'S'))
    @include('services.partials._datatable', compact('services', 'total', 'S'))
    @include('services.partials._modals', compact('organisations'))
</div>
@endsection

@push('js')
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
<script src="{{ asset('js/services/index.js?v='.filemtime(public_path('js/services/index.js'))) }}"></script>
@endpush
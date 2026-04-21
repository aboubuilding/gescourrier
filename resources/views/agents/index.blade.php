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

@push('css')
<link rel="stylesheet" href="{{ asset('app/assets/plugins/datatables/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('app/assets/plugins/datatables/buttons.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('app/assets/plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<style>
    /* ✅ Hérite des variables de layout.css — pas de :root personnalisé */
    .agents-page .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 16px; margin-bottom: 24px; }
    .agents-page .stat-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 18px 20px; display: flex; align-items: center; gap: 14px; box-shadow: var(--shadow-sm); transition: var(--transition); position: relative; overflow: hidden; cursor: pointer; }
    .agents-page .stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); border-color: var(--success); }
    .agents-page .stat-card::after { content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: var(--success); border-radius: 0 0 var(--radius-lg) var(--radius-lg); opacity: 0; transition: opacity 0.2s; }
    .agents-page .stat-card:hover::after { opacity: 1; }
    .agents-page .stat-card.actif::after { background: var(--success); }
    .agents-page .stat-card.inactif::after { background: var(--text-muted); }
    .agents-page .stat-card.chef::after { background: var(--warning); }
    .agents-page .stat-card.service::after { background: var(--info); }
    .agents-page .stat-icon { width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; background: var(--success); color: #fff; }
    .agents-page .stat-icon.primaire { background: var(--success); color: #fff; }
    .agents-page .stat-icon.actif { background: var(--success); color: #fff; }
    .agents-page .stat-icon.inactif { background: var(--text-muted); color: #fff; }
    .agents-page .stat-icon.chef { background: var(--warning); color: #fff; }
    .agents-page .stat-icon.service { background: var(--info); color: #fff; }
    .agents-page .stat-value { font-size: 24px; font-weight: 800; color: var(--text-primary); line-height: 1; }
    .agents-page .stat-label { font-size: 11px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 3px; }
    .agents-page .filter-bar { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 20px; margin-bottom: 24px; box-shadow: var(--shadow-sm); }
    .agents-page .filter-label { font-size: 11px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase; color: var(--text-muted); margin-bottom: 8px; display: block; }
    .agents-page .filter-input, .agents-page .filter-select { width: 100%; background: var(--bg-hover); border: 1.5px solid var(--border); border-radius: 10px; padding: 11px 14px; font-size: 13px; color: var(--text-primary); outline: none; transition: var(--transition); font-family: inherit; }
    .agents-page .filter-input:focus, .agents-page .filter-select:focus { border-color: var(--success); background: var(--bg-card); box-shadow: 0 0 0 4px var(--success); }
    .agents-page .filter-select { appearance: none; background-image: url("image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e"); background-repeat: no-repeat; background-position: right 12px center; background-size: 12px; padding-right: 36px; cursor: pointer; }
    .agents-page .btn-filter-reset { background: var(--bg-hover); border: 1.5px solid var(--border); border-radius: 10px; padding: 11px 18px; font-size: 13px; font-weight: 600; color: var(--text-secondary); cursor: pointer; transition: var(--transition); display: flex; align-items: center; gap: 6px; white-space: nowrap; }
    .agents-page .btn-filter-reset:hover { border-color: var(--success); color: var(--success); background: var(--success); }
    .agents-page .function-pills { display: flex; flex-wrap: wrap; gap: 8px; }
    .agents-page .function-pill { padding: 7px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; cursor: pointer; border: 1.5px solid var(--border); background: var(--bg-hover); color: var(--text-secondary); transition: var(--transition); user-select: none; }
    .agents-page .function-pill:hover { border-color: var(--success); color: var(--success); background: var(--success); }
    .agents-page .function-pill.active { background: var(--success); color: #fff; border-color: var(--success); box-shadow: 0 4px 12px var(--success); }
    .agents-page .function-pill[data-function="chef"].active { background: var(--warning); border-color: var(--warning); }
    .agents-page .function-pill[data-function="secretaire"].active { background: var(--info); border-color: var(--info); }
    .agents-page .function-pill[data-function="gestionnaire"].active { background: var(--danger); border-color: var(--danger); }
    .agents-page .function-pill[data-function="agent"].active { background: var(--success); border-color: var(--success); }
    .agents-page .table-panel { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); overflow: hidden; }
    .agents-page .table-panel-head { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; background: var(--bg-hover); }
    .agents-page .table-panel-title { font-size: 14px; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 8px; }
    .agents-page .table-panel-title i { color: var(--success); }
    .agents-page .count-badge { background: var(--success); color: #fff; border: 1px solid var(--success); font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 20px; }
    .agents-page .dataTable thead th { background: var(--bg-hover) !important; color: var(--text-muted) !important; font-size: 11px !important; font-weight: 700 !important; text-transform: uppercase !important; letter-spacing: 0.6px !important; border-bottom: 2px solid var(--success) !important; white-space: nowrap !important; cursor: pointer !important; }
    .agents-page .dataTable tbody tr { transition: background 0.15s !important; }
    .agents-page .dataTable tbody tr:hover { background: var(--success) !important; }
    .agents-page .dataTable tbody td { padding: 12px 16px !important; border-bottom: 1px solid var(--border) !important; color: var(--text-secondary) !important; vertical-align: middle !important; font-size: 13px !important; }
    .agents-page .agent-cell { display: flex; align-items: center; gap: 12px; }
    .agents-page .agent-avatar { width: 40px; height: 40px; border-radius: 10px; background: var(--success); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; flex-shrink: 0; text-transform: uppercase; }
    .agents-page .agent-info .agent-name { font-weight: 600; color: var(--text-primary); font-size: 13px; }
    .agents-page .agent-info .agent-email { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
    .agents-page .badge-function, .agents-page .badge-statut { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px; white-space: nowrap; }
    .agents-page .badge-function.chef { background: var(--warning); color: #fff; }
    .agents-page .badge-function.secretaire { background: var(--info); color: #fff; }
    .agents-page .badge-function.gestionnaire { background: var(--danger); color: #fff; }
    .agents-page .badge-function.agent { background: var(--success); color: #fff; }
    .agents-page .badge-statut.actif { background: var(--success); color: #fff; }
    .agents-page .badge-statut.inactif { background: var(--text-muted); color: #fff; }
    .agents-page .badge-statut.suspendu { background: var(--danger); color: #fff; }
    .agents-page .badge-statut::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: currentColor; margin-right: 4px; }
    .agents-page .service-chip { display: inline-flex; align-items: center; gap: 5px; background: var(--bg-hover); color: var(--text-secondary); font-size: 11.5px; font-weight: 500; padding: 4px 10px; border-radius: 8px; border: 1px solid var(--border); max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .agents-page .action-dropdown { position: relative; display: inline-block; }
    .agents-page .action-trigger { width: 34px; height: 34px; border-radius: 10px; background: var(--bg-card); border: 1.5px solid var(--border); display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 14px; color: var(--text-muted); transition: var(--transition); }
    .agents-page .action-trigger:hover, .agents-page .action-dropdown.open .action-trigger { border-color: var(--success); color: var(--success); background: var(--success); }
    .agents-page .action-menu { position: absolute; right: 0; top: calc(100% + 4px); min-width: 190px; background: var(--bg-card); border-radius: 12px; box-shadow: var(--shadow-lg); padding: 6px; z-index: 1000; opacity: 0; visibility: hidden; transform: translateY(-6px); transition: var(--transition); border-top: 3px solid var(--success); pointer-events: none; }
    .agents-page .action-dropdown.open .action-menu { opacity: 1; visibility: visible; transform: translateY(0); pointer-events: auto; }
    .agents-page .action-item { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 10px; font-size: 13px; font-weight: 500; color: var(--text-secondary); text-decoration: none; cursor: pointer; transition: var(--transition); background: none; border: none; width: 100%; text-align: left; }
    .agents-page .action-item i { width: 16px; text-align: center; color: var(--text-muted); font-size: 13px; }
    .agents-page .action-item:hover { background: var(--success); color: #fff; }
    .agents-page .action-item:hover i { color: #fff; }
    .agents-page .action-item.danger { color: var(--danger); }
    .agents-page .action-item.danger i { color: var(--danger); }
    .agents-page .action-item.danger:hover { background: var(--danger); }
    .agents-page .action-divider { height: 1px; background: var(--border); margin: 5px 0; }
    .agents-page .empty-state { text-align: center; padding: 60px 20px; color: var(--text-muted); }
    .agents-page .empty-state i { font-size: 42px; opacity: 0.3; margin-bottom: 16px; display: block; }
    .agents-page .empty-state p { font-size: 14px; margin: 0; }
    .agents-page .modal-content { border: none; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); }
    .agents-page .modal-header { border-bottom: 1px solid var(--border); padding: 18px 24px; }
    .agents-page .modal-title { font-size: 16px; font-weight: 700; color: var(--text-primary); }
    .agents-page .modal-body { padding: 24px; }
    .agents-page .modal-footer { border-top: 1px solid var(--border); padding: 16px 24px; }
    .agents-page .form-label { font-size: 12px; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px; }
    .agents-page .form-control, .agents-page .form-select { border: 1.5px solid var(--border); border-radius: 10px; padding: 11px 14px; font-size: 14px; color: var(--text-primary); transition: var(--transition); }
    .agents-page .form-control:focus, .agents-page .form-select:focus { border-color: var(--success); box-shadow: 0 0 0 4px var(--success); }
    @media (max-width: 768px) { .agents-page .stats-grid { grid-template-columns: repeat(2, 1fr); } .agents-page .filter-bar .row > div { margin-bottom: 12px; } .agents-page .agent-info .agent-email { display: none; } .agents-page .modal-dialog { margin: 16px; } }
    @media (max-width: 480px) { .agents-page .stats-grid { grid-template-columns: 1fr; } .agents-page .function-pills { justify-content: center; } }
</style>
@endpush

@section('contenu')
@php
    // ✅ Définition CENTRALISÉE des variables
    $total      = $stats['total'] ?? 0;
    $actifs     = $stats['actifs'] ?? 0;
    $inactifs   = $stats['inactifs'] ?? 0;
    $chefs      = $stats['chefs'] ?? 0;
    $sansUser   = $stats['sans_user'] ?? 0;
@endphp

<div class="agents-page">
    @include('agents.partials._stats-filters', compact('total', 'actifs', 'inactifs', 'chefs', 'sansUser', 'services'))
    @include('agents.partials._datatable', compact('agents', 'total'))
    @include('agents.partials._modals', compact('services', 'users'))
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
<script src="{{ asset('js/agents/index.js?v='.filemtime(public_path('js/agents/index.js'))) }}"></script>
@endpush
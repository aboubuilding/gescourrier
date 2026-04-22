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

        <button class="btn btn-outline-success btn-sm"
                data-bs-toggle="modal"
                data-bs-target="#modalExport">
            <i class="fas fa-download"></i>
            <span class="d-none d-sm-inline">Exporter</span>
        </button>

        <button class="btn btn-success"
                data-bs-toggle="modal"
                data-bs-target="#modalCreateService">
            <i class="fas fa-plus-circle"></i>
            <span class="d-none d-sm-inline">Nouveau service</span>
        </button>

    </div>
@endsection

@push('css')

    <link rel="stylesheet" href="{{ asset('app/assets/plugins/datatables/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('app/assets/plugins/datatables/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <link rel="stylesheet"
          href="{{ asset('css/services/index.css?v='.filemtime(public_path('css/services/index.css'))) }}">

@endpush

@section('contenu')

@php
    $total       = $stats['total'] ?? 0;
    $actifs      = $stats['actifs'] ?? 0;
    $inactifs    = $stats['inactifs'] ?? 0;
    $totalAgents = $stats['total_agents'] ?? 0;

    $S = \App\Models\Service::class;
@endphp

<div class="services-page">

    {{-- 1. Stats --}}
    @include('services.partials._stats', compact(
        'total',
        'actifs',
        'inactifs',
        'totalAgents'
    ))

    {{-- 2. Filters --}}
    @include('services.partials._filters')

    {{-- 3. Table --}}
    @include('services.partials._datatable', [
        'services' => $services,
        'total' => $total,
        'S' => $S
    ])

    {{-- 4. Modals --}}
    @include('services.partials.modals._master')

</div>

@endsection

@push('js')

    {{-- DataTables --}}
    <script src="{{ asset('app/assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('app/assets/js/dataTables.bootstrap5.min.js') }}"></script>

    <script src="{{ asset('app/assets/plugins/datatables/buttons.dataTables.min.js') }}"></script>
    <script src="{{ asset('app/assets/plugins/datatables/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('app/assets/plugins/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset('app/assets/plugins/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('app/assets/plugins/datatables/vfs_fonts.js') }}"></script>

    {{-- Notifications --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    {{-- JS Services (ordre important) --}}
    <script src="{{ asset('js/services/config.js?v='.filemtime(public_path('js/services/config.js'))) }}"></script>
    <script src="{{ asset('js/services/create.js?v='.filemtime(public_path('js/services/create.js'))) }}"></script>
    <script src="{{ asset('js/services/edit.js?v='.filemtime(public_path('js/services/edit.js'))) }}"></script>
    <script src="{{ asset('js/services/actions.js?v='.filemtime(public_path('js/services/actions.js'))) }}"></script>
    <script src="{{ asset('js/services/show.js?v='.filemtime(public_path('js/services/show.js'))) }}"></script>
    <script src="{{ asset('js/services/filters.js?v='.filemtime(public_path('js/services/filters.js'))) }}"></script>
    <script src="{{ asset('js/services/select2.js?v='.filemtime(public_path('js/services/select2.js'))) }}"></script>
    <script src="{{ asset('js/services/index.js?v='.filemtime(public_path('js/services/index.js'))) }}"></script>

@endpush
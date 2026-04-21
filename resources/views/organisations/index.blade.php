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
<link rel="stylesheet" href="{{ asset('app/assets/plugins/datatables/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('app/assets/plugins/datatables/buttons.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('app/assets/plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="stylesheet" href="{{ asset('css/organisations/index.css?v='.filemtime(public_path('css/organisations/index.css'))) }}">
@endpush

@section('contenu')
@php
    // Définition centralisée des variables
    $total        = $stats['total'] ?? 0;
    $internes     = $stats['internes'] ?? 0;
    $externes     = $stats['externes'] ?? 0;
    $gouvernement = $stats['gouvernement'] ?? 0;
    $prive        = $stats['prive'] ?? 0;
    $ong          = $stats['ong'] ?? 0;
    $actifs       = $stats['actifs'] ?? 0;
    $O = \App\Models\Organisation::class;
@endphp

<div class="organisations-page">
    
    {{-- 1. Cartes Statistiques --}}
    @include('organisations.partials._stats-cards', compact('total', 'internes', 'externes', 'gouvernement', 'prive', 'ong', 'actifs'))
    
    {{-- 2. Filtres --}}
    @include('organisations.partials._filters')
    
    {{-- 3. Tableau DataTables --}}
    @include('organisations.partials._datatable', compact('organisations', 'total', 'O'))
    
    {{-- 4. Modals (Conteneur Master incluant Create, Edit, Export) --}}
    @include('organisations.partials.modals._master')

</div>
@endsection

@push('js')
    {{-- Librairies externes --}}
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

    {{-- Logique métier splittée --}}
    <script src="{{ asset('js/organisations/config.js?v='.filemtime(public_path('js/organisations/config.js'))) }}"></script>
    <script src="{{ asset('js/organisations/create.js?v='.filemtime(public_path('js/organisations/create.js'))) }}"></script>
    <script src="{{ asset('js/organisations/edit.js?v='.filemtime(public_path('js/organisations/edit.js'))) }}"></script>
    <script src="{{ asset('js/organisations/actions.js?v='.filemtime(public_path('js/organisations/actions.js'))) }}"></script>
    <script src="{{ asset('js/organisations/filters.js?v='.filemtime(public_path('js/organisations/filters.js'))) }}"></script>
    <script src="{{ asset('js/organisations/index.js?v='.filemtime(public_path('js/organisations/index.js'))) }}"></script>
@endpush
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
        <button class="btn btn-outline-rouge btn-sm" data-bs-toggle="modal" data-bs-target="#modalExport">
            <i class="fas fa-download"></i> <span class="d-none d-sm-inline">Exporter</span>
        </button>
        <button class="btn btn-rouge" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="fas fa-plus-circle"></i> <span class="d-none d-sm-inline">Nouveau courrier</span>
        </button>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('css/courriers/index.css?v='.filemtime(public_path('css/courriers/index.css'))) }}">
@endpush

@section('contenu')
@php
    // ✅ Définition CENTRALISÉE des variables — accessible à tous les partials
    $total        = $stats['total'] ?? 0;
    $nbEntrants   = $stats['entrants'] ?? 0;
    $nbSortants   = $stats['sortants'] ?? 0;
    $nbInternes   = $stats['internes'] ?? 0;
    $nbTresUrgent = $stats['tres_urgents'] ?? 0;
    $nbArchives   = $stats['archives'] ?? 0;
    $C = \App\Models\Courrier::class;
@endphp

<div class="courriers-page">
    {{-- ✅ Passage explicite des variables via compact() --}}
    @include('courriers.partials._stats-filters', compact('total', 'nbEntrants', 'nbSortants', 'nbInternes', 'nbTresUrgent', 'nbArchives', 'C'))
    
    @include('courriers.partials._datatable', compact('courriers', 'total', 'C'))
    
    @include('courriers.partials._modals', compact('services', 'organisations', 'agents'))
</div>
@endsection

@push('js')
    {{-- DataTables + Plugins --}}
    <script src="{{ asset('app/assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('app/assets/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('app/assets/plugins/datatables/buttons.dataTables.min.js') }}"></script>
    <script src="{{ asset('app/assets/plugins/datatables/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('app/assets/plugins/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset('app/assets/plugins/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('app/assets/plugins/datatables/vfs_fonts.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script src="{{ asset('js/courriers/index.js?v='.filemtime(public_path('js/courriers/index.js'))) }}"></script>
@endpush
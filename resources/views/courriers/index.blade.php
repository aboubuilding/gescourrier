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
    {{-- CSS Spécifique à la page Courriers --}}
    <link rel="stylesheet" href="{{ asset('css/courriers/index.css?v='.filemtime(public_path('css/courriers/index.css'))) }}">
@endpush

@section('contenu')
@php
    // ✅ Définition CENTRALISÉE des variables
    $total        = $stats['total'] ?? 0;
    $nbEntrants   = $stats['entrants'] ?? 0;
    $nbSortants   = $stats['sortants'] ?? 0;
    $nbInternes   = $stats['internes'] ?? 0;
    $nbTresUrgent = $stats['tres_urgents'] ?? 0;
    $nbArchives   = $stats['archives'] ?? 0;
    $C = \App\Models\Courrier::class;
@endphp

<div class="courriers-page">
    
    {{-- 1. Stats & Filtres --}}
    @include('courriers.partials._stats-filters', compact('total', 'nbEntrants', 'nbSortants', 'nbInternes', 'nbTresUrgent', 'nbArchives', 'C'))
    
    {{-- 2. Tableau DataTables --}}
    @include('courriers.partials._datatable', compact('courriers', 'total', 'C'))
    
    {{-- 3. Modals (Conteneur Master incluant Create, Edit, Affecter, Export + CSS/JS des modals) --}}
    @include('courriers.partials.modals._master', compact('services', 'organisations', 'agents'))

</div>
@endsection

@push('js')
    {{-- ============================================
         LIBRAIRIES EXTERNES
    ============================================= --}}
    <script src="{{ asset('app/assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('app/assets/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('app/assets/plugins/datatables/buttons.dataTables.min.js') }}"></script>
    <script src="{{ asset('app/assets/plugins/datatables/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('app/assets/plugins/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset('app/assets/plugins/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('app/assets/plugins/datatables/vfs_fonts.js') }}"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    {{-- ============================================
         LOGIQUE MÉTIER SPLITÉE (Ordre Important)
    ============================================= --}}
    
    {{-- 1. Config & Helpers (CSRF, Toasts, Init DataTable) --}}
    <script src="{{ asset('js/courriers/config.js?v='.filemtime(public_path('js/courriers/config.js'))) }}"></script>
    
    {{-- 2. Modal Création --}}
    <script src="{{ asset('js/courriers/create.js?v='.filemtime(public_path('js/courriers/create.js'))) }}"></script>
    
    {{-- 3. Modal Modification --}}
    <script src="{{ asset('js/courriers/edit.js?v='.filemtime(public_path('js/courriers/edit.js'))) }}"></script>
    
{{-- 4. MODAL  Affichage  --}}
       <script src="{{ asset('js/courriers/show.js') }}"></script>
    {{-- 5. Actions (Affecter, Archiver, Supprimer, Export) --}}
    <script src="{{ asset('js/courriers/actions.js?v='.filemtime(public_path('js/courriers/actions.js'))) }}"></script>
    {{-- 6. js  filtres   --}}
       <script src="{{ asset('js/courriers/filters.js') }}"></script>

       {{-- 6. JS  select2  --}}
       <script src="{{ asset('js/courriers/select2.js') }}"></script>
    {{-- 7. Fichier Principal (Orchestrateur) --}}
    <script src="{{ asset('js/courriers/index.js?v='.filemtime(public_path('js/courriers/index.js'))) }}"></script>
@endpush
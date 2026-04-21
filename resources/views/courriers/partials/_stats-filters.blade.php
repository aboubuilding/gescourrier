@php
    $total        = $stats['total'] ?? 0;
    $nbEntrants   = $stats['entrants'] ?? 0;
    $nbSortants   = $stats['sortants'] ?? 0;
    $nbInternes   = $stats['internes'] ?? 0;
    $nbTresUrgent = $stats['tres_urgents'] ?? 0;
    $nbArchives   = $stats['archives'] ?? 0;
    $C = \App\Models\Courrier::class;
@endphp

{{-- KPI CARDS --}}
<div class="stats-grid">
    <div class="stat-card"><div class="stat-icon primaire"><i class="fas fa-envelope"></i></div><div><div class="stat-value">{{ $total }}</div><div class="stat-label">Total</div></div></div>
    <div class="stat-card entrant"><div class="stat-icon entrant"><i class="fas fa-arrow-down-left"></i></div><div><div class="stat-value">{{ $nbEntrants }}</div><div class="stat-label">Entrants</div></div></div>
    <div class="stat-card sortant"><div class="stat-icon sortant"><i class="fas fa-arrow-up-right"></i></div><div><div class="stat-value">{{ $nbSortants }}</div><div class="stat-label">Sortants</div></div></div>
    <div class="stat-card interne"><div class="stat-icon interne"><i class="fas fa-arrows-left-right"></i></div><div><div class="stat-value">{{ $nbInternes }}</div><div class="stat-label">Internes</div></div></div>
    <div class="stat-card urgente"><div class="stat-icon urgente"><i class="fas fa-bolt"></i></div><div><div class="stat-value">{{ $nbTresUrgent }}</div><div class="stat-label">Très urgents</div></div></div>
    <div class="stat-card archive"><div class="stat-icon archive"><i class="fas fa-box-archive"></i></div><div><div class="stat-value">{{ $nbArchives }}</div><div class="stat-label">Archivés</div></div></div>
</div>

{{-- FILTRE BAR --}}
<div class="filter-bar">
    <div class="row g-3 align-items-end">
        <div class="col-md-4"><label class="filter-label">Rechercher</label><input type="text" id="searchInput" class="filter-input" placeholder="Référence, objet, expéditeur…"></div>
        <div class="col-md-2"><label class="filter-label">Statut</label><select id="filterStatut" class="filter-select"><option value="">Tous</option><option value="{{ $C::STATUT_ENREGISTRE }}">Enregistré</option><option value="{{ $C::STATUT_AFFECTE }}">Affecté</option><option value="{{ $C::STATUT_ARCHIVE }}">Archivé</option></select></div>
        <div class="col-md-2"><label class="filter-label">Priorité</label><select id="filterPriorite" class="filter-select"><option value="">Toutes</option><option value="{{ $C::PRIORITE_NORMALE }}">Normale</option><option value="{{ $C::PRIORITE_URGENTE }}">Urgente</option><option value="{{ $C::PRIORITE_TRES_URGENTE }}">Très urgente</option></select></div>
        <div class="col-md-3"><label class="filter-label">Type</label><div class="type-pills"><span class="type-pill active" data-type="">Tous</span><span class="type-pill" data-type="{{ $C::TYPE_ENTRANT }}">Entrants</span><span class="type-pill" data-type="{{ $C::TYPE_SORTANT }}">Sortants</span><span class="type-pill" data-type="{{ $C::TYPE_INTERNE }}">Internes</span></div></div>
        <div class="col-md-1"><button type="button" id="btnResetFilters" class="btn-filter-reset w-100"><i class="fas fa-redo"></i></button></div>
    </div>
</div>
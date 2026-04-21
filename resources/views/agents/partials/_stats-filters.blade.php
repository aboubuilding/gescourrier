{{-- KPI CARDS --}}
<div class="stats-grid">
    <div class="stat-card"><div class="stat-icon primaire"><i class="fas fa-users"></i></div><div><div class="stat-value">{{ $total }}</div><div class="stat-label">Total agents</div></div></div>
    <div class="stat-card actif"><div class="stat-icon actif"><i class="fas fa-check-circle"></i></div><div><div class="stat-value">{{ $actifs }}</div><div class="stat-label">Actifs</div></div></div>
    <div class="stat-card inactif"><div class="stat-icon inactif"><i class="fas fa-pause-circle"></i></div><div><div class="stat-value">{{ $inactifs }}</div><div class="stat-label">Inactifs</div></div></div>
    <div class="stat-card chef"><div class="stat-icon chef"><i class="fas fa-crown"></i></div><div><div class="stat-value">{{ $chefs }}</div><div class="stat-label">Chefs de service</div></div></div>
    <div class="stat-card service"><div class="stat-icon service"><i class="fas fa-user-times"></i></div><div><div class="stat-value">{{ $sansUser }}</div><div class="stat-label">Sans compte</div></div></div>
</div>

{{-- FILTRE BAR --}}
<div class="filter-bar">
    <div class="row g-3 align-items-end">
        <div class="col-md-4"><label class="filter-label">Rechercher</label><input type="text" id="searchInput" class="filter-input" placeholder="Nom, prénom, email, fonction…"></div>
        <div class="col-md-2"><label class="filter-label">Service</label><select id="filterService" class="filter-select"><option value="">Tous</option>@foreach($services as $service)<option value="{{ $service['id'] ?? $service->id }}">{{ $service['nom'] ?? $service->nom }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="filter-label">État</label><select id="filterEtat" class="filter-select"><option value="">Tous</option><option value="1">Actif</option><option value="2">Inactif</option></select></div>
        <div class="col-md-3"><label class="filter-label">Fonction</label><div class="function-pills"><span class="function-pill active" data-function="">Tous</span><span class="function-pill" data-function="chef">Chefs</span><span class="function-pill" data-function="secretaire">Secrétaires</span><span class="function-pill" data-function="gestionnaire">Gestionnaires</span><span class="function-pill" data-function="agent">Agents</span></div></div>
        <div class="col-md-1"><button type="button" id="btnResetFilters" class="btn-filter-reset w-100"><i class="fas fa-redo"></i></button></div>
    </div>
</div>
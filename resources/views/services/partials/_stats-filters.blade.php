{{-- KPI CARDS --}}
<div class="stats-grid">
    <div class="stat-card"><div class="stat-icon primaire"><i class="fas fa-layer-group"></i></div><div><div class="stat-value">{{ $total }}</div><div class="stat-label">Total services</div></div></div>
    <div class="stat-card actif"><div class="stat-icon actif"><i class="fas fa-check-circle"></i></div><div><div class="stat-value">{{ $actifs }}</div><div class="stat-label">Actifs</div></div></div>
    <div class="stat-card inactif"><div class="stat-icon inactif"><i class="fas fa-pause-circle"></i></div><div><div class="stat-value">{{ $inactifs }}</div><div class="stat-label">Inactifs</div></div></div>
    <div class="stat-card"><div class="stat-icon"><i class="fas fa-users"></i></div><div><div class="stat-value">{{ $totalAgents }}</div><div class="stat-label">Agents affectés</div></div></div>
</div>

{{-- FILTRE BAR --}}
<div class="filter-bar">
    <div class="row g-3 align-items-end">
        <div class="col-md-4"><label class="filter-label">Rechercher</label><input type="text" id="searchInput" class="filter-input" placeholder="Nom du service, organisation…"></div>
        <div class="col-md-3"><label class="filter-label">Organisation</label><select id="filterOrganisation" class="filter-select"><option value="">Toutes</option>@foreach($organisations as $org)<option value="{{ $org->id }}">{{ $org->nom }} @if(!empty($org->sigle))({{ $org->sigle }})@endif</option>@endforeach</select></div>
        <div class="col-md-2"><label class="filter-label">État</label><select id="filterEtat" class="filter-select"><option value="">Tous</option><option value="1">Actif</option><option value="2">Inactif</option></select></div>
        <div class="col-md-2"><button type="button" id="btnResetFilters" class="btn-filter-reset w-100"><i class="fas fa-redo"></i> Réinitialiser</button></div>
    </div>
</div>
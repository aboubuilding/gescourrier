{{-- =========================
     KPI STATS
========================= --}}

<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-icon primaire">
            <i class="fas fa-layer-group"></i>
        </div>
        <div>
            <div class="stat-value">{{ $stats['total_actifs'] ?? 0 }}</div>
            <div class="stat-label">Services actifs</div>
        </div>
    </div>

    <div class="stat-card inactif">
        <div class="stat-icon inactif">
            <i class="fas fa-ban"></i>
        </div>
        <div>
            <div class="stat-value">{{ $stats['total_inactifs'] ?? 0 }}</div>
            <div class="stat-label">Services inactifs</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-envelope"></i>
        </div>
        <div>
            <div class="stat-value">{{ $stats['total_courriers'] ?? 0 }}</div>
            <div class="stat-label">Courriers</div>
        </div>
    </div>

    <div class="stat-card actif">
        <div class="stat-icon actif">
            <i class="fas fa-users"></i>
        </div>
        <div>
            <div class="stat-value">{{ $stats['total_agents'] ?? 0 }}</div>
            <div class="stat-label">Agents</div>
        </div>
    </div>

</div>

{{-- =========================
     KPI BONUS
========================= --}}

<div class="stats-grid mt-3">

    <div class="stat-card">
        <div class="stat-icon primaire">
            <i class="fas fa-inbox"></i>
        </div>
        <div>
            <div class="stat-value">{{ $stats['services_sans_courrier'] ?? 0 }}</div>
            <div class="stat-label">Sans courrier</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon inactif">
            <i class="fas fa-user-slash"></i>
        </div>
        <div>
            <div class="stat-value">{{ $stats['services_sans_agents'] ?? 0 }}</div>
            <div class="stat-label">Sans agents</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <div>
            <div class="stat-value">{{ $stats['moyenne_courriers_par_service'] ?? 0 }}</div>
            <div class="stat-label">Moyenne / service</div>
        </div>
    </div>

</div>
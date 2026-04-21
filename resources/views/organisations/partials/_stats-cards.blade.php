{{-- ============================================
     KPI CARDS
============================================ --}}
@php
    // Extraire les statistiques du tableau $stats
    $total = $stats['total_organisations'] ?? 0;
    $parType = $stats['par_type'] ?? [];
    
    // Récupérer les valeurs par type
    $externes = $parType['Externe'] ?? 0;
    $internes = $parType['Interne'] ?? 0;
    $gouvernement = $parType['Gouvernementale'] ?? 0;
    $prive = $parType['Privée'] ?? 0;
    $ong = $parType['ONG'] ?? 0;
    
    // Calculer le total actif (vous pouvez l'ajouter dans getStats)
    $actifs = $total; // Ou utilisez un compteur spécifique
@endphp

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primaire"><i class="fas fa-sitemap"></i></div>
        <div>
            <div class="stat-value">{{ $total }}</div>
            <div class="stat-label">Total organisations</div>
        </div>
    </div>
    <div class="stat-card interne">
        <div class="stat-icon interne"><i class="fas fa-building"></i></div>
        <div>
            <div class="stat-value">{{ $internes }}</div>
            <div class="stat-label">Internes</div>
        </div>
    </div>
    <div class="stat-card externe">
        <div class="stat-icon externe"><i class="fas fa-handshake"></i></div>
        <div>
            <div class="stat-value">{{ $externes }}</div>
            <div class="stat-label">Externes</div>
        </div>
    </div>
    <div class="stat-card gouvernement">
        <div class="stat-icon gouvernement"><i class="fas fa-landmark"></i></div>
        <div>
            <div class="stat-value">{{ $gouvernement }}</div>
            <div class="stat-label">Gouvernementales</div>
        </div>
    </div>
    <div class="stat-card prive">
        <div class="stat-icon prive"><i class="fas fa-briefcase"></i></div>
        <div>
            <div class="stat-value">{{ $prive }}</div>
            <div class="stat-label">Privées</div>
        </div>
    </div>
    <div class="stat-card ong">
        <div class="stat-icon ong"><i class="fas fa-hands-helping"></i></div>
        <div>
            <div class="stat-value">{{ $ong }}</div>
            <div class="stat-label">ONG</div>
        </div>
    </div>
    <div class="stat-card actif">
        <div class="stat-icon actif"><i class="fas fa-check-circle"></i></div>
        <div>
            <div class="stat-value">{{ $total }}</div>
            <div class="stat-label">Actives</div>
        </div>
    </div>
</div>
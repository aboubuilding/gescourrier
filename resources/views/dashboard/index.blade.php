@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('page_title', 'Tableau de bord')
@section('page_icon', 'fa-th-large')

@section('breadcrumb')
    <li><a href="{{ route('dashboard.index') }}">Accueil</a></li>
@endsection

@section('page_actions')
    <div class="d-flex align-items-center gap-2">
        <span class="text-muted small d-none d-md-inline">
            <i class="fas fa-sync-alt me-1"></i> Mis à jour à <span id="last-update">{{ now()->format('H:i') }}</span>
        </span>
        
        {{-- Filtre rapide --}}
        <select id="period-filter" class="form-select form-select-sm" style="width:auto;min-width:140px;">
            <option value="today">Aujourd'hui</option>
            <option value="week" selected>Cette semaine</option>
            <option value="month">Ce mois</option>
            <option value="year">Cette année</option>
        </select>
        
        {{-- Bouton refresh --}}
        <button id="btn-refresh" class="btn btn-outline-rouge btn-sm" title="Actualiser les données">
            <i class="fas fa-sync-alt"></i>
        </button>
        
        {{-- Export --}}
        <div class="dropdown">
            <button class="btn btn-rouge btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-download"></i> Export
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#" id="export-pdf"><i class="fas fa-file-pdf text-danger"></i> PDF</a></li>
                <li><a class="dropdown-item" href="#" id="export-excel"><i class="fas fa-file-excel text-success"></i> Excel</a></li>
                <li><a class="dropdown-item" href="#" id="export-csv"><i class="fas fa-file-csv text-primary"></i> CSV</a></li>
            </ul>
        </div>
    </div>
@endsection

{{-- CSS spécifique au dashboard (optionnel, si besoin de surcharger) --}}
@push('css')
<style>
    /* Animations dashboard */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.6; transform: scale(1.05); }
    }
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    .anim { animation: fadeUp 0.5s ease both; }
    .anim-1 { animation-delay: 0.05s; }
    .anim-2 { animation-delay: 0.10s; }
    .anim-3 { animation-delay: 0.15s; }
    .anim-4 { animation-delay: 0.20s; }
    .anim-5 { animation-delay: 0.25s; }
    .anim-6 { animation-delay: 0.30s; }
    .anim-7 { animation-delay: 0.35s; }
    .anim-8 { animation-delay: 0.40s; }
    
    .skeleton {
        background: linear-gradient(90deg, var(--bg-hover) 25%, var(--bg-card) 50%, var(--bg-hover) 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        border-radius: 8px;
    }
    
    /* Grid dashboard */
    .dash-grid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 20px;
    }
    
    /* Responsive dashboard */
    @media (max-width: 1200px) {
        .kpi-card { grid-column: span 6; }
        .chart-panel { grid-column: span 12; }
        .donut-panel { grid-column: span 6; }
        .activity-panel { grid-column: span 6; }
        .table-panel { grid-column: span 12; }
    }
    @media (max-width: 768px) {
        .dash-grid { gap: 16px; }
        .kpi-card, .donut-panel, .activity-panel, .chart-panel, .table-panel {
            grid-column: span 12;
        }
    }
</style>
@endpush

@section('contenu')

{{-- Alerte erreur --}}
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="dash-grid">
    
    {{-- ══════════════════════════════════════════
         PARTIAL 1 : KPI + BANNIÈRE
    ══════════════════════════════════════════ --}}
    @include('dashboard.partials._kpi')
    
    {{-- ══════════════════════════════════════════
         PARTIAL 2 : CHARTS + TABLES
    ══════════════════════════════════════════ --}}
    @include('dashboard.partials._content')
    
</div>

@endsection

{{-- JS spécifique au dashboard --}}
@push('js')
<script>
$(document).ready(function() {
    
    // ═══════════════════════════════════════
    // ⚙️ CONFIG
    // ═══════════════════════════════════════
    const $dashboard = $('.dash-grid');
    let isRefreshing = false;
    
    // ═══════════════════════════════════════
    // 🕐 HORLOGE EN DIRECT
    // ═══════════════════════════════════════
    function updateClock() {
        const now = new Date();
        $('#live-clock').text(now.toLocaleTimeString('fr-FR', {hour:'2-digit',minute:'2-digit'}));
    }
    setInterval(updateClock, 1000);
    updateClock();

    // ═══════════════════════════════════════
    // 🔢 COMPTEURS ANIMÉS KPI
    // ═══════════════════════════════════════
    function animateCounter($el, target, duration = 1200) {
        const start = 0;
        const step = Math.max(1, Math.ceil(target / (duration / 16)));
        let current = start;
        
        const timer = setInterval(() => {
            current = Math.min(current + step, target);
            $el.text(current.toLocaleString('fr-FR'));
            if (current >= target) {
                clearInterval(timer);
                $el.text(target.toLocaleString('fr-FR'));
            }
        }, 16);
    }
    
    $('.kpi-val[data-count]').each(function() {
        const $el = $(this);
        const target = parseInt($el.data('count')) || 0;
        if (target > 0) {
            setTimeout(() => animateCounter($el, target), 300);
        } else {
            $el.text('0');
        }
    });

    // ═══════════════════════════════════════
    // 📊 BARRES DE PROGRESSION
    // ═══════════════════════════════════════
    setTimeout(() => {
        $('.kpi-bar-fill[data-width]').each(function() {
            const width = $(this).data('width');
            $(this).css('width', width + '%');
        });
    }, 500);

    // ═══════════════════════════════════════
    // 🔄 RAFRAÎCHISSEMENT AJAX
    // ═══════════════════════════════════════
    $('#btn-refresh').on('click', function() {
        if (isRefreshing) return;
        
        isRefreshing = true;
        const $btn = $(this);
        $btn.addClass('refreshing').prop('disabled', true);
        $dashboard.addClass('refreshing');
        
        // Simulation d'appel API (remplacer par vrai fetch en prod)
        setTimeout(function() {
            // Mise à jour factice des compteurs
            $('.kpi-val[data-count]').each(function() {
                const $el = $(this);
                const current = parseInt($el.text().replace(/\s/g, '')) || 0;
                const variation = Math.floor(Math.random() * 5) - 2;
                animateCounter($el, Math.max(0, current + variation), 800);
            });
            
            // Mise à jour horodatage
            const now = new Date();
            $('#last-update').text(now.toLocaleTimeString('fr-FR', {hour:'2-digit',minute:'2-digit'}));
            
            // Feedback utilisateur
            if (typeof window.showToast === 'function') {
                window.showToast('Tableau de bord actualisé', 'success');
            }
            
            // Reset état
            setTimeout(() => {
                $btn.removeClass('refreshing').prop('disabled', false);
                $dashboard.removeClass('refreshing');
                isRefreshing = false;
            }, 500);
            
        }, 1200);
    });

    // ═══════════════════════════════════════
    // 🔍 FILTRE PAR PÉRIODE
    // ═══════════════════════════════════════
    $('#period-filter').on('change', function() {
        const period = $(this).val();
        const labels = {today:'Aujourd\'hui', week:'Cette semaine', month:'Ce mois', year:'Cette année'};
        
        $('#donut-period').text(labels[period]);
        if (typeof window.showToast === 'function') {
            window.showToast(`Filtre : ${labels[period]}`, 'info');
        }
    });

    // ═══════════════════════════════════════
    // 📤 EXPORT
    // ═══════════════════════════════════════
    $('#export-pdf, #export-excel, #export-csv').on('click', function(e) {
        e.preventDefault();
        const type = $(this).attr('id').replace('export-', '').toUpperCase();
        
        if (typeof window.showToast === 'function') {
            window.showToast(`Export ${type} en préparation...`, 'info');
        }
        
        setTimeout(() => {
            if (typeof window.showToast === 'function') {
                window.showToast(`Fichier ${type} prêt au téléchargement`, 'success');
            }
        }, 1500);
    });

    // ═══════════════════════════════════════
    // 📋 TRI DES TABLEAUX (client-side pour démo)
    // ═══════════════════════════════════════
    $('.table-mini thead th[data-sort]').on('click', function() {
        const $th = $(this);
        const col = $th.data('sort');
        const $table = $th.closest('table');
        const $tbody = $table.find('tbody');
        const $rows = $tbody.find('tr').toArray();
        
        const isAsc = !$th.hasClass('sorted') || $th.hasClass('desc');
        $table.find('th').removeClass('sorted asc desc');
        $th.addClass('sorted').toggleClass('asc desc', !isAsc);
        
        $rows.sort((a, b) => {
            const $a = $(a).find('td').eq($th.index());
            const $b = $(b).find('td').eq($th.index());
            const textA = $a.text().trim();
            const textB = $b.text().trim();
            return isAsc ? textA.localeCompare(textB, 'fr') : textB.localeCompare(textA, 'fr');
        });
        
        $rows.forEach((row, i) => {
            setTimeout(() => $tbody.append(row), i * 30);
        });
    });

    // ═══════════════════════════════════════
    // 🥧 INTERACTION DONUT
    // ═══════════════════════════════════════
    $('.donut-leg-item').on('click', function() {
        const filter = $(this).data('filter');
        const label = $(this).find('.donut-leg-label').text();
        const value = $(this).find('.donut-leg-val').text();
        
        $('#donut-total').text(value);
        $('.donut-center-lbl').text(label);
        
        $('.donut-leg-item').css('opacity', '0.6');
        $(this).css('opacity', '1');
        
        if (typeof window.showToast === 'function') {
            window.showToast(`Filtré : ${label}`, 'info');
        }
        
        setTimeout(() => {
            $('.donut-leg-item').css('opacity', '');
            $('#donut-total').text('{{ $stats['total_courriers'] ?? 0 }}');
            $('.donut-center-lbl').text('Total');
        }, 3000);
    });

    // ═══════════════════════════════════════
    // 🔗 CARDS CLIQUABLES
    // ═══════════════════════════════════════
    $('.kpi-card[data-href]').on('click', function(e) {
        if (e.target.closest('a')) return;
        window.location.href = $(this).data('href');
    }).css('cursor', 'pointer');

    // ═══════════════════════════════════════
    // ⌨️ RACCOURCIS CLAVIER
    // ═══════════════════════════════════════
    $(document).on('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
            e.preventDefault();
            $('#btn-refresh').click();
        }
        if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
            e.preventDefault();
            window.location.href = '{{ route('courriers.index') }}';
        }
    });

    // ═══════════════════════════════════════
    // 🎯 INIT : Message de bienvenue
    // ═══════════════════════════════════════
    if (!sessionStorage.getItem('dashboard_welcome')) {
        setTimeout(() => {
            if (typeof window.showToast === 'function') {
                window.showToast('👋 Bienvenue sur votre tableau de bord', 'success');
            }
            sessionStorage.setItem('dashboard_welcome', 'true');
        }, 1500);
    }
});
</script>
@endpush
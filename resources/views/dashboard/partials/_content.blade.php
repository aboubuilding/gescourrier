{{-- ══════════════════════════════════════════
     CHARTS + TABLES D'ACTIVITÉ
     ══════════════════════════════════════════ --}}

@php
    $recentCourriers    = $stats['recent_courriers'] ?? collect([]);
    $recentAffectations = $stats['recent_affectations'] ?? collect([]);
    $recentAnnotations  = $stats['recent_annotations'] ?? collect([]);
@endphp

{{-- ══════════════════════════════════════════
     DONUT CHART — Vue d'ensemble
══════════════════════════════════════════ --}}
<div class="panel donut-panel anim anim-6" style="grid-column: span 4; background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); overflow: hidden; transition: var(--transition);" onmouseover="this.style.boxShadow='var(--shadow-md)'" onmouseout="this.style.boxShadow='var(--shadow-sm)'">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
        <h6 style="font-size: 14px; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-chart-pie" style="color: var(--primaire);"></i> Vue d'ensemble
        </h6>
        <span class="text-muted small" id="donut-period">Cette semaine</span>
    </div>
    <div style="padding: 20px;">
        <div style="display: flex; flex-direction: column; align-items: center; gap: 24px;">
            
            {{-- Donut SVG --}}
            @php
                $slices = [
                    ['val' => $stats['total_courriers'] ?? 0,    'color' => 'var(--primaire)', 'label' => 'Courriers'],
                    ['val' => $stats['total_affectations'] ?? 0, 'color' => 'var(--info)', 'label' => 'Affectations'],
                    ['val' => $stats['total_annotations'] ?? 0,  'color' => 'var(--success)', 'label' => 'Annotations'],
                    ['val' => $stats['total_directions'] ?? 0,   'color' => 'var(--warning)', 'label' => 'Directions'],
                ];
                $grandTotal = max(array_sum(array_column($slices, 'val')), 1);
                $r = 62; $cx = 85; $cy = 85;
                $circumference = 2 * M_PI * $r;
                $offset = 0;
            @endphp

            <div style="position: relative; width: 170px; height: 170px;">
                <svg width="170" height="170" viewBox="0 0 170 170" style="transform: rotate(-90deg); transition: transform 0.3s ease;" onmouseover="this.style.transform='rotate(-90deg) scale(1.02)'" onmouseout="this.style.transform='rotate(-90deg)'">
                    @foreach($slices as $s)
                        @php
                            $dash = ($s['val'] / $grandTotal) * $circumference;
                            $gap  = $circumference - $dash;
                        @endphp
                        <circle
                            cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}"
                            fill="none"
                            stroke="{{ $s['color'] }}"
                            stroke-width="24"
                            stroke-dasharray="{{ $dash }} {{ $gap }}"
                            stroke-dashoffset="{{ -$offset }}"
                            stroke-linecap="round"
                            class="donut-slice"
                            data-label="{{ $s['label'] }}"
                            data-value="{{ $s['val'] }}"
                        />
                        @php $offset += $dash; @endphp
                    @endforeach
                </svg>
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; pointer-events: none;">
                    <div class="donut-center-val" id="donut-total" style="font-size: 30px; font-weight: 800; color: var(--text-primary); line-height: 1;">{{ $stats['total_courriers'] ?? 0 }}</div>
                    <div class="donut-center-lbl" style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.6px; margin-top: 4px;">Total</div>
                </div>
            </div>

            {{-- Légende --}}
            <div style="width: 100%; display: flex; flex-direction: column; gap: 12px;">
                @foreach($slices as $s)
                <div class="donut-leg-item" data-filter="{{ strtolower($s['label']) }}" style="display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 8px 12px; border-radius: 10px; transition: var(--transition); cursor: pointer;" onmouseover="this.style.background='var(--bg-hover)';this.style.transform='translateX(4px)'" onmouseout="this.style.background='';this.style.transform=''">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="donut-dot" style="width: 12px; height: 12px; border-radius: 4px; background: {{ $s['color'] }}; flex-shrink: 0; transition: transform 0.2s ease;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform=''"></div>
                        <span class="donut-leg-label" style="font-size: 13px; color: var(--text-secondary); font-weight: 500;">{{ $s['label'] }}</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span class="donut-leg-val" style="font-size: 14px; font-weight: 700; color: var(--text-primary);">{{ $s['val'] }}</span>
                        <span class="donut-leg-pct" style="font-size: 11px; color: var(--text-muted); font-weight: 600;">{{ round($s['val'] / $grandTotal * 100) }}%</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     BAR CHART — Évolution mensuelle
══════════════════════════════════════════ --}}
<div class="panel chart-panel anim anim-6" style="grid-column: span 8; background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); overflow: hidden; transition: var(--transition);" onmouseover="this.style.boxShadow='var(--shadow-md)'" onmouseout="this.style.boxShadow='var(--shadow-sm)'">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
        <h6 style="font-size: 14px; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-chart-bar" style="color: var(--primaire);"></i> Évolution des courriers
        </h6>
        <div style="display: flex; gap: 20px; margin-top: 16px; justify-content: flex-end; flex-wrap: wrap;">
            <span style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--text-secondary); font-weight: 500;">
                <span style="width: 12px; height: 12px; border-radius: 4px; background: var(--primaire);"></span>Entrants
            </span>
            <span style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--text-secondary); font-weight: 500;">
                <span style="width: 12px; height: 12px; border-radius: 4px; background: var(--info);"></span>Sortants
            </span>
        </div>
    </div>
    <div style="padding: 20px;">
        <div class="bar-chart" style="width: 100%;">
            <div style="display: flex; justify-content: space-between; font-size: 11px; color: var(--text-muted); margin-bottom: 14px; padding: 0 8px;">
                <span>Jan</span><span>Fév</span><span>Mar</span><span>Avr</span>
                <span>Mai</span><span>Juin</span><span>Juil</span><span>Aoû</span>
            </div>
            <div style="display: flex; align-items: flex-end; gap: 12px; height: 180px; padding: 0 8px; position: relative;">
                {{-- Grille horizontale --}}
                <div style="content: ''; position: absolute; left: 0; right: 0; height: 1px; background: var(--border); top: 25%;"></div>
                <div style="content: ''; position: absolute; left: 0; right: 0; height: 1px; background: var(--border); top: 50%;"></div>
                
                @for($i = 1; $i <= 8; $i++)
                    @php
                        $in = rand(15, 85);
                        $out = rand(10, 60);
                        $max = max($in, $out, 1);
                    @endphp
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 8px; height: 100%; justify-content: flex-end;">
                        <div style="display: flex; gap: 4px; align-items: flex-end; width: 100%; justify-content: center; height: 140px;">
                            <div class="bar-col bar-col-in" style="flex: 1; border-radius: 6px 6px 0 0; max-width: 20px; transition: height 0.8s cubic-bezier(0.4,0,0.2,1), opacity 0.2s, transform 0.2s; cursor: pointer; position: relative; background: var(--primaire);" onmouseover="this.style.opacity='0.9';this.style.transform='scaleY(1.05)';this.style.zIndex='2'" onmouseout="this.style.opacity='1';this.style.transform='';this.style.zIndex='1'" data-value="{{ $in }} entrants"></div>
                            <div class="bar-col bar-col-out" style="flex: 1; border-radius: 6px 6px 0 0; max-width: 20px; transition: height 0.8s cubic-bezier(0.4,0,0.2,1), opacity 0.2s, transform 0.2s; cursor: pointer; position: relative; background: var(--info);" onmouseover="this.style.opacity='0.9';this.style.transform='scaleY(1.05)';this.style.zIndex='2'" onmouseout="this.style.opacity='1';this.style.transform='';this.style.zIndex='1'" data-value="{{ $out }} sortants"></div>
                        </div>
                        <span style="font-size: 10px; color: var(--text-muted); text-align: center; font-weight: 500;">{{ ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Aoû'][$i-1] }}</span>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     DERNIERS COURRIERS
══════════════════════════════════════════ --}}
<div class="panel table-panel anim anim-7" style="grid-column: span 7; background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); overflow: hidden; transition: var(--transition);" onmouseover="this.style.boxShadow='var(--shadow-md)'" onmouseout="this.style.boxShadow='var(--shadow-sm)'">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
        <h6 style="font-size: 14px; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-envelope-open-text" style="color: var(--primaire);"></i> Derniers courriers
        </h6>
        <a href="{{ route('courriers.index') }}" style="font-size: 12px; color: var(--primaire); text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 4px; transition: var(--transition); padding: 6px 12px; border-radius: 8px;" onmouseover="this.style.background='var(--primaire-pale)';this.style.gap='8px';this.style.color='var(--primaire-deep)'" onmouseout="this.style.background='';this.style.gap='4px';this.style.color='var(--primaire)'">
            Voir tout <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    <div style="overflow-x: auto; margin: 0 -20px; padding: 0 20px;">
        <table class="table-mini" id="table-courriers" style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px;">
            <thead>
                <tr>
                    <th data-sort="reference" style="background: var(--bg-hover); color: var(--text-muted); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; padding: 12px 14px; border-bottom: 2px solid var(--border); position: sticky; top: 0; z-index: 5; cursor: pointer; user-select: none; transition: var(--transition);" onmouseover="this.style.background='var(--primaire-pale)';this.style.color='var(--primaire)'" onmouseout="this.style.background='var(--bg-hover)';this.style.color='var(--text-muted)'">Référence</th>
                    <th data-sort="objet" style="background: var(--bg-hover); color: var(--text-muted); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; padding: 12px 14px; border-bottom: 2px solid var(--border); position: sticky; top: 0; z-index: 5; cursor: pointer; user-select: none; transition: var(--transition);" onmouseover="this.style.background='var(--primaire-pale)';this.style.color='var(--primaire)'" onmouseout="this.style.background='var(--bg-hover)';this.style.color='var(--text-muted)'">Objet</th>
                    <th data-sort="expediteur" style="background: var(--bg-hover); color: var(--text-muted); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; padding: 12px 14px; border-bottom: 2px solid var(--border); position: sticky; top: 0; z-index: 5; cursor: pointer; user-select: none; transition: var(--transition);" onmouseover="this.style.background='var(--primaire-pale)';this.style.color='var(--primaire)'" onmouseout="this.style.background='var(--bg-hover)';this.style.color='var(--text-muted)'">Expéditeur</th>
                    <th data-sort="date" style="background: var(--bg-hover); color: var(--text-muted); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; padding: 12px 14px; border-bottom: 2px solid var(--border); position: sticky; top: 0; z-index: 5; cursor: pointer; user-select: none; transition: var(--transition);" onmouseover="this.style.background='var(--primaire-pale)';this.style.color='var(--primaire)'" onmouseout="this.style.background='var(--bg-hover)';this.style.color='var(--text-muted)'">Date</th>
                    <th data-sort="statut" style="background: var(--bg-hover); color: var(--text-muted); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; padding: 12px 14px; border-bottom: 2px solid var(--border); position: sticky; top: 0; z-index: 5; cursor: pointer; user-select: none; transition: var(--transition);" onmouseover="this.style.background='var(--primaire-pale)';this.style.color='var(--primaire)'" onmouseout="this.style.background='var(--bg-hover)';this.style.color='var(--text-muted)'">Statut</th>
                    <th style="background: var(--bg-hover); color: var(--text-muted); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; padding: 12px 14px; border-bottom: 2px solid var(--border); position: sticky; top: 0; z-index: 5;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentCourriers as $courrier)
                <tr style="transition: var(--transition);" onmouseover="this.style.background='var(--primaire-muted)';this.style.transform='translateX(4px)'" onmouseout="this.style.background='';this.style.transform=''">
                    <td style="padding: 12px 14px; border-bottom: 1px solid var(--border); color: var(--text-secondary); vertical-align: middle;"><span style="font-size: 11px; font-weight: 700; color: var(--primaire); background: var(--primaire-pale); padding: 3px 10px; border-radius: 6px; font-family: 'SF Mono', 'Fira Code', monospace; letter-spacing: 0.3px;">{{ $courrier->reference ?? 'COU-'.str_pad($courrier->id,5,'0',STR_PAD_LEFT) }}</span></td>
                    <td style="padding: 12px 14px; border-bottom: 1px solid var(--border); color: var(--text-secondary); vertical-align: middle; max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-weight:500;" title="{{ $courrier->objet }}">
                        {{ $courrier->objet ?? '—' }}
                    </td>
                    <td style="padding: 12px 14px; border-bottom: 1px solid var(--border); color: var(--text-secondary); vertical-align: middle; font-size:12px;">{{ optional($courrier->expediteur)->nom ?? '—' }}</td>
                    <td style="padding: 12px 14px; border-bottom: 1px solid var(--border); color: var(--text-secondary); vertical-align: middle; white-space:nowrap;font-size:12px;">{{ $courrier->created_at?->format('d/m/Y') ?? '—' }}</td>
                    <td style="padding: 12px 14px; border-bottom: 1px solid var(--border); color: var(--text-secondary); vertical-align: middle;">
                        <span style="font-size: 11px; font-weight: 600; padding: 4px 12px; border-radius: 20px; display: inline-flex; align-items: center; gap: 5px; {{ $courrier->statut == 2 ? 'background: var(--vert-pale); color: #166534;' : ($courrier->statut == 1 ? 'background: #fffbeb; color: #92400e;' : 'background: var(--bleu-pale); color: #1e40af;') }}">
                            <span style="width: 5px; height: 5px; border-radius: 50%; {{ $courrier->statut == 2 ? 'background: #16a34a;' : ($courrier->statut == 1 ? 'background: #d97706;' : 'background: #3b82f6;') }}"></span>
                            {{ $courrier->statut == 2 ? 'Traité' : ($courrier->statut == 1 ? 'En attente' : 'En transit') }}
                        </span>
                    </td>
                    <td style="padding: 12px 14px; border-bottom: 1px solid var(--border); color: var(--text-secondary); vertical-align: middle;">
                        <a href="{{ route('courriers.show', $courrier->id) }}" style="background: none; border: 1px solid var(--border); border-radius: 8px; padding: 6px 10px; font-size: 12px; color: var(--text-muted); cursor: pointer; transition: var(--transition); text-decoration: none; display: inline-flex; align-items: center; gap: 4px;" onmouseover="this.style.borderColor='var(--primaire)';this.style.color='var(--primaire)';this.style.background='var(--primaire-pale)';this.style.transform='translateY(-1px)'" onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-muted)';this.style.background='';this.style.transform=''">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6"><div style="text-align: center; padding: 40px 20px; color: var(--text-muted);"><i class="fas fa-inbox" style="font-size: 32px; display: block; margin-bottom: 12px; opacity: 0.4;"></i><p style="font-size: 13px; margin: 0;">Aucun courrier enregistré</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ══════════════════════════════════════════
     DERNIÈRES AFFECTATIONS
══════════════════════════════════════════ --}}
<div class="panel activity-panel anim anim-8" style="grid-column: span 5; background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); overflow: hidden; transition: var(--transition);" onmouseover="this.style.boxShadow='var(--shadow-md)'" onmouseout="this.style.boxShadow='var(--shadow-sm)'">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
        <h6 style="font-size: 14px; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-share-alt" style="color: var(--primaire);"></i> Affectations récentes
        </h6>
        <a href="" style="font-size: 12px; color: var(--primaire); text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 4px; transition: var(--transition); padding: 6px 12px; border-radius: 8px;" onmouseover="this.style.background='var(--primaire-pale)';this.style.gap='8px';this.style.color='var(--primaire-deep)'" onmouseout="this.style.background='';this.style.gap='4px';this.style.color='var(--primaire)'">
            Tout voir <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    <div style="padding: 20px;">
        <div style="display: flex; flex-direction: column; gap: 0;">
            @forelse($recentAffectations as $affectation)
            <div style="display: flex; align-items: flex-start; gap: 14px; padding: 14px 0; border-bottom: 1px solid var(--border); transition: var(--transition);" onmouseover="this.style.background='var(--bg-hover)';this.style.paddingLeft='8px';this.style.paddingRight='8px';this.style.margin='0 -8px';this.style.borderRadius='10px'" onmouseout="this.style.background='';this.style.paddingLeft='';this.style.paddingRight='';this.style.margin='';this.style.borderRadius=''">
                <div style="width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; transition: var(--transition); background: var(--bleu-pale); color: var(--info);" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform=''">
                    <i class="fas fa-share-alt"></i>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div style="font-size: 13px; font-weight: 600; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 3px;">
                        {{ optional($affectation->courrier)->objet ?? 'Courrier #' . $affectation->courrier_id }}
                    </div>
                    <div style="font-size: 11px; color: var(--text-muted); display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                        <i class="fas fa-user"></i>
                        {{ optional($affectation->utilisateur)->prenom ?? '' }} {{ optional($affectation->utilisateur)->nom ?? '—' }}
                    </div>
                </div>
                <div style="font-size: 11px; color: var(--text-muted); white-space: nowrap; flex-shrink: 0; font-weight: 500;" title="{{ $affectation->created_at }}">
                    {{ $affectation->created_at?->diffForHumans() ?? '—' }}
                </div>
            </div>
            @empty
            <div style="text-align: center; padding: 40px 20px; color: var(--text-muted);"><i class="fas fa-share-alt" style="font-size: 32px; display: block; margin-bottom: 12px; opacity: 0.4;"></i><p style="font-size: 13px; margin: 0;">Aucune affectation récente</p></div>
            @endforelse
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     DERNIÈRES ANNOTATIONS
══════════════════════════════════════════ --}}
<div class="panel anim anim-8" style="grid-column: span 12; background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); overflow: hidden; transition: var(--transition);" onmouseover="this.style.boxShadow='var(--shadow-md)'" onmouseout="this.style.boxShadow='var(--shadow-sm)'">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
        <h6 style="font-size: 14px; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-comment-alt" style="color: var(--primaire);"></i> Annotations récentes
        </h6>
        <a href="" style="font-size: 12px; color: var(--primaire); text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 4px; transition: var(--transition); padding: 6px 12px; border-radius: 8px;" onmouseover="this.style.background='var(--primaire-pale)';this.style.gap='8px';this.style.color='var(--primaire-deep)'" onmouseout="this.style.background='';this.style.gap='4px';this.style.color='var(--primaire)'">
            Tout voir <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    <div style="overflow-x: auto; margin: 0 -20px; padding: 0 20px;">
        <table class="table-mini" style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px;">
            <thead>
                <tr>
                    <th style="background: var(--bg-hover); color: var(--text-muted); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; padding: 12px 14px; border-bottom: 2px solid var(--border); position: sticky; top: 0; z-index: 5; cursor: pointer; user-select: none; transition: var(--transition);" onmouseover="this.style.background='var(--primaire-pale)';this.style.color='var(--primaire)'" onmouseout="this.style.background='var(--bg-hover)';this.style.color='var(--text-muted)'">Courrier</th>
                    <th style="background: var(--bg-hover); color: var(--text-muted); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; padding: 12px 14px; border-bottom: 2px solid var(--border); position: sticky; top: 0; z-index: 5; cursor: pointer; user-select: none; transition: var(--transition);" onmouseover="this.style.background='var(--primaire-pale)';this.style.color='var(--primaire)'" onmouseout="this.style.background='var(--bg-hover)';this.style.color='var(--text-muted)'">Annotation</th>
                    <th style="background: var(--bg-hover); color: var(--text-muted); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; padding: 12px 14px; border-bottom: 2px solid var(--border); position: sticky; top: 0; z-index: 5; cursor: pointer; user-select: none; transition: var(--transition);" onmouseover="this.style.background='var(--primaire-pale)';this.style.color='var(--primaire)'" onmouseout="this.style.background='var(--bg-hover)';this.style.color='var(--text-muted)'">Agent</th>
                    <th style="background: var(--bg-hover); color: var(--text-muted); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; padding: 12px 14px; border-bottom: 2px solid var(--border); position: sticky; top: 0; z-index: 5; cursor: pointer; user-select: none; transition: var(--transition);" onmouseover="this.style.background='var(--primaire-pale)';this.style.color='var(--primaire)'" onmouseout="this.style.background='var(--bg-hover)';this.style.color='var(--text-muted)'">Date</th>
                    <th style="background: var(--bg-hover); color: var(--text-muted); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; padding: 12px 14px; border-bottom: 2px solid var(--border); position: sticky; top: 0; z-index: 5;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentAnnotations as $annotation)
                <tr style="transition: var(--transition);" onmouseover="this.style.background='var(--primaire-muted)';this.style.transform='translateX(4px)'" onmouseout="this.style.background='';this.style.transform=''">
                    <td style="padding: 12px 14px; border-bottom: 1px solid var(--border); color: var(--text-secondary); vertical-align: middle;"><span style="font-size: 11px; font-weight: 700; color: var(--primaire); background: var(--primaire-pale); padding: 3px 10px; border-radius: 6px; font-family: 'SF Mono', 'Fira Code', monospace; letter-spacing: 0.3px;">{{ optional($annotation->courrier)->reference ?? '—' }}</span></td>
                    <td style="padding: 12px 14px; border-bottom: 1px solid var(--border); color: var(--text-secondary); vertical-align: middle; max-width:300px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $annotation->contenu }}">
                        {{ $annotation->contenu ?? '—' }}
                    </td>
                    <td style="padding: 12px 14px; border-bottom: 1px solid var(--border); color: var(--text-secondary); vertical-align: middle; font-size:12px;">{{ optional($annotation->utilisateur)->name ?? '—' }}</td>
                    <td style="padding: 12px 14px; border-bottom: 1px solid var(--border); color: var(--text-secondary); vertical-align: middle; white-space:nowrap;font-size:12px;">{{ $annotation->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                    <td style="padding: 12px 14px; border-bottom: 1px solid var(--border); color: var(--text-secondary); vertical-align: middle;">
                        <a href="{{ route('courriers.show', $annotation->courrier_id ?? '#') }}" style="background: none; border: 1px solid var(--border); border-radius: 8px; padding: 6px 10px; font-size: 12px; color: var(--text-muted); cursor: pointer; transition: var(--transition); text-decoration: none; display: inline-flex; align-items: center; gap: 4px;" onmouseover="this.style.borderColor='var(--primaire)';this.style.color='var(--primaire)';this.style.background='var(--primaire-pale)';this.style.transform='translateY(-1px)'" onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-muted)';this.style.background='';this.style.transform=''">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5"><div style="text-align: center; padding: 40px 20px; color: var(--text-muted);"><i class="fas fa-comment-slash" style="font-size: 32px; display: block; margin-bottom: 12px; opacity: 0.4;"></i><p style="font-size: 13px; margin: 0;">Aucune annotation récente</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
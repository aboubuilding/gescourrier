{{-- ══════════════════════════════════════════
     BANNIÈRE DE BIENVENUE + KPI CARDS
     ══════════════════════════════════════════ --}}

@php
    $totalCourriers    = $stats['total_courriers'] ?? 0;
    $totalAffectations = $stats['total_affectations'] ?? 0;
    $totalAnnotations  = $stats['total_annotations'] ?? 0;
    $totalUtilisateurs = $stats['total_utilisateurs'] ?? 0;
    $totalDirections   = $stats['total_directions'] ?? 0;
    
    $tauxAffectation = $totalCourriers > 0 ? min(100, round($totalAffectations / $totalCourriers * 100)) : 0;
    $tauxAnnotation  = $totalCourriers > 0 ? min(100, round($totalAnnotations / $totalCourriers * 100)) : 0;
@endphp

{{-- ══════════════════════════════════════════
     BANNIÈRE DE BIENVENUE
══════════════════════════════════════════ --}}
<div class="welcome-banner anim anim-1" style="grid-column: span 12; background: linear-gradient(135deg, var(--primaire-deep) 0%, var(--primaire) 55%, var(--primaire-light) 100%); border-radius: var(--radius-lg); padding: 24px 28px; display: flex; align-items: center; justify-content: space-between; gap: 20px; position: relative; overflow: hidden; box-shadow: var(--shadow-md);">
    
    <div class="welcome-left" style="position: relative; z-index: 1;">
        <div style="font-size: 11px; font-weight: 700; letter-spacing: 1.8px; text-transform: uppercase; color: rgba(255,255,255,0.7); margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
            <i class="fas fa-hand-wave"></i> Bienvenue sur le portail
        </div>
        <div style="font-family: 'Playfair Display', serif; font-size: clamp(22px, 3vw, 28px); font-weight: 700; color: #fff; margin-bottom: 6px; line-height: 1.2;">
            Bonjour, {{ auth()->user()->prenom ?? auth()->user()->name ?? 'Agent' }} 👋
        </div>
        <div style="font-size: 13px; color: rgba(255,255,255,0.75); display: flex; align-items: center; gap: 12px;">
            <span><i class="fas fa-calendar-alt me-1"></i>{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</span>
            <span><i class="fas fa-clock me-1"></i><span id="live-clock">{{ now()->format('H:i') }}</span></span>
        </div>
    </div>
    
    <div class="welcome-right" style="position: relative; z-index: 1; display: flex; gap: 14px; flex-shrink: 0;">
        <div style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); border-radius: 10px; padding: 12px 18px; text-align: center; min-width: 90px; backdrop-filter: blur(4px); transition: var(--transition);" title="Courriers actifs" onmouseover="this.style.background='rgba(255,255,255,0.18)';this.style.transform='translateY(-2px)'" onmouseout="this.style.background='rgba(255,255,255,0.12)';this.style.transform=''">
            <div style="font-size: 24px; font-weight: 800; color: #fff; line-height: 1; margin-bottom: 3px;" id="stat-courriers">{{ $totalCourriers }}</div>
            <div style="font-size: 10px; color: rgba(255,255,255,0.7); text-transform: uppercase; letter-spacing: 0.6px; display: flex; align-items: center; justify-content: center; gap: 4px;">
                <span style="display: inline-block; width: 6px; height: 6px; background: var(--or); border-radius: 50%; animation: pulse 2s infinite;"></span>Courriers
            </div>
        </div>
        <div style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); border-radius: 10px; padding: 12px 18px; text-align: center; min-width: 90px; backdrop-filter: blur(4px); transition: var(--transition);" title="Affectations en cours" onmouseover="this.style.background='rgba(255,255,255,0.18)';this.style.transform='translateY(-2px)'" onmouseout="this.style.background='rgba(255,255,255,0.12)';this.style.transform=''">
            <div style="font-size: 24px; font-weight: 800; color: #fff; line-height: 1; margin-bottom: 3px;" id="stat-affectations">{{ $totalAffectations }}</div>
            <div style="font-size: 10px; color: rgba(255,255,255,0.7); text-transform: uppercase; letter-spacing: 0.6px;">Affectations</div>
        </div>
        <div style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); border-radius: 10px; padding: 12px 18px; text-align: center; min-width: 90px; backdrop-filter: blur(4px); transition: var(--transition);" title="Directions actives" onmouseover="this.style.background='rgba(255,255,255,0.18)';this.style.transform='translateY(-2px)'" onmouseout="this.style.background='rgba(255,255,255,0.12)';this.style.transform=''">
            <div style="font-size: 24px; font-weight: 800; color: #fff; line-height: 1; margin-bottom: 3px;" id="stat-directions">{{ $totalDirections }}</div>
            <div style="font-size: 10px; color: rgba(255,255,255,0.7); text-transform: uppercase; letter-spacing: 0.6px;">Directions</div>
        </div>
    </div>
    
    <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #009a44 0% 33%, #ffcc00 33% 66%, #ffffff 66% 100%); opacity: 0.6;"></div>
</div>

{{-- ══════════════════════════════════════════
     KPI CARDS
══════════════════════════════════════════ --}}

{{-- KPI 1 : Total courriers --}}
<div class="kpi-card anim anim-2" data-href="{{ route('courriers.index') }}" style="grid-column: span 3; background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 20px; display: flex; flex-direction: column; gap: 14px; box-shadow: var(--shadow-sm); transition: var(--transition); position: relative; overflow: hidden; cursor: pointer;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='var(--shadow-lg)';this.style.borderColor='var(--primaire)'" onmouseout="this.style.transform='';this.style.boxShadow='var(--shadow-sm)';this.style.borderColor='var(--border)'">
    <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--primaire); border-radius: var(--radius-lg) 0 0 var(--radius-lg); transition: width 0.3s ease;"></div>
    
    <div style="display: flex; align-items: flex-start; justify-content: space-between;">
        <div style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; background: var(--primaire-pale); color: var(--primaire); transition: var(--transition);" onmouseover="this.style.transform='scale(1.1) rotate(5deg)'" onmouseout="this.style.transform=''">
            <i class="fas fa-envelope"></i>
        </div>
        <span style="display: flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px; background: {{ $tauxAffectation >= 80 ? 'var(--success)' : ($tauxAffectation >= 50 ? 'var(--bg-hover)' : 'var(--danger)') }}; color: {{ $tauxAffectation >= 80 ? '#fff' : ($tauxAffectation >= 50 ? 'var(--text-secondary)' : '#fff') }};">
            <i class="fas fa-{{ $tauxAffectation >= 80 ? 'arrow-up' : ($tauxAffectation >= 50 ? 'minus' : 'arrow-down') }}"></i>
            {{ $tauxAffectation }}%
        </span>
    </div>
    
    <div>
        <div class="kpi-val" data-count="{{ $totalCourriers }}" style="font-size: clamp(28px, 4vw, 34px); font-weight: 800; color: var(--text-primary); line-height: 1; transition: color 0.2s ease;">0</div>
        <div style="font-size: 12px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px;">Total courriers</div>
    </div>
    
    <div>
        <div style="height: 5px; background: var(--bg-hover); border-radius: 4px; overflow: hidden; margin-top: 8px;">
            <div class="kpi-bar-fill" data-width="{{ $tauxAffectation }}" style="height: 100%; border-radius: 4px; background: var(--primaire); transition: width 1.2s cubic-bezier(0.4,0,0.2,1); width: 0%;"></div>
        </div>
        <div style="font-size: 12px; color: var(--text-muted); display: flex; align-items: center; gap: 6px; margin-top: 8px;">
            <i class="fas fa-circle" style="font-size:5px;"></i>
            {{ $tauxAffectation }}% affectés
        </div>
    </div>
    
    <div style="position: absolute; bottom: -30px; right: -30px; width: 100px; height: 100px; border-radius: 50%; opacity: 0.06; background: var(--primaire); transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform=''"></div>
</div>

{{-- KPI 2 : Affectations --}}
<div class="kpi-card anim anim-3" data-href="" style="grid-column: span 3; background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 20px; display: flex; flex-direction: column; gap: 14px; box-shadow: var(--shadow-sm); transition: var(--transition); position: relative; overflow: hidden; cursor: pointer;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='var(--shadow-lg)';this.style.borderColor='var(--info)'" onmouseout="this.style.transform='';this.style.boxShadow='var(--shadow-sm)';this.style.borderColor='var(--border)'">
    <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--info); border-radius: var(--radius-lg) 0 0 var(--radius-lg); transition: width 0.3s ease;"></div>
    
    <div style="display: flex; align-items: flex-start; justify-content: space-between;">
        <div style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; background: var(--bleu-pale); color: var(--info); transition: var(--transition);" onmouseover="this.style.transform='scale(1.1) rotate(5deg)'" onmouseout="this.style.transform=''">
            <i class="fas fa-share-alt"></i>
        </div>
        <span style="display: flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px; background: var(--bg-hover); color: var(--text-secondary);">
            <i class="fas fa-minus"></i> En cours
        </span>
    </div>
    
    <div>
        <div class="kpi-val" data-count="{{ $totalAffectations }}" style="font-size: clamp(28px, 4vw, 34px); font-weight: 800; color: var(--text-primary); line-height: 1; transition: color 0.2s ease;">0</div>
        <div style="font-size: 12px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px;">Affectations</div>
    </div>
    
    <div>
        <div style="height: 5px; background: var(--bg-hover); border-radius: 4px; overflow: hidden; margin-top: 8px;">
            <div class="kpi-bar-fill" data-width="{{ min(100, $totalAffectations) }}" style="height: 100%; border-radius: 4px; background: var(--info); transition: width 1.2s cubic-bezier(0.4,0,0.2,1); width: 0%;"></div>
        </div>
        <div style="font-size: 12px; color: var(--text-muted); display: flex; align-items: center; gap: 6px; margin-top: 8px;">
            <i class="fas fa-circle" style="font-size:5px;"></i>
            Sur {{ $totalCourriers }} courrier{{ $totalCourriers != 1 ? 's' : '' }}
        </div>
    </div>
    
    <div style="position: absolute; bottom: -30px; right: -30px; width: 100px; height: 100px; border-radius: 50%; opacity: 0.06; background: var(--info); transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform=''"></div>
</div>

{{-- KPI 3 : Annotations --}}
<div class="kpi-card anim anim-4" data-href="" style="grid-column: span 3; background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 20px; display: flex; flex-direction: column; gap: 14px; box-shadow: var(--shadow-sm); transition: var(--transition); position: relative; overflow: hidden; cursor: pointer;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='var(--shadow-lg)';this.style.borderColor='var(--success)'" onmouseout="this.style.transform='';this.style.boxShadow='var(--shadow-sm)';this.style.borderColor='var(--border)'">
    <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--success); border-radius: var(--radius-lg) 0 0 var(--radius-lg); transition: width 0.3s ease;"></div>
    
    <div style="display: flex; align-items: flex-start; justify-content: space-between;">
        <div style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; background: var(--vert-pale); color: var(--success); transition: var(--transition);" onmouseover="this.style.transform='scale(1.1) rotate(5deg)'" onmouseout="this.style.transform=''">
            <i class="fas fa-comment-alt"></i>
        </div>
        <span style="display: flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px; background: {{ $tauxAnnotation >= 70 ? 'var(--success)' : 'var(--danger)' }}; color: {{ $tauxAnnotation >= 70 ? '#fff' : '#fff' }};">
            <i class="fas fa-{{ $tauxAnnotation >= 70 ? 'arrow-up' : 'arrow-down' }}"></i>
            {{ $tauxAnnotation }}%
        </span>
    </div>
    
    <div>
        <div class="kpi-val" data-count="{{ $totalAnnotations }}" style="font-size: clamp(28px, 4vw, 34px); font-weight: 800; color: var(--text-primary); line-height: 1; transition: color 0.2s ease;">0</div>
        <div style="font-size: 12px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px;">Annotations</div>
    </div>
    
    <div>
        <div style="height: 5px; background: var(--bg-hover); border-radius: 4px; overflow: hidden; margin-top: 8px;">
            <div class="kpi-bar-fill" data-width="{{ $tauxAnnotation }}" style="height: 100%; border-radius: 4px; background: var(--success); transition: width 1.2s cubic-bezier(0.4,0,0.2,1); width: 0%;"></div>
        </div>
        <div style="font-size: 12px; color: var(--text-muted); display: flex; align-items: center; gap: 6px; margin-top: 8px;">
            <i class="fas fa-circle" style="font-size:5px;"></i>
            {{ $tauxAnnotation }}% annotés
        </div>
    </div>
    
    <div style="position: absolute; bottom: -30px; right: -30px; width: 100px; height: 100px; border-radius: 50%; opacity: 0.06; background: var(--success); transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform=''"></div>
</div>

{{-- KPI 4 : Utilisateurs --}}
<div class="kpi-card anim anim-5" data-href="{{ route('users.index') }}" style="grid-column: span 3; background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 20px; display: flex; flex-direction: column; gap: 14px; box-shadow: var(--shadow-sm); transition: var(--transition); position: relative; overflow: hidden; cursor: pointer;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='var(--shadow-lg)';this.style.borderColor='var(--warning)'" onmouseout="this.style.transform='';this.style.boxShadow='var(--shadow-sm)';this.style.borderColor='var(--border)'">
    <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--warning); border-radius: var(--radius-lg) 0 0 var(--radius-lg); transition: width 0.3s ease;"></div>
    
    <div style="display: flex; align-items: flex-start; justify-content: space-between;">
        <div style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; background: var(--or-pale); color: var(--warning); transition: var(--transition);" onmouseover="this.style.transform='scale(1.1) rotate(5deg)'" onmouseout="this.style.transform=''">
            <i class="fas fa-users"></i>
        </div>
        <span style="display: flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px; background: var(--bg-hover); color: var(--text-secondary);">
            <i class="fas fa-users"></i> Actifs
        </span>
    </div>
    
    <div>
        <div class="kpi-val" data-count="{{ $totalUtilisateurs }}" style="font-size: clamp(28px, 4vw, 34px); font-weight: 800; color: var(--text-primary); line-height: 1; transition: color 0.2s ease;">0</div>
        <div style="font-size: 12px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px;">Utilisateurs</div>
    </div>
    
    <div>
        <div style="height: 5px; background: var(--bg-hover); border-radius: 4px; overflow: hidden; margin-top: 8px;">
            <div class="kpi-bar-fill" data-width="{{ min(100, $totalUtilisateurs * 10) }}" style="height: 100%; border-radius: 4px; background: var(--warning); transition: width 1.2s cubic-bezier(0.4,0,0.2,1); width: 0%;"></div>
        </div>
        <div style="font-size: 12px; color: var(--text-muted); display: flex; align-items: center; gap: 6px; margin-top: 8px;">
            <i class="fas fa-circle" style="font-size:5px;"></i>
            {{ $totalDirections }} direction{{ $totalDirections != 1 ? 's' : '' }}
        </div>
    </div>
    
    <div style="position: absolute; bottom: -30px; right: -30px; width: 100px; height: 100px; border-radius: 50%; opacity: 0.06; background: var(--warning); transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform=''"></div>
</div>
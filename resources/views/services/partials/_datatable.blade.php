<div class="table-panel">
    <div class="table-panel-head">
        <h5 class="table-panel-title"><i class="fas fa-list"></i> Liste des Services</h5>
        <span class="count-badge" id="tableCount">{{ $total }} service{{ $total != 1 ? 's' : '' }}</span>
    </div>
    <div class="table-responsive">
        <table id="servicesTable" class="dataTable w-100">
            <thead>
                <tr><th>Service</th><th>Organisation</th><th>Agents</th><th>État</th><th class="text-center">Actions</th></tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                @php
                    // 🔁 SOLUTION : Conversion sécurisée array → objet
                    $s = is_array($service) ? (object) $service : $service;
                    
                    // Gestion des relations qui pourraient aussi être des arrays
                    $org = $s->organisation ?? null;
                    if (is_array($org)) $org = (object) $org;
                    
                    // ✅ Cast en int pour éviter "Illegal offset type"
                    $etatVal = (int) ($s->etat ?? $S::ETAT_ACTIF);
                    
                    $statutClass = $etatVal == $S::ETAT_ACTIF ? 'actif' : 'inactif';
                    $statutLabel = $etatVal == $S::ETAT_ACTIF ? 'Actif' : 'Inactif';
                @endphp
                <tr data-id="{{ $s->id ?? '' }}"
                    data-etat="{{ $etatVal }}"
                    data-organisation="{{ $s->organisation_id ?? '' }}"
                    data-search="{{ strtolower(($s->nom ?? '').' '.($org->nom ?? '').' '.($org->sigle ?? '')) }}">
                    
                    {{-- Service --}}
                    <td>
                        <div class="service-cell">
                            <div class="service-icon"><i class="fas fa-layer-group"></i></div>
                            <div class="service-info">
                                <div class="service-name">{{ $s->nom ?? '—' }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Organisation --}}
                    <td>
                        @if($org && !empty($org->nom))
                            <span class="org-chip" title="{{ $org->nom ?? '' }}">
                                <i class="fas fa-sitemap"></i> {{ $org->nom ?? '—' }}
                                @if(!empty($org->sigle))<span class="text-muted">({{ $org->sigle }})</span>@endif
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    {{-- Agents affectés --}}
                    <td>
                        @if(isset($s->agents_count) && $s->agents_count > 0)
                            <span class="agents-badge"><i class="fas fa-users"></i> {{ $s->agents_count }}</span>
                        @else
                            <span class="text-muted small">Aucun</span>
                        @endif
                    </td>

                    {{-- État --}}
                    <td><span class="badge-statut {{ $statutClass }}">{{ $statutLabel }}</span></td>

                    {{-- Actions --}}
                    <td class="text-center">
                        <div class="action-dropdown">
                            <button class="action-trigger"><i class="fas fa-ellipsis-v"></i></button>
                            <div class="action-menu">
                                <button class="action-item btn-edit" data-id="{{ $s->id ?? '' }}"><i class="fas fa-pen"></i> Modifier</button>
                                <button class="action-item btn-agents" data-id="{{ $s->id ?? '' }}"><i class="fas fa-users"></i> Voir les agents</button>
                                <div class="action-divider"></div>
                                @if($etatVal == $S::ETAT_ACTIF)
                                <button class="action-item danger btn-suspend" data-id="{{ $s->id ?? '' }}"><i class="fas fa-pause"></i> Désactiver</button>
                                @else
                                <button class="action-item btn-restore" data-id="{{ $s->id ?? '' }}"><i class="fas fa-play"></i> Réactiver</button>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5"><div class="empty-state"><i class="fas fa-layer-group"></i><p>Aucun service enregistré</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
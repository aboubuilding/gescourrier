<div class="table-panel">
    <div class="table-panel-head">
        <h5 class="table-panel-title"><i class="fas fa-list"></i> Liste des Agents</h5>
        <span class="count-badge" id="tableCount">{{ $total }} agent{{ $total != 1 ? 's' : '' }}</span>
    </div>
    <div class="table-responsive">
        <table id="agentsTable" class="dataTable w-100">
            <thead>
                <tr><th>Agent</th><th>Contact</th><th>Fonction</th><th>Service</th><th>Compte</th><th>État</th><th class="text-center">Actions</th></tr>
            </thead>
            <tbody>
                @forelse($agents as $agent)
                @php
                    // 🔁 SOLUTION : Accès sécurisé aux données (array ou objet)
                    $a = is_array($agent) ? $agent : (object) $agent;
                    
                    // Accès aux propriétés avec fallback
                    $id = $a['id'] ?? $a->id ?? null;
                    $nom = $a['nom'] ?? $a->nom ?? '—';
                    $prenom = $a['prenom'] ?? $a->prenom ?? '';
                    $email = $a['email'] ?? $a->email ?? null;
                    $telephone = $a['telephone'] ?? $a->telephone ?? null;
                    $fonction = $a['fonction'] ?? $a->fonction ?? 'agent';
                    $etat = (int) ($a['etat'] ?? $a->etat ?? 1);
                    $service_id = $a['service_id'] ?? $a->service_id ?? null;
                    
                    // Service (peut être array ou objet ou null)
                    $svc = $a['service'] ?? $a->service ?? null;
                    if (is_array($svc)) $svc = (object) $svc;
                    $serviceNom = $svc?->nom ?? '—';
                    
                    // User (pour afficher "Lié" ou bouton "Lier")
                    $user = $a['user'] ?? $a->user ?? null;
                    $hasUser = !empty($user) || (is_object($user) && !empty($user->id));
                    
                    // Maps pour l'affichage
                    $functionMap = [
                        'chef' => ['label'=>'Chef de service','class'=>'chef','icon'=>'fa-crown'],
                        'secretaire' => ['label'=>'Secrétaire','class'=>'secretaire','icon'=>'fa-keyboard'],
                        'gestionnaire' => ['label'=>'Gestionnaire','class'=>'gestionnaire','icon'=>'fa-folder-open'],
                        'agent' => ['label'=>'Agent','class'=>'agent','icon'=>'fa-user'],
                    ];
                    $f = $functionMap[$fonction] ?? $functionMap['agent'];
                    
                    $statutClass = $etat == 1 ? 'actif' : ($etat == 2 ? 'inactif' : 'suspendu');
                    $statutLabel = $etat == 1 ? 'Actif' : ($etat == 2 ? 'Inactif' : 'Suspendu');
                    $initials = strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1));
                @endphp
                <tr data-id="{{ $id }}"
                    data-fonction="{{ $fonction }}"
                    data-etat="{{ $etat }}"
                    data-service="{{ $service_id }}"
                    data-search="{{ strtolower($nom.' '.$prenom.' '.$email.' '.$fonction) }}">
                    
                    {{-- Agent --}}
                    <td>
                        <div class="agent-cell">
                            <div class="agent-avatar">{{ $initials }}</div>
                            <div class="agent-info">
                                <div class="agent-name">{{ $nom }} {{ $prenom }}</div>
                                <div class="agent-email">{{ $email ?? '—' }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Contact --}}
                    <td>
                        @if($telephone)
                            <a href="tel:{{ $telephone }}" class="text-decoration-none" title="Appeler">
                                <i class="fas fa-phone text-muted me-1"></i>
                                <span class="text-muted small">{{ $telephone }}</span>
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    {{-- Fonction --}}
                    <td><span class="badge-function {{ $f['class'] }}"><i class="fas {{ $f['icon'] }}"></i>{{ $f['label'] }}</span></td>

                    {{-- Service --}}
                    <td>
                        @if($serviceNom && $serviceNom !== '—')
                            <span class="service-chip" title="{{ $serviceNom }}">
                                <i class="fas fa-network-wired"></i> {{ $serviceNom }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    {{-- Compte utilisateur --}}
                    <td>
                        @if($hasUser)
                            <span class="badge bg-success-subtle text-success"><i class="fas fa-check"></i> Lié</span>
                        @else
                            <button class="btn btn-sm btn-outline-primary btn-link-user" data-id="{{ $id }}">
                                <i class="fas fa-link"></i> Lier
                            </button>
                        @endif
                    </td>

                    {{-- État --}}
                    <td><span class="badge-statut {{ $statutClass }}">{{ $statutLabel }}</span></td>

                    {{-- Actions --}}
                    <td class="text-center">
                        <div class="action-dropdown">
                            <button class="action-trigger"><i class="fas fa-ellipsis-v"></i></button>
                            <div class="action-menu">
                                <button class="action-item btn-edit" data-id="{{ $id }}"><i class="fas fa-pen"></i> Modifier</button>
                                @if(!$hasUser)
                                <button class="action-item btn-link-user" data-id="{{ $id }}"><i class="fas fa-user-plus"></i> Lier un compte</button>
                                @endif
                                @if($service_id)
                                <button class="action-item btn-reassign" data-id="{{ $id }}"><i class="fas fa-share-alt"></i> Réassigner service</button>
                                @endif
                                <div class="action-divider"></div>
                                @if($etat == 1)
                                <button class="action-item danger btn-suspend" data-id="{{ $id }}"><i class="fas fa-pause"></i> Suspendre</button>
                                @else
                                <button class="action-item btn-restore" data-id="{{ $id }}"><i class="fas fa-play"></i> Réactiver</button>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><div class="empty-state"><i class="fas fa-user-slash"></i><p>Aucun agent enregistré</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
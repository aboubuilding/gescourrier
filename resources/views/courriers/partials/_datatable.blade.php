<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
        <div>
            <h5 class="mb-0 text-primary fw-bold">
                <i class="fas fa-envelope-shield me-2"></i>Gestion du Courrier
            </h5>
            <small class="text-muted">
                {{ count($courriers) }} dossier(s) affiché(s)
            </small>
        </div>
        
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="courriersTable">
            <thead class="bg-light">
                <tr>
                    <th class="border-0 ps-3">Identification</th>
                    <th class="border-0">Objet & Organisation</th>
                    <th class="border-0">Classification</th>
                    <th class="border-0">Assignation</th>
                    <th class="border-0">Fichier</th>
                    <th class="border-0 text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courriers as $c)
                <tr>
                    <td class="ps-3">
                        <div class="d-flex align-items-center">
                            <div class="type-icon me-3 {{ $c['type']['libelle'] == 'Entrant' ? 'entrant' : 'sortant' }}">
                                <i class="fas {{ $c['type']['libelle'] == 'Entrant' ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">{{ $c['reference'] }}</div>
                                <div class="small text-muted">№ {{ $c['numero'] ?? '—' }}</div>
                                <div class="extra-small text-secondary mt-1">
                                    <i class="far fa-calendar-alt"></i> {{ $c['dates']['reception'] ?? $c['dates']['envoi'] ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </td>

                    <td>
                        <div class="text-truncate fw-semibold" style="max-width: 250px;" title="{{ $c['objet'] }}">
                            {{ $c['objet'] }}
                        </div>
                        <div class="small text-primary">
                            <i class="fas fa-building me-1"></i>
                            {{ $c['acteurs']['organisation']['sigle'] ?? $c['acteurs']['organisation']['nom'] ?? 'Sans organisation' }}
                        </div>
                    </td>

                    <td>
                        <div class="mb-1">
                            <span class="badge-status {{ strtolower(str_replace(' ', '-', $c['statut']['libelle'])) }}">
                                {{ $c['statut']['libelle'] }}
                            </span>
                        </div>
                        <div class="extra-small fw-bold text-uppercase">
                            <i class="fas fa-circle me-1 {{ $c['priorite']['libelle'] == 'Urgente' ? 'text-danger' : 'text-success' }}"></i>
                            {{ $c['priorite']['libelle'] }}
                        </div>
                    </td>

                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle me-2">
                                {{ substr($c['acteurs']['service']['nom'] ?? 'S', 0, 1) }}
                            </div>
                            <div>
                                <div class="small fw-bold text-dark">{{ $c['acteurs']['service']['nom'] ?? 'Non affecté' }}</div>
                                <div class="extra-small text-muted italic">{{ $c['acteurs']['agent']['nom'] ?? 'En attente' }}</div>
                            </div>
                        </div>
                    </td>

                    <td>
                        @if($c['fichier'])
                            <a href="{{ $c['fichier']['url'] }}" target="_blank" class="file-box">
                                <i class="far fa-file-pdf text-danger fa-lg"></i>
                                <div class="ms-2">
                                    <div class="extra-small fw-bold">{{ $c['fichier']['taille_formatee'] }}</div>
                                    <div class="extra-small text-muted text-uppercase">PDF</div>
                                </div>
                            </a>
                        @else
                            <span class="text-muted extra-small italic">Aucun scan</span>
                        @endif
                    </td>

                    <td class="text-end pe-3">
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle border shadow-sm" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                                <li><a class="dropdown-item py-2" href="{{ route('courriers.show', $c['id']) }}"><i class="fas fa-eye me-2 text-info"></i> Voir le dossier</a></li>
                                <li><button class="dropdown-item py-2 btn-edit" data-id="{{ $c['id'] }}"><i class="fas fa-pen me-2 text-primary"></i> Modifier</button></li>
                                <li><button class="dropdown-item py-2 btn-affecter" data-id="{{ $c['id'] }}"><i class="fas fa-share-nodes me-2 text-warning"></i> Affecter</button></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><button class="dropdown-item py-2 text-danger btn-delete" data-id="{{ $c['id'] }}"><i class="fas fa-trash me-2"></i> Supprimer</button></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 opacity-20"></i>
                            <p>Aucun courrier ne correspond à vos critères.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
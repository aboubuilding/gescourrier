{{-- CRÉATION --}}
<div class="modal fade" id="modalCreate" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formCreate" action="{{ route('agents.store') }}" method="POST">@csrf
                <div class="modal-header"><h5 class="modal-title"><i class="fas fa-plus-circle text-success"></i> Nouvel agent</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body"><div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Nom *</label><input type="text" name="nom" class="form-control" required placeholder="Nom de famille"></div>
                    <div class="col-md-6"><label class="form-label">Prénom *</label><input type="text" name="prenom" class="form-control" required placeholder="Prénom"></div>
                    <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" placeholder="email@exemple.tg"><div class="form-text">Optionnel si sans compte</div></div>
                    <div class="col-md-6"><label class="form-label">Téléphone</label><input type="tel" name="telephone" class="form-control" placeholder="+228 XX XX XX XX"></div>
                    <div class="col-md-6"><label class="form-label">Fonction *</label><select name="fonction" class="form-select" required><option value="">Sélectionner</option><option value="chef">Chef de service</option><option value="secretaire">Secrétaire</option><option value="gestionnaire">Gestionnaire courrier</option><option value="agent">Agent de saisie</option><option value="charge_mission">Chargé de mission</option></select></div>
                    <div class="col-md-6"><label class="form-label">Service *</label><select name="service_id" class="form-select select2" required><option value="">Sélectionner</option>@foreach($services as $s)<option value="{{ $s['id'] ?? $s->id }}">{{ $s['nom'] ?? $s->nom }}</option>@endforeach</select></div>
                    <div class="col-12"><label class="form-label">Compte utilisateur (optionnel)</label><select name="user_id" class="form-select select2"><option value="">Aucun compte</option>@foreach($users as $u)<option value="{{ $u['id'] ?? $u->id }}">{{ $u['name'] ?? $u->name }} ({{ $u['email'] ?? $u->email }})</option>@endforeach</select><div class="form-text">Permet à l'agent de se connecter</div></div>
                </div></div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-success" id="btnSubmitCreate"><span class="spinner-border spinner-border-sm d-none" id="spinnerCreate"></span> Créer</button></div>
            </form>
        </div>
    </div>
</div>

{{-- MODIFICATION --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formEdit" method="POST">@csrf @method('PUT')
                <div class="modal-header"><h5 class="modal-title"><i class="fas fa-pen text-warning"></i> Modifier</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body"><input type="hidden" name="id" id="editId"><div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Nom *</label><input type="text" name="nom" id="editNom" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label">Prénom *</label><input type="text" name="prenom" id="editPrenom" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" id="editEmail" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">Téléphone</label><input type="tel" name="telephone" id="editTelephone" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">Fonction *</label><select name="fonction" id="editFonction" class="form-select" required><option value="chef">Chef</option><option value="secretaire">Secrétaire</option><option value="gestionnaire">Gestionnaire</option><option value="agent">Agent</option><option value="charge_mission">Chargé de mission</option></select></div>
                    <div class="col-md-6"><label class="form-label">Service *</label><select name="service_id" id="editService" class="form-select select2" required>@foreach($services as $s)<option value="{{ $s['id'] ?? $s->id }}">{{ $s['nom'] ?? $s->nom }}</option>@endforeach</select></div>
                    <div class="col-12"><label class="form-label">Compte utilisateur</label><select name="user_id" id="editUser" class="form-select select2"><option value="">Aucun</option>@foreach($users as $u)<option value="{{ $u['id'] ?? $u->id }}">{{ $u['name'] ?? $u->name }} ({{ $u['email'] ?? $u->email }})</option>@endforeach</select></div>
                </div></div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-warning" id="btnSubmitEdit"><span class="spinner-border spinner-border-sm d-none" id="spinnerEdit"></span> Mettre à jour</button></div>
            </form>
        </div>
    </div>
</div>

{{-- LIER COMPTE --}}
<div class="modal fade" id="modalLinkUser" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formLinkUser" method="POST">@csrf <input type="hidden" name="agent_id" id="linkAgentId">
                <div class="modal-header"><h5 class="modal-title"><i class="fas fa-user-plus text-primary"></i> Lier un compte</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body"><p class="text-muted mb-3">Sélectionnez le compte à associer.</p>
                    <div class="mb-3"><label class="form-label">Compte *</label><select name="user_id" class="form-select select2" required><option value="">Sélectionner</option>@foreach($users as $u)<option value="{{ $u['id'] ?? $u->id }}">{{ $u['name'] ?? $u->name }} — {{ $u['email'] ?? $u->email }}</option>@endforeach</select></div>
                    <div class="alert alert-info small"><i class="fas fa-info-circle"></i> L'agent pourra se connecter avec ce compte.</div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary"><span class="spinner-border spinner-border-sm d-none" id="spinnerLink"></span> Lier</button></div>
            </form>
        </div>
    </div>
</div>

{{-- RÉASSIGNER SERVICE --}}
<div class="modal fade" id="modalReassign" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formReassign" method="POST">@csrf <input type="hidden" name="agent_id" id="reassignAgentId">
                <div class="modal-header"><h5 class="modal-title"><i class="fas fa-share-alt text-info"></i> Réassigner service</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body"><p class="text-muted mb-3">Changer le service de rattachement.</p>
                    <div class="mb-3"><label class="form-label">Nouveau service *</label><select name="service_id" class="form-select select2" required><option value="">Sélectionner</option>@foreach($services as $s)<option value="{{ $s['id'] ?? $s->id }}">{{ $s['nom'] ?? $s->nom }}</option>@endforeach</select></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-info"><span class="spinner-border spinner-border-sm d-none" id="spinnerReassign"></span> Réassigner</button></div>
            </form>
        </div>
    </div>
</div>

{{-- EXPORT --}}
<div class="modal fade" id="modalExport" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="fas fa-download text-success"></i> Exporter</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body text-center py-4"><p class="mb-4">Format d'export :</p><div class="d-grid gap-3">
            <a href="#" class="btn btn-outline-success btn-lg" id="exportExcel"><i class="fas fa-file-excel fa-2x mb-2"></i><br>Excel (.xlsx)</a>
            <a href="#" class="btn btn-outline-danger btn-lg" id="exportPDF"><i class="fas fa-file-pdf fa-2x mb-2"></i><br>PDF (.pdf)</a>
            <a href="#" class="btn btn-outline-primary btn-lg" id="exportCSV"><i class="fas fa-file-csv fa-2x mb-2"></i><br>CSV (.csv)</a>
        </div></div>
    </div></div>
</div>
{{-- CRÉATION --}}
<div class="modal fade" id="modalCreate" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formCreate" action="{{ route('services.store') }}" method="POST">@csrf
                <div class="modal-header"><h5 class="modal-title"><i class="fas fa-plus-circle text-success"></i> Nouveau service</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body"><div class="row g-3">
                    <div class="col-12"><label class="form-label">Nom du service *</label><input type="text" name="nom" class="form-control" required placeholder="Ex: Ressources Humaines"><div class="form-text">Nom unique au sein de l'organisation</div></div>
                    <div class="col-12"><label class="form-label">Organisation *</label><select name="organisation_id" class="form-select select2" required><option value="">Sélectionner</option>@foreach($organisations as $org)<option value="{{ $org->id }}">{{ $org->nom }} @if(!empty($org->sigle))({{ $org->sigle }})@endif</option>@endforeach</select></div>
                </div></div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-success" id="btnSubmitCreate"><span class="spinner-border spinner-border-sm d-none" id="spinnerCreate"></span> Créer</button></div>
            </form>
        </div>
    </div>
</div>

{{-- MODIFICATION --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formEdit" method="POST">@csrf @method('PUT')
                <div class="modal-header"><h5 class="modal-title"><i class="fas fa-pen text-warning"></i> Modifier</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body"><input type="hidden" name="id" id="editId"><div class="row g-3">
                    <div class="col-12"><label class="form-label">Nom *</label><input type="text" name="nom" id="editNom" class="form-control" required></div>
                    <div class="col-12"><label class="form-label">Organisation *</label><select name="organisation_id" id="editOrganisation" class="form-select select2" required>@foreach($organisations as $org)<option value="{{ $org->id }}">{{ $org->nom }} @if(!empty($org->sigle))({{ $org->sigle }})@endif</option>@endforeach</select></div>
                </div></div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-warning" id="btnSubmitEdit"><span class="spinner-border spinner-border-sm d-none" id="spinnerEdit"></span> Mettre à jour</button></div>
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
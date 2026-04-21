{{-- ============================================
     BARRE DE FILTRES
============================================ --}}
<div class="filter-bar">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="filter-label">Rechercher</label>
            <input type="text" id="searchInput" class="filter-input" placeholder="Nom, sigle, email, téléphone…">
        </div>
        <div class="col-md-2">
            <label class="filter-label">Type</label>
            <select id="filterType" class="filter-select">
                <option value="">Tous</option>
                <option value="0">Externe</option>
                <option value="1">Interne</option>
                <option value="2">Gouvernementale</option>
                <option value="3">Privée</option>
                <option value="4">ONG</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="filter-label">État</label>
            <select id="filterEtat" class="filter-select">
                <option value="">Tous</option>
                <option value="1">Actif</option>
                <option value="0">Inactif</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="filter-label">Catégorie</label>
            <div class="type-pills">
                <span class="type-pill active" data-type="">Tous</span>
                <span class="type-pill" data-type="0">Externes</span>
                <span class="type-pill" data-type="1">Internes</span>
                <span class="type-pill" data-type="2">Gouv.</span>
                <span class="type-pill" data-type="3">Privées</span>
                <span class="type-pill" data-type="4">ONG</span>
            </div>
        </div>
        <div class="col-md-1">
            <button type="button" id="btnResetFilters" class="btn-filter-reset w-100">
                <i class="fas fa-redo"></i>
            </button>
        </div>
    </div>
</div>
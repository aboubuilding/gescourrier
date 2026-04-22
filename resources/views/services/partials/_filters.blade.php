{{-- =========================
     FILTER BAR
========================= --}}

<div class="filter-bar">

    <div class="row g-3 align-items-end">

        {{-- 🔎 Recherche --}}
        <div class="col-md-5">
            <label class="filter-label">Rechercher</label>
            <input type="text"
                   id="searchInput"
                   class="filter-input"
                   placeholder="Nom du service…">
        </div>

        {{-- 📌 État --}}
        <div class="col-md-3">
            <label class="filter-label">État</label>
            <select id="filterEtat" class="filter-select">
                <option value="">Tous</option>
                <option value="1">Actif</option>
                <option value="2">Inactif</option>
            </select>
        </div>

        {{-- 📊 Situation métier --}}
        <div class="col-md-3">
            <label class="filter-label">Situation</label>
            <select id="filterSituation" class="filter-select">
                <option value="">Tous</option>
                <option value="sans_courrier">Sans courrier</option>
                <option value="sans_agents">Sans agents</option>
            </select>
        </div>

        {{-- 🔄 Reset --}}
        <div class="col-md-1">
            <label class="filter-label">&nbsp;</label>
            <button type="button"
                    id="btnResetFilters"
                    class="btn-filter-reset w-100">
                <i class="fas fa-redo"></i>
            </button>
        </div>

    </div>
</div>
<!-- ═══════════════════════════════════════
     🔍 SEARCH MODAL
═══════════════════════════════════════ -->
<div class="search-modal" id="searchModal" role="dialog" aria-modal="true" aria-label="Recherche">
    <div class="search-modal-content">
        <div class="search-modal-header">
            <i class="fas fa-search" style="color: var(--text-muted);"></i>
            <input type="text" class="search-modal-input" placeholder="Rechercher un courrier, un agent, une organisation..." aria-label="Champ de recherche">
            <button class="search-modal-close" id="searchModalClose" aria-label="Fermer la recherche">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="search-modal-body">
            <div class="search-result-item" tabindex="0">
                <div class="search-result-icon"><i class="fas fa-envelope"></i></div>
                <div class="search-result-info">
                    <div class="search-result-title">Courrier #REF-2024-001</div>
                    <div class="search-result-sub">Demande de subvention · Service Finances</div>
                </div>
            </div>
            <div class="search-result-item" tabindex="0">
                <div class="search-result-icon"><i class="fas fa-user"></i></div>
                <div class="search-result-info">
                    <div class="search-result-title">Agent: Konan Amadou</div>
                    <div class="search-result-sub">Gestionnaire · Ressources Humaines</div>
                </div>
            </div>
            <div class="search-result-item" tabindex="0">
                <div class="search-result-icon"><i class="fas fa-building"></i></div>
                <div class="search-result-info">
                    <div class="search-result-title">Organisation: MINFI</div>
                    <div class="search-result-sub">Ministère des Finances · Gouvernementale</div>
                </div>
            </div>
        </div>
    </div>
</div>
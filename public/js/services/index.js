/**
 * services/index.js
 * Point d'entrée — appelle tous les modules après DOM ready
 */

$(document).ready(function () {

   

    // ─────────────────────────────────────
    // 2. MODULES CRUD SERVICES
    // ─────────────────────────────────────

    if (typeof initCreateServiceModal === 'function') {
        initCreateServiceModal();
    }

    if (typeof initEditServiceModal === 'function') {
        initEditServiceModal();
    }

    if (typeof initShowServiceModal === 'function') {
        initShowServiceModal();
    }

    if (typeof initServiceActions === 'function') {
        initServiceActions();
    }

    // ─────────────────────────────────────
    // 3. FILTRES & HELPERS
    // ─────────────────────────────────────

    if (typeof initServiceFilters === 'function') {
        initServiceFilters();
    }

    if (typeof initServiceSelect2Helpers === 'function') {
        initServiceSelect2Helpers();
    }

    // ─────────────────────────────────────
    // 4. TOOLTIP BOOTSTRAP GLOBAL
    // ─────────────────────────────────────

    $(document).on('ajaxComplete', function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });

});
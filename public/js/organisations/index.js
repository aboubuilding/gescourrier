/**
 * organisations/index.js
 * Point d'entrée — appelle tous les modules après DOM ready
 */

$(document).ready(function () {

    // 1. DataTable (définie dans actions.js)
    initOrganisationsTable();

    // 2. Modules métier
    initCreateOrganisationModal();
    initEditOrganisationModal();
    initShowOrganisationModal();
    initOrganisationActions();

    // 3. Filtres & Select2
    if (typeof initOrganisationFilters       === 'function') initOrganisationFilters();
    if (typeof initOrganisationSelect2Helpers === 'function') initOrganisationSelect2Helpers();

    // 4. Tooltips Bootstrap après chaque requête AJAX
    $(document).on('ajaxComplete', function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
});
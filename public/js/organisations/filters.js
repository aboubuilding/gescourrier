/**
 * organisations/filters.js
 * Filtres sur le tableau organisations — compatible DataTable
 */

function initOrganisationFilters() {

    // Si DataTable est actif, utiliser son API de recherche
    function applyFilters() {
        const search = ($('#searchOrganisation').val() || $('#tableSearch').val() || '').toLowerCase().trim();
        const type   = $('#filterType').val()   || '';
        const status = $('#filterEtat').val()   || '';

        if (window.organisationTable) {
            // Recherche globale DataTable
            window.organisationTable.search(search);

            // Filtres colonne via $.fn.dataTable.ext.search (si non déjà ajouté)
            window.organisationTable.draw();
        } else {
            // Fallback : filtrage jQuery sur les lignes
            $('#organisationsTable tbody tr').each(function () {
                const $row       = $(this);
                const rowSearch  = ($row.data('search') || $row.text()).toLowerCase();
                const rowType    = String($row.data('type')   ?? '');
                const rowStatus  = String($row.data('status') ?? '');

                const matchSearch = !search || rowSearch.includes(search);
                const matchType   = !type   || rowType === type;
                const matchStatus = !status || rowStatus === status;

                $row.toggle(matchSearch && matchType && matchStatus);
            });
            updateOrganisationCount();
        }
    }

    // Écouter les champs de filtre
    $('#searchOrganisation, #tableSearch, #filterType, #filterEtat').off('input change').on('input change', applyFilters);

    // Bouton reset
    $('#btnResetFilters').off('click').on('click', function () {
        $('#searchOrganisation').val('');
        $('#tableSearch').val('');
        $('#filterType').val('');
        $('#filterEtat').val('');
        if (window.organisationTable) {
            window.organisationTable.search('').draw();
        } else {
            applyFilters();
        }
    });

    // Appliquer au chargement
    applyFilters();
}
/**
 * services/filters.js
 * Filtres sur le tableau services — compatible DataTable
 */

function initServiceFilters() {

    function applyFilters() {

        const search = (
            $('#searchService').val() ||
            $('#tableSearch').val() ||
            ''
        ).toLowerCase().trim();

        const organisation = $('#filterOrganisation').val() || '';
        const etat = $('#filterEtat').val() || ''; // si tu l’utilises côté UI (optionnel)

        // ─────────────────────────────
        // MODE DATATABLE
        // ─────────────────────────────

        if (window.servicesTable) {

            window.servicesTable.search(search).draw();

            // Filtrage avancé (organisation / état si colonnes présentes)
            $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(fn => !fn._serviceFilter);

            const filterFn = function (settings, data, dataIndex) {

                const row = window.servicesTable.row(dataIndex).node();
                if (!row) return true;

                const $row = $(row);

                const rowOrg = String($row.data('organisation') ?? '');
                const rowEtat = String($row.data('etat') ?? '');

                const matchOrg = !organisation || rowOrg === organisation;
                const matchEtat = !etat || rowEtat === etat;

                return matchOrg && matchEtat;
            };

            filterFn._serviceFilter = true;
            $.fn.dataTable.ext.search.push(filterFn);

            window.servicesTable.draw();

        } else {

            // ─────────────────────────────
            // MODE FALLBACK (jQuery)
            // ─────────────────────────────

            $('#servicesTable tbody tr').each(function () {

                const $row = $(this);

                const rowSearch = ($row.data('search') || $row.text()).toLowerCase();
                const rowOrg    = String($row.data('organisation') ?? '');
                const rowEtat   = String($row.data('etat') ?? '');

                const matchSearch = !search || rowSearch.includes(search);
                const matchOrg    = !organisation || rowOrg === organisation;
                const matchEtat   = !etat || rowEtat === etat;

                $row.toggle(matchSearch && matchOrg && matchEtat);
            });
        }

        updateServiceCount?.();
    }

    // ─────────────────────────────────────
    // EVENTS
    // ─────────────────────────────────────

    $(document)
        .off('input change', '#searchService, #tableSearch, #filterOrganisation, #filterEtat')
        .on('input change', '#searchService, #tableSearch, #filterOrganisation, #filterEtat', applyFilters);

    // ─────────────────────────────────────
    // RESET
    // ─────────────────────────────────────

    $('#btnResetFilters')
        .off('click')
        .on('click', function () {

            $('#searchService').val('');
            $('#tableSearch').val('');
            $('#filterOrganisation').val('');
            $('#filterEtat').val('');

            if (window.servicesTable) {
                window.servicesTable.search('').draw();
            } else {
                applyFilters();
            }
        });

    // ─────────────────────────────────────
    // INIT
    // ─────────────────────────────────────

    applyFilters();
}
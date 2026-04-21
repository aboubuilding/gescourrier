/**
 * Gestion des Filtres DataTables
 */
function initFilters() {
    const table = window.courrierTable; // Récupère l'instance globale
    if (!table) return;

    function applyFilters() {
        const search = $('#searchInput').val().toLowerCase();
        const type = $('.type-pill.active').data('type');
        const statut = $('#filterStatut').val();
        const priorite = $('#filterPriorite').val();
        
        $.fn.dataTable.ext.search = [];
        
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            const row = $(table.row(dataIndex).node());
            const matchType = !type || String(row.data('type')) === String(type);
            const matchStatut = !statut || String(row.data('statut')) === String(statut);
            const matchPriorite = !priorite || String(row.data('priorite')) === String(priorite);
            return matchType && matchStatut && matchPriorite;
        });
        
        table.search(search).draw();
        updateCount();
    }
    
    function updateCount() {
        const count = table.rows({ search: 'applied' }).count();
        $('#tableCount').text(`${count} courrier${count !== 1 ? 's' : ''}`);
    }

    // Event Listeners
    $('#searchInput').off('keyup').on('keyup', applyFilters);
    
    $('.type-pill').off('click').on('click', function() {
        $('.type-pill').removeClass('active');
        $(this).addClass('active');
        applyFilters();
    });
    
    $('#filterStatut, #filterPriorite').off('change').on('change', applyFilters);
    
    $('#btnResetFilters').off('click').on('click', function() {
        $('#searchInput').val('');
        $('.type-pill').removeClass('active').first().addClass('active');
        $('#filterStatut, #filterPriorite').val('');
        $.fn.dataTable.ext.search = [];
        table.search('').columns().search('').draw();
        updateCount();
    });
}
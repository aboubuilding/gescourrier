/**
 * INDEX - Organisations
 * Point d'entrée principal
 */

$(document).ready(function() {
    // 1. Initialiser la DataTable
    initDataTable();

    // 2. Initialiser les modules
    initCreateModal();
    initEditModal();
    initActions();

    // 3. Autres logiques globales
    initFilters();
    initSelect2Helpers();
});

// ============================================
// INITIALISATION DE LA DATATABLE
// ============================================

function initDataTable() {
    if ($.fn.DataTable.isDataTable('#organisationsTable')) {
        $('#organisationsTable').DataTable().destroy();
    }
    
    window.dataTable = $('#organisationsTable').DataTable({
        responsive: true,
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json' },
        pageLength: 15,
        lengthMenu: [[15, 30, 50, -1], [15, 30, 50, 'Tous']],
        order: [[0, 'asc']],
        columnDefs: [{ orderable: false, targets: -1 }],
        dom: '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
        drawCallback: function() { updateTableCount(); }
    });
}

// ============================================
// INITIALISATION DES FILTRES
// ============================================

function initFilters() {
    $('#searchInput').on('keyup', debounce(applyFilters, 300));
    $('#filterType, #filterEtat').on('change', applyFilters);
    
    $('.type-pill').on('click', function() {
        $('.type-pill').removeClass('active');
        $(this).addClass('active');
        applyFilters();
    });
    
    $('#btnResetFilters').on('click', resetFilters);
}

function applyFilters() {
    const search = $('#searchInput').val().toLowerCase();
    const type = $('.type-pill.active').data('type') || $('#filterType').val();
    const etat = $('#filterEtat').val();
    
    $.fn.dataTable.ext.search = [];
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        const row = $(window.dataTable.row(dataIndex).node());
        const matchType = !type || String(row.data('type')) === String(type);
        const matchEtat = !etat || String(row.data('etat')) === String(etat);
        return matchType && matchEtat;
    });
    
    window.dataTable.search(search).draw();
    updateTableCount();
}

function resetFilters() {
    $('#searchInput, #filterType, #filterEtat').val('');
    $('.type-pill').removeClass('active').first().addClass('active');
    $.fn.dataTable.ext.search = [];
    window.dataTable.search('').draw();
    updateTableCount();
}

function updateTableCount() {
    const count = window.dataTable.rows({ search: 'applied' }).count();
    $('#tableCount').text(`${count} organisation${count !== 1 ? 's' : ''}`);
}

// ============================================
// INITIALISATION DES ACTIONS
// ============================================

function initActions() {
    // Dropdown actions
    $(document).on('click', '.action-trigger', function(e) {
        e.stopPropagation();
        $('.action-dropdown').removeClass('open');
        $(this).closest('.action-dropdown').addClass('open');
    });
    
    // Fermer dropdown en cliquant ailleurs
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.action-dropdown').length) {
            $('.action-dropdown').removeClass('open');
        }
    });
}

// ============================================
// INITIALISATION DES SELECT2
// ============================================

function initSelect2Helpers() {
    $('.select2').each(function() {
        if (!$(this).hasClass('select2-hidden-accessible')) {
            $(this).select2({
                width: '100%',
                dropdownParent: $(this).closest('.modal').length ? $(this).closest('.modal') : $(document.body)
            });
        }
    });
}

// ============================================
// FONCTIONS UTILITAIRES
// ============================================

function debounce(func, wait) {
    let timeout;
    return function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, arguments), wait);
    };
}

function reloadDataTable() {
    if (window.dataTable) {
        window.dataTable.ajax.reload();
        updateTableCount();
    } else {
        location.reload();
    }
}

// Exposer les fonctions globalement
window.organisations = {
    reloadDataTable: reloadDataTable,
    applyFilters: applyFilters,
    resetFilters: resetFilters
};
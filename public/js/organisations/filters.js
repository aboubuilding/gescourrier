/**
 * FILTRES - Organisations
 */

let dataTable = null;

function initFilters() {
    // Initialiser la DataTable
    dataTable = initDataTable();
    
    // Mettre à jour le compteur
    updateCount();
    
    // Filtre de recherche
    $('#searchInput').off('keyup').on('keyup', debounce(function() {
        applyFilters();
    }, 300));
    
    // Filtre par type (select)
    $('#filterType').off('change').on('change', function() {
        applyFilters();
    });
    
    // Filtre par état
    $('#filterEtat').off('change').on('change', function() {
        applyFilters();
    });
    
    // Filtre par pills
    $('.type-pill').off('click').on('click', function() {
        $('.type-pill').removeClass('active');
        $(this).addClass('active');
        applyFilters();
    });
    
    // Bouton reset
    $('#btnResetFilters').off('click').on('click', function() {
        resetFilters();
    });
}

function applyFilters() {
    const search = $('#searchInput').val().toLowerCase();
    const type = $('.type-pill.active').data('type');
    const filterType = $('#filterType').val();
    const etat = $('#filterEtat').val();
    
    // Nettoyer les filtres existants
    $.fn.dataTable.ext.search = [];
    
    // Ajouter le nouveau filtre
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        const row = $(dataTable.row(dataIndex).node());
        const rowType = String(row.data('type'));
        const rowEtat = String(row.data('etat'));
        
        // Filtre type (priorité au pill)
        let matchType = true;
        if (type && type !== '') {
            matchType = rowType === String(type);
        } else if (filterType && filterType !== '') {
            matchType = rowType === String(filterType);
        }
        
        // Filtre état
        let matchEtat = true;
        if (etat && etat !== '') {
            matchEtat = rowEtat === String(etat);
        }
        
        return matchType && matchEtat;
    });
    
    // Appliquer la recherche
    dataTable.search(search).draw();
    updateCount();
}

function resetFilters() {
    // Réinitialiser les champs
    $('#searchInput').val('');
    $('#filterType').val('');
    $('#filterEtat').val('');
    
    // Réinitialiser les pills
    $('.type-pill').removeClass('active');
    $('.type-pill').first().addClass('active');
    
    // Nettoyer les filtres
    $.fn.dataTable.ext.search = [];
    
    // Redessiner le tableau
    dataTable.search('').draw();
    updateCount();
}

function updateCount() {
    const count = dataTable.rows({ search: 'applied' }).count();
    const text = `${count} organisation${count !== 1 ? 's' : ''}`;
    $('#tableCount').text(text);
}

// Debounce pour optimiser la recherche
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
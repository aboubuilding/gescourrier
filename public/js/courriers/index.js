/**
 * Point d'entrée principal
 */
$(document).ready(function() {
    
    // 1. Initialiser la DataTable
    initDataTable();

    // 2. Initialiser les modules
    initCreateModal();
    initEditModal();
    initShowModal();
    initActions();

    // 3. Autres logiques globales (Filtres, Select2, etc.)
    initFilters(); 
    initSelect2Helpers();
});
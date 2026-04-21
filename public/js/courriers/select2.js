/**
 * Helpers pour Select2 dans les Modals
 * Gère l'initialisation et le nettoyage pour éviter les conflits d'affichage
 */

function initSelect2Helpers() {
    
    // Configuration commune pour tous les Select2
    const select2Config = {
        width: '100%',
        language: 'fr',
        placeholder: 'Sélectionner...',
        allowClear: true,
        theme: 'default' // Ou 'bootstrap-5' si tu utilises ce thème
    };

    /**
     * Initialise Select2 pour un modal spécifique
     * @param {string} modalId - L'ID du modal (ex: '#modalCreate')
     * @param {string} selector - Le sélecteur des champs (ex: '.select2-organisation')
     */
    function initSelect2InModal(modalId, selector) {
        $(selector).each(function() {
            const $el = $(this);
            
            // Détruire l'instance existante si elle existe pour éviter les doublons
            if ($el.hasClass('select2-hidden-accessible')) {
                $el.select2('destroy');
            }

            // Initialiser avec le dropdownParent pointant vers le modal
            $el.select2({
                ...select2Config,
                dropdownParent: $(modalId)
            });
        });
    }

    // --- Initialisation au chargement de la page (si des modals sont déjà présents) ---
    // Note: Souvent, on préfère initialiser à l'ouverture du modal (voir plus bas)
    
    // --- Écouteurs d'événements Bootstrap pour initialiser/détruire proprement ---
    
    const modals = ['#modalCreate', '#modalEdit', '#modalAffecter'];
    
    modals.forEach(modalId => {
        
        // Quand le modal s'ouvre -> On initialise Select2
        $(modalId).on('shown.bs.modal', function () {
            
            // Déterminer quels champs initialiser selon le modal
            if (modalId === '#modalCreate' || modalId === '#modalEdit') {
                initSelect2InModal(modalId, '.select2-organisation');
                // Ajoute d'autres select2 ici si besoin (ex: .select2-service)
            } 
            else if (modalId === '#modalAffecter') {
                initSelect2InModal(modalId, '.select2-agent');
                initSelect2InModal(modalId, '.select2-service');
            }
        });

        // Quand le modal se ferme -> On détruit Select2 pour libérer la mémoire et éviter les bugs
        $(modalId).on('hidden.bs.modal', function () {
            $(this).find('select.select2').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }
            });
            
            // Optionnel : Reset du formulaire à la fermeture
            // $(this).find('form')[0]?.reset();
        });
    });
}
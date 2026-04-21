/**
 * CRÉATION D'ORGANISATION
 * Gère le modal de création et l'envoi du formulaire
 */

function initCreateModal() {
    // Réinitialiser le formulaire quand le modal s'ouvre
    $('#modalCreate').on('show.bs.modal', function() {
        resetCreateForm();
    });
    
    // Nettoyer quand le modal se ferme
    $('#modalCreate').on('hidden.bs.modal', function() {
        resetCreateForm();
    });
    
    // Soumission du formulaire
    $('#formCreate').off('submit').on('submit', function(e) {
        e.preventDefault();
        submitCreateForm($(this));
    });
}

/**
 * Réinitialiser le formulaire de création
 */
function resetCreateForm() {
    // Réinitialiser les champs
    $('#formCreate')[0].reset();
    
    // Effacer les erreurs de validation
    clearValidationErrors();
    
    // Réinitialiser l'état du bouton
    const $btn = $('#btnSubmitCreate');
    const $spinner = $('#spinnerCreate');
    $btn.prop('disabled', false);
    $spinner.addClass('d-none');
    
    // Retirer les classes d'erreur des champs
    $('#formCreate .is-invalid').removeClass('is-invalid');
    $('#formCreate .invalid-feedback').remove();
}

/**
 * Soumettre le formulaire de création
 * @param {jQuery} $form - Le formulaire à soumettre
 */
function submitCreateForm($form) {
    const $btn = $('#btnSubmitCreate');
    const $spinner = $('#spinnerCreate');
    
    // Effacer les erreurs précédentes
    clearValidationErrors();
    
    // Désactiver le bouton et afficher le spinner
    toggleButtonLoading($btn, $spinner, true);
    
    // Récupérer les données du formulaire
    const formData = $form.serialize();
    const actionUrl = $form.attr('action');
    
    // Envoyer la requête AJAX
    $.ajax({
        url: actionUrl,
        method: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': getCsrfToken()
        },
        success: function(response) {
            handleCreateSuccess(response);
        },
        error: function(xhr) {
            handleCreateError(xhr);
        },
        complete: function() {
            // Réactiver le bouton et cacher le spinner
            toggleButtonLoading($btn, $spinner, false);
        }
    });
}

/**
 * Gérer la réponse de succès
 * @param {Object} response - La réponse du serveur
 */
function handleCreateSuccess(response) {
    // Afficher le message de succès
    const message = response.message || 'Organisation créée avec succès';
    showToast('success', message);
    
    // Fermer le modal
    $('#modalCreate').modal('hide');
    
    // Recharger la page après un court délai
    setTimeout(() => {
        location.reload();
    }, 800);
}

/**
 * Gérer les erreurs de création
 * @param {Object} xhr - L'objet XMLHttpRequest
 */
function handleCreateError(xhr) {
    // Gestion des erreurs de validation (422)
    if (xhr.status === 422 && xhr.responseJSON?.errors) {
        // Afficher les erreurs sous les champs concernés
        showValidationErrors(xhr.responseJSON.errors);
        
        // Afficher un message d'erreur global
        showToast('error', 'Veuillez corriger les erreurs dans le formulaire');
        
        // Scroll vers la première erreur
        const firstError = $('.is-invalid').first();
        if (firstError.length) {
            $('html, body').animate({
                scrollTop: firstError.offset().top - 100
            }, 500);
        }
    } 
    // Gestion des erreur de serveur (500)
    else if (xhr.status === 500) {
        const message = xhr.responseJSON?.message || 'Erreur serveur lors de la création';
        showToast('error', message);
        console.error('Erreur serveur:', xhr.responseJSON);
    }
    // Gestion des autres erreurs
    else {
        const message = xhr.responseJSON?.message || 'Erreur lors de la création de l\'organisation';
        showToast('error', message);
    }
}

/**
 * Récupérer le token CSRF
 * @returns {string}
 */
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

/**
 * Afficher une notification toast
 * @param {string} type - Type de notification (success, error, warning, info)
 * @param {string} message - Message à afficher
 */
function showToast(type, message) {
    if (typeof toastr !== 'undefined') {
        toastr[type](message);
    } else {
        alert(message);
    }
}

/**
 * Afficher les erreurs de validation sous les champs
 * @param {Object} errors - Objet contenant les erreurs
 */
function showValidationErrors(errors) {
    // Supprimer les anciennes erreurs
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    
    // Parcourir chaque champ en erreur
    Object.keys(errors).forEach(field => {
        const $field = $(`[name="${field}"]`);
        if ($field.length) {
            // Ajouter la classe d'erreur
            $field.addClass('is-invalid');
            
            // Récupérer le premier message d'erreur
            const message = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
            
            // Créer l'élément d'erreur
            const $error = $(`<div class="invalid-feedback">${message}</div>`);
            
            // Insérer après le champ
            $field.after($error);
            
            // Pour les selects avec Select2
            if ($field.hasClass('select2-hidden-accessible')) {
                $field.next('.select2-container').find('.select2-selection').addClass('is-invalid');
            }
        }
    });
}

/**
 * Effacer toutes les erreurs de validation
 */
function clearValidationErrors() {
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    
    // Pour Select2
    $('.select2-container .select2-selection').removeClass('is-invalid');
}

/**
 * Gérer l'état de chargement d'un bouton
 * @param {jQuery} $btn - Le bouton
 * @param {jQuery} $spinner - L'élément spinner
 * @param {boolean} isLoading - État de chargement
 */
function toggleButtonLoading($btn, $spinner, isLoading) {
    if (isLoading) {
        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');
        $btn.data('original-text', $btn.html());
        $btn.html('<span class="spinner-border spinner-border-sm me-1"></span> Création en cours...');
    } else {
        $btn.prop('disabled', false);
        $spinner.addClass('d-none');
        if ($btn.data('original-text')) {
            $btn.html($btn.data('original-text'));
        }
    }
}

// Exposer les fonctions globalement pour une utilisation dans d'autres scripts
window.organisationsCreate = {
    resetForm: resetCreateForm,
    submitForm: submitCreateForm
};
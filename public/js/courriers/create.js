/**
 * Gestion du Modal Création
 */
function initCreateModal() {
    
    // Upload Drag & Drop
    const $fileDrop = $('#fileDrop');
    const $fileInput = $('#fileInput');
    const $fileName = $('#fileName');

    if ($fileDrop.length) {
        $fileDrop.on('click', () => $fileInput.click());
        
        $fileInput.on('change', function() {
            if (this.files?.[0]) {
                $fileName.text(this.files[0].name);
                $fileDrop.addClass('dragover');
            }
        });

        // Gestion complète du Drag & Drop
        $fileDrop.on('dragover dragenter', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).addClass('dragover');
        });
        
        $fileDrop.on('dragleave dragend drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('dragover');
        });
        
        $fileDrop.on('drop', function(e) {
            const files = e.originalEvent?.dataTransfer?.files;
            if (files?.[0] && $fileInput[0]) {
                $fileInput[0].files = files;
                $fileName.text(files[0].name);
                $fileInput.trigger('change');
            }
        });
    }

    // Fonction pour effacer les erreurs d'un champ
    function clearFieldError($field) {
        const $errorDiv = $field.next('.field-error');
        if ($errorDiv.length) {
            $errorDiv.remove();
        }
        $field.removeClass('is-invalid');
    }

    // Fonction pour afficher une erreur sous un champ
    function showFieldError($field, message) {
        // Supprimer l'erreur existante
        clearFieldError($field);
        
        // Ajouter la classe d'erreur
        $field.addClass('is-invalid');
        
        // Créer le message d'erreur
        const $error = $(`<div class="field-error text-danger small mt-1">${message}</div>`);
        $field.after($error);
    }

    // Fonction pour effacer toutes les erreurs du formulaire
    function clearAllErrors() {
        $('#formCreate .is-invalid').removeClass('is-invalid');
        $('#formCreate .field-error').remove();
    }

    // Écouteur pour effacer l'erreur quand l'utilisateur modifie un champ
    $('#formCreate input, #formCreate select').on('input change', function() {
        clearFieldError($(this));
    });

    // Submit Form Create
    $('#formCreate').off('submit').on('submit', function(e) {
        e.preventDefault();
        
        // Effacer toutes les erreurs précédentes
        clearAllErrors();
        
        const $form = $(this);
        const $btn = $('#btnSubmitCreate');
        const $spinner = $('#spinnerCreate');
        
        window.toggleButtonLoading($btn, $spinner, true);
        
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: new FormData($form[0]),
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN },
            success: function(res) {
                if (res.success) {
                    window.showNotification(res.message || 'Courrier créé avec succès', 'success');
                    $('#modalCreate').modal('hide');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    // Gestion des erreurs backend qui ne sont pas des erreurs de validation
                    if (res.message) {
                        window.showNotification(res.message, 'error');
                    }
                    if (res.error) {
                        // Afficher l'erreur backend dans une notification
                        window.showNotification('Erreur serveur: ' + res.error, 'error');
                    }
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                
                // Gestion des erreurs de validation (422)
                if (xhr.status === 422 && response?.errors) {
                    // Parcourir chaque champ avec erreur
                    Object.keys(response.errors).forEach(fieldName => {
                        const message = response.errors[fieldName][0];
                        let $field = null;
                        
                        // Trouver le champ correspondant dans le formulaire
                        switch(fieldName) {
                            case 'type':
                                $field = $('#createType');
                                break;
                            case 'priorite':
                                $field = $('select[name="priorite"]');
                                break;
                            case 'reference':
                                $field = $('input[name="reference"]');
                                break;
                            case 'numero':
                                $field = $('input[name="numero"]');
                                break;
                            case 'organisation_id':
                                $field = $('#createOrganisation');
                                break;
                            case 'objet':
                                $field = $('input[name="objet"]');
                                break;
                            case 'date_reception':
                                $field = $('input[name="date_reception"]');
                                break;
                            case 'date_envoi':
                                $field = $('input[name="date_envoi"]');
                                break;
                            case 'fichier':
                                $field = $('#fileInputModern');
                                // Pour le fichier, afficher l'erreur dans la zone de drop
                                const $fileZone = $('#fileUploadZone');
                                showFieldError($fileZone, message);
                                break;
                            default:
                                $field = $(`[name="${fieldName}"]`);
                        }
                        
                        if ($field && $field.length) {
                            showFieldError($field, message);
                        } else {
                            // Si le champ n'est pas trouvé, afficher dans une notification
                            window.showNotification(message, 'error');
                        }
                    });
                } 
                // Gestion des erreurs serveur (500)
                else if (xhr.status === 500) {
                    const errorMsg = response?.message || response?.error || 'Erreur interne du serveur';
                    window.showNotification(errorMsg, 'error');
                }
                // Gestion des autres erreurs
                else {
                    window.showNotification(
                        response?.message || 'Erreur lors de la création du courrier', 
                        'error'
                    );
                }
            },
            complete: function() {
                window.toggleButtonLoading($btn, $spinner, false);
            }
        });
    });
}

// Fonctions pour la gestion du fichier
function handleFilePreview(input) {
    if (input.files && input.files[0]) {
        document.getElementById('fileNameModern').textContent = input.files[0].name;
        document.getElementById('fileUploadZone').style.display = 'none';
        document.getElementById('fileInfoModern').classList.remove('d-none');
        document.getElementById('fileInfoModern').style.display = 'flex';
        
        // Effacer l'erreur du fichier si elle existe
        const $fileZone = $('#fileUploadZone');
        clearFieldError($fileZone);
    }
}

function clearFilePreview() {
    document.getElementById('fileInputModern').value = '';
    document.getElementById('fileUploadZone').style.display = 'block';
    document.getElementById('fileInfoModern').classList.add('d-none');
    
    // Effacer l'erreur du fichier
    const $fileZone = $('#fileUploadZone');
    clearFieldError($fileZone);
}

// Fonction utilitaire pour effacer les erreurs d'un champ
function clearFieldError($field) {
    const $errorDiv = $field.next('.field-error');
    if ($errorDiv.length) {
        $errorDiv.remove();
    }
    $field.removeClass('is-invalid');
}

function showFieldError($field, message) {
    clearFieldError($field);
    $field.addClass('is-invalid');
    const $error = $(`<div class="field-error text-danger small mt-1">${message}</div>`);
    $field.after($error);
}
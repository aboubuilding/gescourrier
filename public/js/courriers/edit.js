/**
 * Gestion du Modal Modification (Edit) - Avec Erreurs Inline
 */

function initEditModal() {

    // --- FONCTIONS UTILITAIRES ---

    // Nettoyer les erreurs visuelles précédentes
    function clearInlineErrors() {
        $('#formEdit .is-invalid').removeClass('is-invalid');
        $('#formEdit .invalid-feedback').remove();
        $('#formEdit .field-error').remove();
        
        // Nettoyer aussi les erreurs sur les Select2
        $('#formEdit .select2-container--default .select2-selection--single').removeClass('is-invalid');
        $('#formEdit .select2-container--default .select2-selection--single').css('border-color', '');
    }

    // Afficher les erreurs Laravel sous les champs correspondants
    function showInlineErrors(errors) {
        clearInlineErrors();
        
        $.each(errors, function(field, messages) {
            const msg = Array.isArray(messages) ? messages[0] : messages;
            
            // Cibler le champ par son attribut name
            let $field = $(`[name="${field}"]`);
            
            // Cas spécial pour certains champs
            if (field === 'organisation_id') {
                $field = $('#editOrganisation');
            } else if (field === 'type') {
                $field = $('#editType');
            } else if (field === 'priorite') {
                $field = $('#editPriorite');
            } else if (field === 'fichier') {
                $field = $('.file-upload-modern');
            }
            
            if ($field && $field.length) {
                $field.addClass('is-invalid');
                
                // Pour Select2
                if ($field.hasClass('select2-hidden-accessible')) {
                    $field.next('.select2-container').find('.select2-selection').addClass('is-invalid');
                }
                
                // Insérer le message d'erreur
                const $errorDiv = $(`<div class="invalid-feedback field-error">${msg}</div>`);
                $field.after($errorDiv);
            } else {
                // Si le champ n'est pas trouvé, afficher dans une notification
                window.showNotification(msg, 'error');
            }
        });
        
        // Scroll vers la première erreur
        const firstError = $('.is-invalid').first();
        if (firstError.length) {
            $('html, body').animate({
                scrollTop: firstError.offset().top - 100
            }, 500);
        }
    }

    // Gérer l'affichage des champs Date selon le Type
    function toggleEditDateFields(type) {
        $('.edit-date-reception-field, .edit-date-envoi-field').hide();
        
        if (String(type) === '0') { // Entrant
            $('.edit-date-reception-field').fadeIn(200);
        } else if (String(type) === '1') { // Sortant
            $('.edit-date-envoi-field').fadeIn(200);
        } else if (String(type) === '2') { // Interne
            $('.edit-date-reception-field').fadeIn(200);
            $('.edit-date-envoi-field').fadeIn(200);
        }
    }

    // --- ÉVÉNEMENTS ---

    // 1. Clic sur le bouton "Modifier" dans la liste
    $(document).off('click', '.btn-edit').on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        console.log('ID récupéré du bouton:', id); // Débogage
        
        if (!id) {
            window.showNotification('ID du courrier non trouvé', 'error');
            return;
        }
        
        const $btn = $(this);
        const originalHtml = $btn.html();

        // Reset UI
        clearInlineErrors();
        $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

        // Appel AJAX pour récupérer les données
        $.get(`/courriers/${id}/edit`, function(data) {
            console.log('Données reçues pour édition:', data); // Débogage
            
            // Vérifier que l'ID est présent
            if (!data.id) {
                window.showNotification('Données du courrier invalides', 'error');
                return;
            }
            
            // Remplissage des champs
            $('#editId').val(data.id);
            console.log('ID mis dans le champ caché:', $('#editId').val()); // Débogage
            
            $('#editReference').val(data.reference || '');
            $('#editNumero').val(data.numero || '');
            $('#editObjet').val(data.objet || '');
            
            // Remplissage des Selects
            $('#editType').val(data.type).trigger('change');
            $('#editPriorite').val(data.priorite).trigger('change');
            
            // Remplissage des Dates
            $('#editDateReception').val(data.date_reception || '');
            $('#editDateEnvoi').val(data.date_envoi || '');
            
            // Remplissage Select2 Organisation
            if (data.organisation_id) {
                $('#editOrganisation').val(data.organisation_id).trigger('change');
            } else {
                $('#editOrganisation').val(null).trigger('change');
            }

            // Service
            $('#editService').val(data.service_id || '');

            // Affichage Fichier Actuel
            if (data.fichier_nom_original) {
                $('#editFileCurrent')
                    .html(`<i class="fas fa-file-pdf text-danger me-2"></i> ${data.fichier_nom_original}`)
                    .show();
            } else {
                $('#editFileCurrent')
                    .html('<i class="fas fa-ban text-muted me-2"></i> Aucun fichier joint')
                    .show();
            }

            // Mise à jour de l'affichage des dates
            toggleEditDateFields(data.type);

            // Ouvrir le modal
            $('#modalEdit').modal('show');

        }).fail(function(xhr) {
            console.error('Erreur chargement:', xhr);
            window.showNotification('Impossible de charger les données', 'error');
        }).always(function() {
            $btn.html(originalHtml).prop('disabled', false);
        });
    });

    // Écouteur pour changer l'affichage des dates
    $('#editType').off('change').on('change', function() {
        toggleEditDateFields($(this).val());
    });

    // 2. Soumission du Formulaire de Modification (AJAX)
    $('#formEdit').off('submit').on('submit', function(e) {
        e.preventDefault();
        
        const $btn = $('#btnSubmitEdit');
        const $spinner = $('#spinnerEdit');
        
        // Nettoyer les erreurs
        clearInlineErrors();
        
        // Récupérer l'ID
        const courrierId = $('#editId').val();
        console.log('ID avant soumission:', courrierId); // Débogage
        
        if (!courrierId || courrierId === '') {
            window.showNotification('ID du courrier manquant. Veuillez réessayer.', 'error');
            window.toggleButtonLoading($btn, $spinner, false);
            return;
        }
        
        window.toggleButtonLoading($btn, $spinner, true);
        
        // Construction du FormData
        const formData = new FormData();
        
        // Ajouter le token CSRF
        formData.append('_token', window.CSRF_TOKEN);
        formData.append('_method', 'POST'); // Important pour Laravel
        
        // Récupération explicite des valeurs
        formData.append('type', $('#editType').val());
        formData.append('priorite', $('#editPriorite').val());
        formData.append('reference', $('#editReference').val());
        formData.append('numero', $('#editNumero').val());
        formData.append('objet', $('#editObjet').val());
        formData.append('organisation_id', $('#editOrganisation').val());
        formData.append('service_id', $('#editService').val() || '');
        formData.append('date_reception', $('#editDateReception').val() || '');
        formData.append('date_envoi', $('#editDateEnvoi').val() || '');
        
        // Gestion du fichier (seulement si un nouveau est sélectionné)
        const fileInput = document.getElementById('editFileInput');
        if (fileInput && fileInput.files.length > 0) {
            formData.append('fichier', fileInput.files[0]);
        }

        // Conservation de votre route personnalisée
        const updateUrl = `/courriers/update/${courrierId}`;
        console.log('URL d\'update:', updateUrl); // Débogage
        
        $.ajax({
            url: updateUrl,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                console.log('Succès:', res); // Débogage
                if (res.success) {
                    window.showNotification(res.message || 'Courrier mis à jour avec succès', 'success');
                    $('#modalEdit').modal('hide');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    window.showNotification(res.message || 'Erreur lors de la mise à jour', 'error');
                }
            },
            error: function(xhr) {
                console.error('Erreur AJAX:', xhr); // Débogage
                console.error('Status:', xhr.status);
                console.error('Response:', xhr.responseJSON);
                
                if (xhr.status === 422) {
                    // Erreur de Validation : Affichage Inline
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        showInlineErrors(xhr.responseJSON.errors);
                    } else {
                        window.showNotification('Erreurs de validation', 'error');
                    }
                } else if (xhr.status === 404) {
                    window.showNotification('Courrier non trouvé. Vérifiez l\'ID.', 'error');
                } else if (xhr.status === 500) {
                    const errorMsg = xhr.responseJSON?.message || 'Erreur serveur lors de la modification';
                    window.showNotification(errorMsg, 'error');
                } else {
                    const errorMsg = xhr.responseJSON?.message || 'Erreur lors de la modification';
                    window.showNotification(errorMsg, 'error');
                }
            },
            complete: function() {
                window.toggleButtonLoading($btn, $spinner, false);
            }
        });
    });
}
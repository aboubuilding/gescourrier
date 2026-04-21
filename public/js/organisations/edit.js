/**
 * MODIFICATION D'ORGANISATION
 */

function initEditModal() {
    // Écouteur sur les boutons d'édition
    $(document).off('click', '.btn-edit').on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        const $btn = $(this);
        const originalHtml = $btn.html();
        
        clearValidationErrors();
        $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
        
        // Charger les données
        $.get(`${window.organisationsConfig.routes.edit}${id}/edit`, function(data) {
            // Remplir le formulaire
            $('#editId').val(data.id);
            $('#editNom').val(data.nom);
            $('#editSigle').val(data.sigle || '');
            $('#editType').val(data.type);
            $('#editEmail').val(data.email || '');
            $('#editTelephone').val(data.telephone || '');
            $('#editAdresse').val(data.adresse || '');
            
            // Mettre à jour l'action du formulaire
            $('#formEdit').attr('action', `${window.organisationsConfig.routes.update}${data.id}`);
            
            // Ouvrir le modal
            $('#modalEdit').modal('show');
        }).fail(function(xhr) {
            toastr.error('Impossible de charger les données de l\'organisation');
            console.error(xhr);
        }).always(function() {
            $btn.html(originalHtml).prop('disabled', false);
        });
    });
    
    // Soumission du formulaire d'édition
    $('#formEdit').off('submit').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $btn = $('#btnSubmitEdit');
        const $spinner = $('#spinnerEdit');
        const formData = $form.serialize();
        const actionUrl = $form.attr('action');
        
        clearValidationErrors();
        toggleButtonLoading($btn, $spinner, true);
        
        $.ajax({
            url: actionUrl,
            method: 'POST',
            data: formData + '&_method=PUT',
            headers: {
                'X-CSRF-TOKEN': window.organisationsConfig.csrfToken
            },
            success: function(response) {
                toastr.success(response.message || 'Organisation modifiée avec succès');
                $('#modalEdit').modal('hide');
                setTimeout(() => location.reload(), 800);
            },
            error: function(xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    showValidationErrors(xhr.responseJSON.errors);
                    toastr.error('Veuillez corriger les erreurs dans le formulaire');
                } else {
                    const message = xhr.responseJSON?.message || 'Erreur lors de la modification';
                    toastr.error(message);
                }
            },
            complete: function() {
                toggleButtonLoading($btn, $spinner, false);
            }
        });
    });
    
    // Nettoyer le formulaire quand le modal se ferme
    $('#modalEdit').on('hidden.bs.modal', function() {
        $('#formEdit')[0].reset();
        clearValidationErrors();
    });
}
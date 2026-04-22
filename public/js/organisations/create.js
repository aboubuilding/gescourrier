/**
 * organisations/create.js
 * Gestion du Modal Création Organisation
 * IDs corrigés pour correspondre au Blade : orgNom, orgSigle, orgType, etc.
 */

function initCreateOrganisationModal() {

    // ─────────────────────────────────────
    // UTILITAIRES
    // ─────────────────────────────────────

    function clearFieldError($field) {
        $field.removeClass('is-invalid');
        $field.next('.field-error').remove();
        const $wrapper = $field.closest('.select-wrapper');
        if ($wrapper.length) {
            $wrapper.find('.select-icon').css('color', '');
            $wrapper.next('.field-error').remove();
        }
    }

    function showFieldError($field, message) {
        clearFieldError($field);
        $field.addClass('is-invalid');
        const $wrapper = $field.closest('.select-wrapper');
        const $error = $(`<div class="field-error text-danger small mt-1">⚠ ${message}</div>`);
        if ($wrapper.length) {
            $wrapper.find('.select-icon').css('color', '#dc3545');
            $wrapper.after($error);
        } else {
            $field.after($error);
        }
    }

    function clearAllErrors() {
        $('#formCreateOrganisation .is-invalid').removeClass('is-invalid');
        $('#formCreateOrganisation .field-error').remove();
        $('#formCreateOrganisation .select-icon').css('color', '');
    }

    function validateForm() {
        let isValid = true;

        // ✅ Correction : utiliser les IDs du Blade (orgNom, orgSigle, etc.)
        const nom = $('#orgNom').val()?.trim() ?? '';
        if (!nom) {
            showFieldError($('#orgNom'), "Le nom de l'organisation est requis");
            isValid = false;
        } else if (nom.length > 150) {
            showFieldError($('#orgNom'), "Le nom ne doit pas dépasser 150 caractères");
            isValid = false;
        }

        const sigle = $('#orgSigle').val()?.trim() ?? '';
        if (sigle && sigle.length > 20) {
            showFieldError($('#orgSigle'), "Le sigle ne doit pas dépasser 20 caractères");
            isValid = false;
        }

        const type = $('#orgType').val();
        if (type === '' || type === null || type === undefined) {
            showFieldError($('#orgType'), "Le type d'organisation est requis");
            isValid = false;
        }

        const adresse = $('#orgAdresse').val() ?? '';
        if (adresse && adresse.length > 255) {
            showFieldError($('#orgAdresse'), "L'adresse ne doit pas dépasser 255 caractères");
            isValid = false;
        }

        const telephone = $('#orgTelephone').val()?.trim() ?? '';
        if (telephone && telephone.length > 20) {
            showFieldError($('#orgTelephone'), "Le téléphone ne doit pas dépasser 20 caractères");
            isValid = false;
        }

        const email = $('#orgEmail').val()?.trim() ?? '';
        if (email) {
            const emailRegex = /^[^\s@]+@([^\s@.,]+\.)+[^\s@.,]{2,}$/;
            if (!emailRegex.test(email)) {
                showFieldError($('#orgEmail'), "Veuillez saisir une adresse email valide");
                isValid = false;
            } else if (email.length > 150) {
                showFieldError($('#orgEmail'), "L'email ne doit pas dépasser 150 caractères");
                isValid = false;
            }
        }

        return isValid;
    }

    // ─────────────────────────────────────
    // ÉVÉNEMENTS
    // ─────────────────────────────────────

    // Nettoyage live des erreurs
    $(document).on('input change', '#formCreateOrganisation input, #formCreateOrganisation select', function () {
        clearFieldError($(this));
    });

    // Reset à l'ouverture
    $('#modalCreateOrganisation').off('show.bs.modal').on('show.bs.modal', function () {
        resetCreateOrganisationForm();
    });

    // Reset à la fermeture
    $('#modalCreateOrganisation').off('hidden.bs.modal').on('hidden.bs.modal', function () {
        resetCreateOrganisationForm();
    });

    // Soumission
    $('#formCreateOrganisation').off('submit').on('submit', function (e) {
        e.preventDefault();

        clearAllErrors();
        if (!validateForm()) {
            const $first = $('.is-invalid').first();
            if ($first.length) $('html, body').animate({ scrollTop: $first.offset().top - 100 }, 300);
            return;
        }

        const $btn     = $('#btnSubmitCreateOrg'); // ✅ ID correct du bouton
        const origHtml = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin me-1"></i> Création...').prop('disabled', true);

        $.ajax({
            url:         $(this).attr('action'),
            method:      'POST',
            data:        new FormData(this),
            processData: false,
            contentType: false,
            headers:     { 'X-CSRF-TOKEN': window.CSRF_TOKEN },
            success: function (res) {
                if (res.success) {
                    if (typeof window.showNotification === 'function') {
                        window.showNotification(res.message || 'Organisation créée avec succès', 'success');
                    }
                    $('#modalCreateOrganisation').modal('hide');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    if (typeof window.showNotification === 'function') {
                        window.showNotification(res.message || 'Erreur lors de la création', 'error');
                    }
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    $.each(xhr.responseJSON.errors, function (field, messages) {
                        // ✅ Mapping des champs vers les IDs du Blade
                        const map = {
                            nom: '#orgNom', 
                            sigle: '#orgSigle', 
                            type: '#orgType',
                            adresse: '#orgAdresse', 
                            telephone: '#orgTelephone', 
                            email: '#orgEmail'
                        };
                        const $field = $(map[field] || `[name="${field}"]`);
                        if ($field.length) showFieldError($field, messages[0]);
                        else if (typeof window.showNotification === 'function') {
                            window.showNotification(messages[0], 'error');
                        }
                    });
                    const $first = $('.is-invalid').first();
                    if ($first.length) $('html, body').animate({ scrollTop: $first.offset().top - 100 }, 300);
                } else {
                    const msg = xhr.responseJSON?.message || "Erreur lors de la création de l'organisation";
                    if (typeof window.showNotification === 'function') {
                        window.showNotification(msg, 'error');
                    }
                }
            },
            complete: function () {
                $btn.html(origHtml).prop('disabled', false);
            }
        });
    });
}

function resetCreateOrganisationForm() {
    const form = document.getElementById('formCreateOrganisation');
    if (form) form.reset();
    $('#formCreateOrganisation .is-invalid').removeClass('is-invalid');
    $('#formCreateOrganisation .field-error').remove();
    $('#formCreateOrganisation .select-icon').css('color', '');
}
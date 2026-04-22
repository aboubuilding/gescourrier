/**
 * services/create.js
 * Gestion du Modal Création Service
 * IDs adaptés : srvNom, srvDescription, srvLocalisation, etc.
 */

function initCreateServiceModal() {

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
        $('#formCreateService .is-invalid').removeClass('is-invalid');
        $('#formCreateService .field-error').remove();
        $('#formCreateService .select-icon').css('color', '');
    }

    // ─────────────────────────────────────
    // VALIDATION
    // ─────────────────────────────────────

    function validateForm() {
        let isValid = true;

        const nom = $('#srvNom').val()?.trim() ?? '';
        if (!nom) {
            showFieldError($('#srvNom'), "Le nom du service est requis");
            isValid = false;
        } else if (nom.length > 150) {
            showFieldError($('#srvNom'), "Le nom ne doit pas dépasser 150 caractères");
            isValid = false;
        }

        const description = $('#srvDescription').val()?.trim() ?? '';
        if (description && description.length > 1000) {
            showFieldError($('#srvDescription'), "La description est trop longue (max 1000 caractères)");
            isValid = false;
        }

        const localisation = $('#srvLocalisation').val()?.trim() ?? '';
        if (localisation && localisation.length > 255) {
            showFieldError($('#srvLocalisation'), "La localisation ne doit pas dépasser 255 caractères");
            isValid = false;
        }

        const telephone = $('#srvTelephone').val()?.trim() ?? '';
        if (telephone && telephone.length > 20) {
            showFieldError($('#srvTelephone'), "Le téléphone ne doit pas dépasser 20 caractères");
            isValid = false;
        }

        const email = $('#srvEmail').val()?.trim() ?? '';
        if (email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showFieldError($('#srvEmail'), "Email invalide");
                isValid = false;
            } else if (email.length > 150) {
                showFieldError($('#srvEmail'), "Email trop long");
                isValid = false;
            }
        }

        const organisation = $('#srvOrganisationId').val();
        if (!organisation) {
            showFieldError($('#srvOrganisationId'), "L'organisation est requise");
            isValid = false;
        }

        return isValid;
    }

    // ─────────────────────────────────────
    // ÉVÉNEMENTS
    // ─────────────────────────────────────

    $(document).on('input change', '#formCreateService input, #formCreateService select, #formCreateService textarea', function () {
        clearFieldError($(this));
    });

    $('#modalCreateService').on('show.bs.modal', function () {
        resetCreateServiceForm();
    });

    $('#modalCreateService').on('hidden.bs.modal', function () {
        resetCreateServiceForm();
    });

    // ─────────────────────────────────────
    // SUBMIT
    // ─────────────────────────────────────

    $('#formCreateService').off('submit').on('submit', function (e) {
        e.preventDefault();

        clearAllErrors();

        if (!validateForm()) {
            const $first = $('.is-invalid').first();
            if ($first.length) $('html, body').animate({ scrollTop: $first.offset().top - 100 }, 300);
            return;
        }

        const $btn = $('#btnSubmitCreateService');
        const original = $btn.html();

        $btn.html('<i class="fas fa-spinner fa-spin me-1"></i> Création...').prop('disabled', true);

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN },

            success: function (res) {
                if (res.success) {
                    window.showNotification?.(res.message || 'Service créé avec succès', 'success');
                    $('#modalCreateService').modal('hide');
                    setTimeout(() => location.reload(), 800);
                } else {
                    window.showNotification?.(res.message || 'Erreur lors de la création', 'error');
                }
            },

            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {

                    const map = {
                        nom: '#srvNom',
                        description: '#srvDescription',
                        localisation: '#srvLocalisation',
                        telephone: '#srvTelephone',
                        email: '#srvEmail',
                        organisation_id: '#srvOrganisationId'
                    };

                    $.each(xhr.responseJSON.errors, function (field, messages) {
                        const $field = $(map[field] || `[name="${field}"]`);

                        if ($field.length) {
                            showFieldError($field, messages[0]);
                        } else {
                            window.showNotification?.(messages[0], 'error');
                        }
                    });

                    const $first = $('.is-invalid').first();
                    if ($first.length) $('html, body').animate({ scrollTop: $first.offset().top - 100 }, 300);

                } else {
                    const msg = xhr.responseJSON?.message || "Erreur serveur";
                    window.showNotification?.(msg, 'error');
                }
            },

            complete: function () {
                $btn.html(original).prop('disabled', false);
            }
        });
    });
}

// ─────────────────────────────────────
// RESET FORM
// ─────────────────────────────────────

function resetCreateServiceForm() {
    const form = document.getElementById('formCreateService');
    if (form) form.reset();

    $('#formCreateService .is-invalid').removeClass('is-invalid');
    $('#formCreateService .field-error').remove();
    $('#formCreateService .select-icon').css('color', '');
}
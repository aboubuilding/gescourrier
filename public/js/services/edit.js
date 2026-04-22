/**
 * services/edit.js
 * Gestion du Modal Modification Service
 */

function initEditServiceModal() {

    // ─────────────────────────────────────
    // UTILITAIRES
    // ─────────────────────────────────────

    function clearInlineErrors() {
        $('#formEditService .is-invalid').removeClass('is-invalid');
        $('#formEditService .invalid-feedback, #formEditService .field-error').remove();
        $('#formEditService .select-icon').css('color', '');
    }

    function showInlineErrors(errors) {
        clearInlineErrors();

        const fieldMap = {
            nom: '#editSrvNom',
            description: '#editSrvDescription',
            localisation: '#editSrvLocalisation',
            telephone: '#editSrvTelephone',
            email: '#editSrvEmail',
            organisation_id: '#editSrvOrganisationId'
        };

        $.each(errors, function (field, messages) {
            const msg = Array.isArray(messages) ? messages[0] : messages;
            const $field = $(fieldMap[field] || `[name="${field}"]`);

            if (!$field.length) {
                window.showNotification?.(msg, 'error');
                return;
            }

            $field.addClass('is-invalid');

            const $wrapper = $field.closest('.select-wrapper');

            if ($wrapper.length) {
                $wrapper.find('.select-icon').css('color', '#dc3545');
                $wrapper.after(`<div class="field-error text-danger small mt-1">⚠ ${msg}</div>`);
            } else {
                $field.after(`<div class="invalid-feedback field-error text-danger small mt-1">⚠ ${msg}</div>`);
            }
        });

        const $first = $('#formEditService .is-invalid').first();
        if ($first.length) $('html, body').animate({ scrollTop: $first.offset().top - 100 }, 400);
    }

    function validateForm() {
        const errors = {};

        const nom = $('#editSrvNom').val()?.trim() ?? '';
        if (!nom) errors.nom = ["Le nom du service est requis"];
        else if (nom.length > 150) errors.nom = ["Le nom ne doit pas dépasser 150 caractères"];

        const description = $('#editSrvDescription').val()?.trim() ?? '';
        if (description && description.length > 1000)
            errors.description = ["La description ne doit pas dépasser 1000 caractères"];

        const localisation = $('#editSrvLocalisation').val()?.trim() ?? '';
        if (localisation && localisation.length > 255)
            errors.localisation = ["La localisation ne doit pas dépasser 255 caractères"];

        const telephone = $('#editSrvTelephone').val()?.trim() ?? '';
        if (telephone && telephone.length > 20)
            errors.telephone = ["Le téléphone ne doit pas dépasser 20 caractères"];

        const email = $('#editSrvEmail').val()?.trim() ?? '';
        if (email) {
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email))
                errors.email = ["Email invalide"];
            else if (email.length > 150)
                errors.email = ["Email trop long"];
        }

        const org = $('#editSrvOrganisationId').val();
        if (!org) errors.organisation_id = ["L'organisation est requise"];

        if (Object.keys(errors).length) {
            showInlineErrors(errors);
            return false;
        }

        return true;
    }

    // ─────────────────────────────────────
    // OUVERTURE MODAL EDIT
    // ─────────────────────────────────────

    $(document).off('click', '.btn-edit-service').on('click', '.btn-edit-service', function () {

        const id = $(this).data('id');
        const $btn = $(this);
        const oldHtml = $btn.html();

        if (!id) {
            window.showNotification?.("ID service introuvable", "error");
            return;
        }

        clearInlineErrors();
        $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

        $.get(`${window.servicesConfig.routes.edit}${id}/edit`)
            .done(function (data) {

                $('#editServiceId').val(data.id);
                $('#editSrvNom').val(data.nom || '');
                $('#editSrvDescription').val(data.description || '');
                $('#editSrvLocalisation').val(data.localisation || '');
                $('#editSrvTelephone').val(data.telephone || '');
                $('#editSrvEmail').val(data.email || '');
                $('#editSrvOrganisationId').val(data.organisation_id ?? '').trigger('change');

                $('#modalEditService').modal('show');
            })
            .fail(function (xhr) {
                const msg = xhr.responseJSON?.message || "Erreur chargement service";
                window.showNotification?.(msg, "error");
            })
            .always(function () {
                $btn.html(oldHtml).prop('disabled', false);
            });
    });

    // ─────────────────────────────────────
    // SUBMIT UPDATE
    // ─────────────────────────────────────

    $('#formEditService').off('submit').on('submit', function (e) {
        e.preventDefault();

        clearInlineErrors();
        if (!validateForm()) return;

        const id = $('#editServiceId').val();
        if (!id) {
            window.showNotification?.("ID service manquant", "error");
            return;
        }

        const $btn = $(this).find('[type="submit"]');
        const oldHtml = $btn.html();

        $btn.html('<i class="fas fa-spinner fa-spin me-1"></i> Mise à jour...')
            .prop('disabled', true);

        const formData = new FormData();
        formData.append('_token', window.CSRF_TOKEN);
        formData.append('_method', 'PUT');

        formData.append('nom', $('#editSrvNom').val().trim());
        formData.append('description', $('#editSrvDescription').val() || '');
        formData.append('localisation', $('#editSrvLocalisation').val() || '');
        formData.append('telephone', $('#editSrvTelephone').val().trim() || '');
        formData.append('email', $('#editSrvEmail').val().trim() || '');
        formData.append('organisation_id', $('#editSrvOrganisationId').val());

        $.ajax({
            url: `${window.servicesConfig.routes.update}${id}`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,

            success: function (res) {
                if (res.success) {
                    window.showNotification?.(res.message || "Service mis à jour", "success");
                    $('#modalEditService').modal('hide');
                    setTimeout(() => location.reload(), 800);
                } else {
                    window.showNotification?.(res.message || "Erreur mise à jour", "error");
                }
            },

            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    showInlineErrors(xhr.responseJSON.errors);
                } else {
                    window.showNotification?.(
                        xhr.responseJSON?.message || "Erreur serveur",
                        "error"
                    );
                }
            },

            complete: function () {
                $btn.html(oldHtml).prop('disabled', false);
            }
        });
    });

    // ─────────────────────────────────────
    // CLEAN LIVE
    // ─────────────────────────────────────

    $(document).on('input change', '#formEditService input, #formEditService select, #formEditService textarea', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.field-error').remove();

        const $wrapper = $(this).closest('.select-wrapper');
        if ($wrapper.length) {
            $wrapper.find('.select-icon').css('color', '');
            $wrapper.next('.field-error').remove();
        }
    });

    $('#modalEditService').on('hidden.bs.modal', function () {
        clearInlineErrors();
    });
}
/**
 * organisations/edit.js
 * Gestion du Modal Modification Organisation
 */

function initEditOrganisationModal() {

    // ─────────────────────────────────────
    // UTILITAIRES
    // ─────────────────────────────────────

    function clearInlineErrors() {
        $('#formEditOrganisation .is-invalid').removeClass('is-invalid');
        $('#formEditOrganisation .invalid-feedback, #formEditOrganisation .field-error').remove();
        $('#formEditOrganisation .select-icon').css('color', '');
    }

    function showInlineErrors(errors) {
        clearInlineErrors();

        const fieldMap = {
            nom: '#editNom', sigle: '#editSigle', type: '#editType',
            adresse: '#editAdresse', telephone: '#editTelephone', email: '#editEmail'
        };

        $.each(errors, function (field, messages) {
            const msg    = Array.isArray(messages) ? messages[0] : messages;
            const $field = $(fieldMap[field] || `[name="${field}"]`);

            if (!$field.length) { toastr.error(msg); return; }

            $field.addClass('is-invalid');

            const $wrapper = $field.closest('.select-wrapper');
            if ($wrapper.length) {
                $wrapper.find('.select-icon').css('color', '#dc3545');
                $wrapper.after(`<div class="field-error text-danger small mt-1">⚠ ${msg}</div>`);
            } else {
                $field.after(`<div class="invalid-feedback field-error text-danger small mt-1">⚠ ${msg}</div>`);
            }
        });

        const $first = $('#formEditOrganisation .is-invalid').first();
        if ($first.length) $('html, body').animate({ scrollTop: $first.offset().top - 100 }, 400);
    }

    function validateForm() {
        const errors = {};

        const nom = $('#editNom').val().trim();
        if (!nom) errors['nom'] = ["Le nom de l'organisation est requis"];
        else if (nom.length > 150) errors['nom'] = ["Le nom ne doit pas dépasser 150 caractères"];

        const sigle = $('#editSigle').val().trim();
        if (sigle && sigle.length > 20) errors['sigle'] = ["Le sigle ne doit pas dépasser 20 caractères"];

        const type = $('#editType').val();
        if (type === '' || type === null) errors['type'] = ["Le type d'organisation est requis"];

        const adresse = $('#editAdresse').val();
        if (adresse && adresse.length > 255) errors['adresse'] = ["L'adresse ne doit pas dépasser 255 caractères"];

        const telephone = $('#editTelephone').val().trim();
        if (telephone && telephone.length > 20) errors['telephone'] = ["Le téléphone ne doit pas dépasser 20 caractères"];

        const email = $('#editEmail').val().trim();
        if (email) {
            if (!/^[^\s@]+@([^\s@.,]+\.)+[^\s@.,]{2,}$/.test(email))
                errors['email'] = ["L'email doit être une adresse valide"];
            else if (email.length > 150)
                errors['email'] = ["L'email ne doit pas dépasser 150 caractères"];
        }

        if (Object.keys(errors).length) { showInlineErrors(errors); return false; }
        return true;
    }

    // ─────────────────────────────────────
    // OUVERTURE — clic sur .btn-edit-organisation
    // ─────────────────────────────────────

    $(document).off('click', '.btn-edit-organisation').on('click', '.btn-edit-organisation', function () {
        const id       = $(this).data('id');
        const $btn     = $(this);
        const origHtml = $btn.html();

        if (!id) { toastr.error("ID de l'organisation non trouvé"); return; }

        clearInlineErrors();
        $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

        $.get(`${window.organisationsConfig.routes.edit}${id}/edit`)
            .done(function (data) {
                if (!data.id) { toastr.error("Données de l'organisation invalides"); return; }

                $('#editOrganisationId').val(data.id);
                $('#editNom').val(data.nom || '');
                $('#editSigle').val(data.sigle || '');
                $('#editType').val(data.type ?? '').trigger('change');
                $('#editAdresse').val(data.adresse || '');
                $('#editTelephone').val(data.telephone || '');
                $('#editEmail').val(data.email || '');

                $('#modalEditOrganisation').modal('show');
            })
            .fail(function (xhr) {
                const msg = xhr.status === 404
                    ? 'Organisation non trouvée'
                    : xhr.responseJSON?.message || 'Impossible de charger les données';
                toastr.error(msg);
            })
            .always(function () {
                $btn.html(origHtml).prop('disabled', false);
            });
    });

    // ─────────────────────────────────────
    // SOUMISSION
    // ─────────────────────────────────────

    $('#formEditOrganisation').off('submit').on('submit', function (e) {
        e.preventDefault();

        clearInlineErrors();
        if (!validateForm()) return;

        const organisationId = $('#editOrganisationId').val();
        if (!organisationId) { toastr.error("ID de l'organisation manquant."); return; }

        const $submitBtn = $(this).find('[type="submit"]');
        const origHtml   = $submitBtn.html();
        $submitBtn.html('<i class="fas fa-spinner fa-spin me-1"></i> Mise à jour...').prop('disabled', true);

        const formData = new FormData();
        formData.append('_token',    window.CSRF_TOKEN);
        formData.append('_method',   'PUT');
        formData.append('nom',       $('#editNom').val().trim());
        formData.append('sigle',     $('#editSigle').val().trim() || '');
        formData.append('type',      $('#editType').val());
        formData.append('adresse',   $('#editAdresse').val() || '');
        formData.append('telephone', $('#editTelephone').val().trim() || '');
        formData.append('email',     $('#editEmail').val().trim() || '');

        $.ajax({
            url:         `${window.organisationsConfig.routes.update}${organisationId}`,
            method:      'POST',
            data:        formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    toastr.success(res.message || 'Organisation mise à jour avec succès');
                    $('#modalEditOrganisation').modal('hide');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(res.message || 'Erreur lors de la mise à jour');
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    showInlineErrors(xhr.responseJSON.errors);
                } else if (xhr.status === 404) {
                    toastr.error('Organisation non trouvée.');
                } else if (xhr.status === 409) {
                    toastr.error(xhr.responseJSON?.message || 'Ce sigle est déjà utilisé.');
                } else {
                    toastr.error(xhr.responseJSON?.message || 'Erreur lors de la modification.');
                }
            },
            complete: function () {
                $submitBtn.html(origHtml).prop('disabled', false);
            }
        });
    });

    // ─────────────────────────────────────
    // RESET + NETTOYAGE LIVE
    // ─────────────────────────────────────

    $('#modalEditOrganisation').off('hidden.bs.modal').on('hidden.bs.modal', function () {
        clearInlineErrors();
    });

    $(document).off('input change', '#formEditOrganisation input, #formEditOrganisation select')
               .on('input change',  '#formEditOrganisation input, #formEditOrganisation select', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.field-error').remove();
        const $wrapper = $(this).closest('.select-wrapper');
        if ($wrapper.length) {
            $wrapper.find('.select-icon').css('color', '');
            $wrapper.next('.field-error').remove();
        }
    });
}
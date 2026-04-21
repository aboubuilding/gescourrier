$(document).ready(function () {

    // ═══════════════════════════════════════
    // ⚙️ INIT
    // ═══════════════════════════════════════

    const $form      = $('#form-login');
    const $btnLogin  = $('#btn-login');

    // CSRF Token pour toutes les requêtes AJAX
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // Focus automatique sur le champ email
    if (!$('#email').val()) $('#email').focus();

    // ═══════════════════════════════════════
    // 👁️ AFFICHER / MASQUER MOT DE PASSE
    // ═══════════════════════════════════════

    $('#toggle-pw').on('click', function () {
        const $input = $('#password');
        const isPassword = $input.attr('type') === 'password';
        $input.attr('type', isPassword ? 'text' : 'password');
        $('#eye-icon').toggleClass('fa-eye fa-eye-slash');
        $input.focus();
    });

    // ═══════════════════════════════════════
    // ✅ VALIDATION SIMPLE
    // ═══════════════════════════════════════

    function clearErrors() {
        $('.field-input').removeClass('error');
        $('.error-message').removeClass('show').text('');
    }

    function showFieldError(fieldId, message) {
        $('#' + fieldId).addClass('error');
        $('#error-' + fieldId).text(message).addClass('show');
    }

    function validateForm() {
        let valid = true;

        if (!$('#email').val().trim()) {
            showFieldError('email', 'Ce champ est requis');
            valid = false;
        }

        if (!$('#password').val().trim()) {
            showFieldError('password', 'Ce champ est requis');
            valid = false;
        }

        return valid;
    }

    // Validation en temps réel à la saisie
    $('#email, #password').on('blur', function () {
        if ($(this).val().trim()) {
            $(this).removeClass('error');
            $('#error-' + $(this).attr('id')).removeClass('show');
        }
    });

    // ═══════════════════════════════════════
    // 🔄 SOUMISSION AJAX
    // ═══════════════════════════════════════

    $form.on('submit', function (e) {
        e.preventDefault();
        clearErrors();

        if (!validateForm()) return;

        setLoading(true);

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            timeout: 15000,

            success: function (res) {
                if (res.success) {
                    // ✅ Redirection vers le tableau de bord
                    window.location.href = '/';
                } else {
                    handleErrors(res.errors, res.message || 'Erreur de connexion.');
                }
            },

            error: function (xhr) {
                if (xhr.status === 422) {
                    // Erreurs de validation Laravel
                    const errors = xhr.responseJSON?.errors || {};
                    handleErrors(errors, 'Veuillez corriger les erreurs.');
                } else if (xhr.status === 401 || xhr.status === 403) {
                    showFieldError('password', 'Identifiants incorrects ou compte suspendu.');
                } else {
                    showFieldError('email', 'Une erreur est survenue. Veuillez réessayer.');
                }
            },

            complete: function () {
                setLoading(false);
            }
        });
    });

    // ═══════════════════════════════════════
    // 🛠️ HELPERS
    // ═══════════════════════════════════════

    // Affiche les erreurs renvoyées par Laravel (champ par champ)
    function handleErrors(errors, fallback) {
        if (!errors || Object.keys(errors).length === 0) {
            showFieldError('email', fallback);
            return;
        }

        const fieldMap = {
            'email':    'email',
            'password': 'password'
        };

        let focused = false;
        Object.entries(errors).forEach(function ([field, messages]) {
            const fieldId = fieldMap[field];
            if (fieldId && messages?.[0]) {
                showFieldError(fieldId, messages[0]);
                if (!focused) { $('#' + fieldId).focus(); focused = true; }
            }
        });
    }

    // Active / désactive le bouton de soumission
    function setLoading(loading) {
        $btnLogin.toggleClass('loading', loading).prop('disabled', loading);
        $btnLogin.find('.btn-text').text(loading ? 'Connexion...' : 'Accéder au portail');
        $btnLogin.find('.spinner').toggle(loading);
        $btnLogin.find('.btn-arrow').toggle(!loading);
    }

    // ═══════════════════════════════════════
    // 🔐 MODAL "MOT DE PASSE OUBLIÉ"
    // ═══════════════════════════════════════

    const $modal = $('#forgotModal');

    $('#forgotLink').on('click', function (e) {
        e.preventDefault();
        $modal.addClass('active');
        $('#forgotEmail').focus();
    });

    function closeModal() {
        $modal.removeClass('active');
        $('#forgotForm')[0].reset();
        $('#forgotError').text('').hide();
        $('#forgotEmail').removeClass('error');
    }

    $('#modalClose, #modalCancel').on('click', closeModal);
    $modal.on('click', function (e) { if ($(e.target).is($modal)) closeModal(); });
    $(document).on('keydown', function (e) { if (e.key === 'Escape') closeModal(); });

    $('#forgotForm').on('submit', function (e) {
        e.preventDefault();
        const email = $('#forgotEmail').val().trim();

        if (!email) {
            $('#forgotError').text('Ce champ est requis').show();
            $('#forgotEmail').addClass('error');
            return;
        }

        const $btn = $('#modalSubmit');
        $btn.prop('disabled', true).addClass('loading');

        // Appel AJAX réel vers la route forgot-password
        $.ajax({
            url: '/forgot-password',
            method: 'POST',
            data: { email: email },
            dataType: 'json',

            complete: function () {
                // Message générique pour la sécurité (ne pas révéler si l'email existe)
                alert('Si ce compte existe, un email de réinitialisation a été envoyé.');
                $btn.prop('disabled', false).removeClass('loading');
                closeModal();
            }
        });
    });

    // ═══════════════════════════════════════
    // 🧹 FERMETURE DES ALERTES LARAVEL
    // ═══════════════════════════════════════

    $('.alert-close').on('click', function () {
        $(this).closest('.alert-box').fadeOut(200, function () { $(this).remove(); });
    });

});
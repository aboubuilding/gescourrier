$(document).ready(function() {
    
    // ═══════════════════════════════════════
    // ⚙️ CONFIG & DEBUG
    // ═══════════════════════════════════════
    
    console.log('✅ jQuery loaded:', jQuery.fn.jquery);
    console.log('✅ login.js executed');
    
    const $form = $('#form-login');
    const $btnLogin = $('#btn-login');
    const $toastContainer = $('#toast-container');
    
    // CSRF Token pour AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // ═══════════════════════════════════════
    // 🔔 TOAST NOTIFICATIONS
    // ═══════════════════════════════════════
    
    function showToast(message, type = 'info', title = '') {
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };
        
        const $toast = $(`
            <div class="toast ${type}" role="alert">
                <div class="toast-icon"><i class="fas ${icons[type] || icons.info}"></i></div>
                <div class="toast-content">
                    ${title ? `<div class="toast-title">${title}</div>` : ''}
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" aria-label="Fermer"><i class="fas fa-times"></i></button>
            </div>
        `);
        
        $toast.find('.toast-close').on('click', () => $toast.remove());
        $toastContainer.append($toast);
        setTimeout(() => $toast.remove(), 3200);
    }
    
    // ═══════════════════════════════════════
    // 👁️ TOGGLE MOT DE PASSE
    // ═══════════════════════════════════════
    
    $('#toggle-pw').on('click', function() {
        const $input = $('#password');  // ✅ Changé de #mot_passe à #password
        const $icon = $('#eye-icon');
        const isPassword = $input.attr('type') === 'password';
        $input.attr('type', isPassword ? 'text' : 'password');
        $icon.toggleClass('fa-eye fa-eye-slash');
        $input.focus();
    });
    
    // ═══════════════════════════════════════
    // ✅ VALIDATION EN TEMPS RÉEL
    // ═══════════════════════════════════════
    
    function validateField($field, errorId) {
        const value = $field.val().trim();
        const $error = $('#' + errorId);
        if (!value) {
            $field.addClass('error');
            $error.addClass('show');
            return false;
        }
        $field.removeClass('error');
        $error.removeClass('show');
        return true;
    }
    
    // ✅ Changé de #login_utilisateur à #email
    $('#email, #password').on('blur input', function() {
        const errorId = $(this).attr('id') === 'email' ? 'error-email' : 'error-password';
        validateField($(this), errorId);
    });
    
    // ═══════════════════════════════════════
    // 🔄 SOUMISSION AJAX DU FORMULAIRE
    // ═══════════════════════════════════════
    
    $form.on('submit', function(e) {
        e.preventDefault();
        
        // Reset des erreurs
        $('.field-input').removeClass('error');
        $('.error-message').removeClass('show').text('');
        
        // Validation basique
        let hasError = false;
        ['#email', '#password'].forEach(selector => {  // ✅ Changé ici
            const $field = $(selector);
            if (!$field.val().trim()) {
                $field.addClass('error');
                // ✅ Mapping simple : email → error-email, password → error-password
                const errorId = `#error-${$field.attr('id')}`;
                $(errorId).text('Ce champ est requis').addClass('show');
                if (!hasError) { $field.focus(); hasError = true; }
            }
        });
        
        if (hasError) {
            showToast('Veuillez remplir tous les champs obligatoires', 'error');
            return;
        }
        
        // État de chargement
        setLoading(true);
        
        // AJAX
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),  // ✅ Envoie automatiquement email & password
            dataType: 'json',
            timeout: 15000,
            
            success: function(res) {
                if (res.success) {
                    showToast('Connexion réussie !', 'success');
                    setTimeout(() => {
                        window.location.href = res.data?.redirect || '{{ route("dashboard.index") }}';
                    }, 1000);
                } else {
                    displayErrors(res.errors, res.message || 'Erreur de connexion');
                }
            },
            
            error: function(xhr) {
                if (xhr.status === 422) {
                    displayErrors(xhr.responseJSON?.errors, 'Veuillez corriger les erreurs');
                } else if ([401, 403].includes(xhr.status)) {
                    showToast('Identifiants incorrects ou compte suspendu', 'error');
                    highlightField('password', 'Mot de passe incorrect');  // ✅ Changé de mot_passe à password
                } else if (xhr.status === 0) {
                    showToast('Problème de connexion au serveur', 'error');
                } else {
                    showToast('Une erreur est survenue. Veuillez réessayer.', 'error');
                }
            },
            
            complete: () => setLoading(false)
        });
    });
    
    // ═══════════════════════════════════════
    // 🛠️ HELPERS
    // ═══════════════════════════════════════
    
    function displayErrors(errors, fallbackMsg) {
        if (!errors) { showToast(fallbackMsg, 'error'); return; }
        
        // ✅ Mapping mis à jour pour email/password
        const map = {
            'email':    ['#email', '#error-email'],
            'password': ['#password', '#error-password'],
        };
        
        let firstField = null;
        
        Object.entries(errors).forEach(([field, msgs]) => {
            const [inputSel, errorSel] = map[field] || [];
            if (inputSel && errorSel && msgs?.[0]) {
                $(inputSel).addClass('error');
                $(errorSel).text(msgs[0]).addClass('show');
                if (!firstField) firstField = inputSel;
            }
        });
        
        if (firstField) $(firstField).focus();
        showToast(fallbackMsg, 'error');
    }
    
    function highlightField(fieldId, message) {
        $(`#${fieldId}`).addClass('error').focus();
        const errorId = `#error-${fieldId}`;  // ✅ Simplifié
        if ($(errorId).length) $(errorId).text(message).addClass('show');
    }
    
    function setLoading(loading) {
        $btnLogin.toggleClass('loading', loading).prop('disabled', loading);
        $btnLogin.find('.btn-text').text(loading ? 'Connexion...' : 'Accéder au portail');
        $btnLogin.find('.btn-arrow, .spinner').toggle(!loading);
    }
    
    // ═══════════════════════════════════════
    // 🔐 MODAL "MOT DE PASSE OUBLIÉ"
    // ═══════════════════════════════════════
    
    const $modal = $('#forgotModal');
    const $forgotForm = $('#forgotForm');
    const $modalSubmit = $('#modalSubmit');
    
    $('#forgotLink').on('click', function(e) {
        e.preventDefault();
        $modal.addClass('active');
        $('#forgotEmail').focus();
        $('body').css('overflow', 'hidden');
    });
    
    function closeModal() {
        $modal.removeClass('active');
        $('body').css('overflow', '');
        $forgotForm[0].reset();
        $('#forgotError').text('').hide();
    }
    
    $('#modalClose, #modalCancel').on('click', closeModal);
    $modal.on('click', e => { if ($(e.target).is($modal)) closeModal(); });
    $(document).on('keydown', e => { if (e.key === 'Escape' && $modal.hasClass('active')) closeModal(); });
    
    $forgotForm.on('submit', function(e) {
        e.preventDefault();
        const email = $('#forgotEmail').val().trim();
        const $error = $('#forgotError');
        
        if (!email) {
            $error.text('Ce champ est requis').show();
            $('#forgotEmail').addClass('error');
            return;
        }
        
        $modalSubmit.prop('disabled', true).addClass('loading');
        $modalSubmit.find('.btn-text').hide();
        $modalSubmit.find('.spinner').show();
        
        setTimeout(() => {
            showToast('Si ce compte existe, vous recevrez un email de réinitialisation.', 'success', 'Lien envoyé');
            closeModal();
        }, 1500);
    });
    
    $('#forgotEmail').on('blur input', function() {
        const $input = $(this), $error = $('#forgotError');
        if (!$input.val().trim()) {
            $input.addClass('error');
            $error.text('Ce champ est requis').show();
        } else {
            $input.removeClass('error');
            $error.hide();
        }
    });
    
    // ═══════════════════════════════════════
    // 🎨 UI & INIT
    // ═══════════════════════════════════════
    
    $('.field-input').on('focus', function() {
        $(this).closest('.field-group').css('z-index', '2');
    }).on('blur', function() {
        $(this).closest('.field-group').css('z-index', '');
    });
    
    $('.alert-close').on('click', function() {
        $(this).closest('.alert-box').fadeOut(200, function() { $(this).remove(); });
    });
    
    // ✅ Focus sur email si vide (au lieu de login_utilisateur)
    if (!$('#email').val()) $('#email').focus();
    
    // ✅ localStorage : clé 'saved_email' au lieu de 'saved_login'
    const savedEmail = localStorage.getItem('saved_email');
    if (savedEmail && !$('#email').val()) {
        $('#email').val(savedEmail);
        $('#remember').prop('checked', true);
    }
    
    $form.on('submit', function() {
        if ($('#remember').is(':checked')) {
            localStorage.setItem('saved_email', $('#email').val());  // ✅ Changé ici
        } else {
            localStorage.removeItem('saved_email');
        }
    });
    
    if (!sessionStorage.getItem('welcome_shown')) {
        setTimeout(() => {
            showToast('Bienvenue sur la plateforme de gestion des courriers', 'info', '👋 Bonjour');
            sessionStorage.setItem('welcome_shown', 'true');
        }, 1000);
    }
});
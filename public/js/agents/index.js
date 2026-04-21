    });

    // ═══════════════════════════════════════
    // ✏️ MODIFICATION : Charger les données
    // ═══════════════════════════════════════
    
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        const $btn = $(this);
        
        // Feedback visuel pendant le chargement
        const originalHtml = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
        
        $.get(`/agents/${id}/edit`, function(data) {
            // Remplir les champs du modal
            $('#editId').val(data.id);
            $('#editNom').val(data.nom);
            $('#editPrenom').val(data.prenom);
            $('#editEmail').val(data.email);
            $('#editTelephone').val(data.telephone);
            $('#editFonction').val(data.fonction);
            
            // Select2 : mettre à jour + trigger change pour refresh UI
            if (data.service_id) {
                $('#editService').val(data.service_id).trigger('change');
            }
            if (data.user_id) {
                $('#editUser').val(data.user_id).trigger('change');
            } else {
                $('#editUser').val('').trigger('change');
            }
            
            $('#formEdit').attr('action', `/agents/${data.id}`);
            
            // Afficher le modal + réinitialiser Select2
            $('#modalEdit').modal('show');
            initSelect2InModal($('#modalEdit'));
        })
        .fail(function() {
            showToast('Impossible de charger les données de l\'agent', 'error');
        })
        .always(function() {
            // Restaurer le bouton
            $btn.html(originalHtml).prop('disabled', false);
        });
    });
    
    // ✏️ MODIFICATION : Soumettre le formulaire
    $('#formEdit').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $btn = $('#btnSubmitEdit');
        const $spinner = $('#spinnerEdit');
        
        toggleButton($btn, $spinner, true);
        
        $.ajax({
            url: $form.attr('action'),
            method: 'POST', // Laravel accepte POST avec @method('PUT')
            data: $form.serialize(),
            headers: { 'X-CSRF-TOKEN': CSRF },
            success: function(res) {
                showToast(res.message || 'Agent mis à jour', 'success');
                $('#modalEdit').modal('hide');
                setTimeout(() => location.reload(), 800);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.values(errors).flat().forEach(msg => showToast(msg, 'error'));
                } else {
                    showToast(xhr.responseJSON?.message || 'Erreur lors de la mise à jour', 'error');
                }
            },
            complete: function() {
                toggleButton($btn, $spinner, false);
            }
        });
    });

    // ═══════════════════════════════════════
    // 🔗 LIER COMPTE UTILISATEUR
    // ═══════════════════════════════════════
    
    $(document).on('click', '.btn-link-user', function() {
        const id = $(this).data('id');
        $('#linkAgentId').val(id);
        
        // Réinitialiser le select avant ouverture
        $('#formLinkUser select[name="user_id"]').val('').trigger('change');
        
        $('#modalLinkUser').modal('show');
    });
    
    $('#formLinkUser').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $btn = $form.find('button[type="submit"]');
        const $spinner = $('#spinnerLink');
        const id = $('#linkAgentId').val();
        
        toggleButton($btn, $spinner, true);
        
        $.ajax({
            url: `/agents/${id}/lier-user`,
            method: 'POST',
            data: $form.serialize(),
            headers: { 'X-CSRF-TOKEN': CSRF },
            success: function(res) {
                showToast(res.message || 'Compte lié avec succès', 'success');
                $('#modalLinkUser').modal('hide');
                setTimeout(() => location.reload(), 800);
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Erreur lors de la liaison', 'error');
            },
            complete: function() {
                toggleButton($btn, $spinner, false);
            }
        });
    });

    // ═══════════════════════════════════════
    // 🔄 RÉASSIGNER SERVICE
    // ═══════════════════════════════════════
    
    $(document).on('click', '.btn-reassign', function() {
        const id = $(this).data('id');
        $('#reassignAgentId').val(id);
        
        // Réinitialiser le select
        $('#formReassign select[name="service_id"]').val('').trigger('change');
        
        $('#modalReassign').modal('show');
    });
    
    $('#formReassign').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $btn = $form.find('button[type="submit"]');
        const $spinner = $('#spinnerReassign');
        const id = $('#reassignAgentId').val();
        
        toggleButton($btn, $spinner, true);
        
        $.ajax({
            url: `/agents/${id}/reassigner-service`,
            method: 'POST',
            data: $form.serialize(),
            headers: { 'X-CSRF-TOKEN': CSRF },
            success: function(res) {
                showToast(res.message || 'Service réassigné', 'success');
                $('#modalReassign').modal('hide');
                setTimeout(() => location.reload(), 800);
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Erreur lors de la réassignation', 'error');
            },
            complete: function() {
                toggleButton($btn, $spinner, false);
            }
        });
    });

    // ═══════════════════════════════════════
    // ⏸️ SUSPENDRE / ▶️ RÉACTIVER
    // ═══════════════════════════════════════
    
    $(document).on('click', '.btn-suspend', function() {
        const id = $(this).data('id');
        
        if (typeof Swal === 'undefined') {
            if (confirm('Suspendre cet agent ? Il ne pourra plus accéder au système.')) {
                suspendAgent(id);
            }
            return;
        }
        
        Swal.fire({
            title: 'Suspendre cet agent ?',
            text: 'L\'agent ne pourra plus accéder au système.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'var(--text-muted, #64748b)',
            cancelButtonColor: 'var(--border, #e2e8f0)',
            confirmButtonText: 'Suspendre',
            cancelButtonText: 'Annuler',
            background: 'var(--bg-card, #fff)',
            color: 'var(--text-primary, #1e293b)'
        }).then(result => {
            if (result.isConfirmed) {
                suspendAgent(id);
            }
        });
    });
    
    function suspendAgent(id) {
        $.ajax({
            url: `/agents/${id}`,
            method: 'DELETE', // Ou POST /agents/{id}/suspend selon ta route
            headers: { 'X-CSRF-TOKEN': CSRF },
            success: function(res) {
                showToast(res.message || 'Agent suspendu', 'success');
                setTimeout(() => location.reload(), 800);
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Erreur lors de la suspension', 'error');
            }
        });
    }
    
    $(document).on('click', '.btn-restore', function() {
        const id = $(this).data('id');
        
        if (typeof Swal === 'undefined') {
            if (confirm('Réactiver cet agent ?')) {
                restoreAgent(id);
            }
            return;
        }
        
        Swal.fire({
            title: 'Réactiver cet agent ?',
            text: 'L\'agent retrouvera un accès actif au système.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: 'var(--success, #16a34a)',
            cancelButtonColor: 'var(--border, #e2e8f0)',
            confirmButtonText: 'Réactiver',
            cancelButtonText: 'Annuler',
            background: 'var(--bg-card, #fff)',
            color: 'var(--text-primary, #1e293b)'
        }).then(result => {
            if (result.isConfirmed) {
                restoreAgent(id);
            }
        });
    });
    
    function restoreAgent(id) {
        $.post(`/agents/${id}/restaurer`, { _token: CSRF })
            .done(function(res) {
                showToast(res.message || 'Agent réactivé', 'success');
                setTimeout(() => location.reload(), 800);
            })
            .fail(function(xhr) {
                showToast(xhr.responseJSON?.message || 'Erreur lors de la réactivation', 'error');
            });
    }

    // ═══════════════════════════════════════
    // 📤 EXPORT
    // ═══════════════════════════════════════
    
    $('#exportExcel, #exportPDF, #exportCSV').on('click', function(e) {
        e.preventDefault();
        
        const format = this.id.replace('export', '').toLowerCase();
        const filters = {
            search: $('#searchInput').val(),
            fonction: $('.function-pill.active').data('function'),
            service: $('#filterService').val(),
            etat: $('#filterEtat').val()
        };
        
        // Feedback utilisateur
        showToast(`Préparation de l'export ${format.toUpperCase()}...`, 'info');
        
        // Redirection vers l'URL d'export
        const params = new URLSearchParams({
            format: format,
            filters: JSON.stringify(filters)
        });
        window.location.href = `/agents/export?${params.toString()}`;
    });

    // ═══════════════════════════════════════
    // 🎨 UI : Dropdowns actions
    // ═══════════════════════════════════════
    
    $(document).on('click', '.action-trigger', function(e) {
        e.stopPropagation();
        const $dropdown = $(this).closest('.action-dropdown');
        const isOpen = $dropdown.hasClass('open');
        
        // Fermer tous les dropdowns
        $('.action-dropdown').removeClass('open');
        
        // Ouvrir celui-ci si il était fermé
        if (!isOpen) {
            $dropdown.addClass('open');
        }
    });
    
    // Fermer les dropdowns au clic extérieur
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.action-dropdown').length) {
            $('.action-dropdown').removeClass('open');
        }
    });

    // ═══════════════════════════════════════
    // 🔽 SELECT2 DANS MODALS
    // ═══════════════════════════════════════
    
    // Réinitialiser Select2 à l'ouverture de chaque modal
    $('#modalCreate, #modalEdit, #modalLinkUser, #modalReassign').on('shown.bs.modal', function() {
        initSelect2InModal($(this));
    });
    
    // Nettoyer Select2 à la fermeture pour éviter les fuites mémoire
    $('#modalCreate, #modalEdit, #modalLinkUser, #modalReassign').on('hidden.bs.modal', function() {
        $(this).find('select.select2').select2('destroy');
    });

    // ═══════════════════════════════════════
    // 🔔 FLASH MESSAGES (via data attributes)
    // ═══════════════════════════════════════
    
    // Les messages flash sont passés via data attributes sur le body
    const $body = $('body');
    const flashSuccess = $body.data('flash-success');
    const flashError = $body.data('flash-error');
    
    if (flashSuccess) showToast(flashSuccess, 'success');
    if (flashError) showToast(flashError, 'error');

    // ═══════════════════════════════════════
    // ⌨️ RACCOURCIS CLAVIER (optionnel)
    // ═══════════════════════════════════════
    
    $(document).on('keydown', function(e) {
        // Ctrl/Cmd + N : Nouvel agent
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'n') {
            if (!['input', 'textarea', 'select'].includes(e.target.tagName.toLowerCase())) {
                e.preventDefault();
                $('#modalCreate').modal('show');
            }
        }
        
        // Échap : Fermer les dropdowns actions
        if (e.key === 'Escape') {
            $('.action-dropdown').removeClass('open');
        }
    });

    // ═══════════════════════════════════════
    // 🎯 INIT : Message de bienvenue
    // ═══════════════════════════════════════
    
    if (!sessionStorage.getItem('agents_welcome') && typeof toastr !== 'undefined') {
        setTimeout(() => {
            showToast('💡 Astuce : Utilisez les filtres pour trouver rapidement un agent', 'info');
            sessionStorage.setItem('agents_welcome', 'true');
        }, 2000);
    }
    
}); // ← FIN DU $(document).ready()
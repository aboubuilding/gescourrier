/**
 * ACTIONS - Organisations
 */

function initActions() {
    initSuspendAction();
    initRestoreAction();
    initServicesAction();
    initExportAction();
}

function initSuspendAction() {
    // Désactiver une organisation
    $(document).off('click', '.btn-suspend').on('click', '.btn-suspend', function() {
        const id = $(this).data('id');
        const $btn = $(this);
        
        Swal.fire({
            title: 'Désactiver l\'organisation ?',
            text: 'L\'organisation sera masquée des listes actives.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#64748b',
            confirmButtonText: 'Oui, désactiver',
            cancelButtonText: 'Annuler',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const originalHtml = $btn.html();
                $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
                
                $.ajax({
                    url: `${window.organisationsConfig.routes.delete}${id}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': window.organisationsConfig.csrfToken
                    },
                    success: function(response) {
                        toastr.success(response.message || 'Organisation désactivée avec succès');
                        setTimeout(() => location.reload(), 800);
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || 'Erreur lors de la désactivation';
                        toastr.error(message);
                        $btn.html(originalHtml).prop('disabled', false);
                    }
                });
            }
        });
    });
}

function initRestoreAction() {
    // Réactiver une organisation
    $(document).off('click', '.btn-restore').on('click', '.btn-restore', function() {
        const id = $(this).data('id');
        const $btn = $(this);
        
        Swal.fire({
            title: 'Réactiver l\'organisation ?',
            text: 'L\'organisation réapparaîtra dans les listes actives.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#009a44',
            confirmButtonText: 'Oui, réactiver',
            cancelButtonText: 'Annuler',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const originalHtml = $btn.html();
                $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
                
                $.ajax({
                    url: `${window.organisationsConfig.routes.restore}${id}/restaurer`,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.organisationsConfig.csrfToken
                    },
                    success: function(response) {
                        toastr.success(response.message || 'Organisation réactivée avec succès');
                        setTimeout(() => location.reload(), 800);
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || 'Erreur lors de la réactivation';
                        toastr.error(message);
                        $btn.html(originalHtml).prop('disabled', false);
                    }
                });
            }
        });
    });
}

function initServicesAction() {
    // Voir les services liés
    $(document).off('click', '.btn-services').on('click', '.btn-services', function() {
        const id = $(this).data('id');
        window.location.href = `/services?organisation_id=${id}`;
    });
}

function initExportAction() {
    // Export
    $('#exportExcel, #exportPDF, #exportCSV').off('click').on('click', function(e) {
        e.preventDefault();
        
        const format = this.id.replace('export', '').toLowerCase();
        const search = $('#searchInput').val();
        const type = $('.type-pill.active').data('type') || $('#filterType').val();
        const etat = $('#filterEtat').val();
        
        const filters = { search, type, etat };
        
        window.location.href = `${window.organisationsConfig.routes.export}?format=${format}&filters=${encodeURIComponent(JSON.stringify(filters))}`;
        $('#modalExport').modal('hide');
    });
}
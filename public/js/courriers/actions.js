/**
 * Actions Diverses (Affecter, Supprimer, Export, etc.)
 * - Auto-remplissage service selon agent
 * - Notifications SweetAlert2
 * - Gestion des erreurs et chargements
 */

function initActions() {
    
    // ═══════════════════════════════════════
    // ➕ AFFECTER UN COURRIER
    // ═══════════════════════════════════════
    
    // 1. Ouverture du modal d'affectation
    $(document).off('click', '.btn-affecter').on('click', '.btn-affecter', function() {
        const id = $(this).data('id');
        $('#affecterCourrierId').val(id);
        
        // Reset du formulaire
        $('#formAffecter')[0]?.reset();
        $('#affecterServiceId').val('');
        $('#serviceDisplayGroup').hide();
        $('#affecterServiceName').text('—');
        
        // Reset Select2 si utilisé
        if (typeof $.fn.select2 !== 'undefined') {
            const $agentSelect = $('#affecterAgent');
            
        }
        
        $('#modalAffecter').modal('show');
    });

    // 2. Auto-remplissage du service quand on change l'agent
  $('#affecterAgent').on('change', function() {
    const $selected = $(this).find('option:selected');

    const serviceId = $selected.attr('data-service-id'); // ✅ FIX
    const serviceName = $selected.attr('data-service-name') || 'Non défini';

    console.log(serviceId); // debug

    if (serviceId && serviceId !== '') {
        $('#affecterServiceId').val(serviceId);
        $('#affecterServiceName').text(serviceName);
        $('#serviceDisplayGroup').fadeIn(200);
    } else {
        $('#affecterServiceId').val('');
        $('#serviceDisplayGroup').fadeOut(200);
       
    }
});

    // 3. Soumission du formulaire d'affectation (AJAX)
    $('#formAffecter').off('submit').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $btn = $('#btnSubmitAffecter');
        const $spinner = $('#spinnerAffecter');
        const id = $('#affecterCourrierId').val();
        
        // Validation côté client
        if (!$('#affecterServiceId').val()) {
            window.showNotification('Veuillez sélectionner un agent avec un service rattaché.', 'error');
            return;
        }
        
        // UI Loading
        window.toggleButtonLoading($btn, $spinner, true);

        $.ajax({
            url: `/courriers/${id}/affecter`,
            method: 'POST',
            data :$form.serialize(),
            headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN },
            success: function(res) {
                window.showNotification(res.message || 'Courrier affecté avec succès !', 'success');
                $('#modalAffecter').modal('hide');
                // Recharger après un court délai pour UX
                setTimeout(() => location.reload(), 1200);
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Erreur lors de l\'affectation.';
                window.showNotification(msg, 'error');
            },
            complete: function() {
                window.toggleButtonLoading($btn, $spinner, false);
            }
        });
    });

    // ═══════════════════════════════════════
    // 🗑️ SUPPRIMER UN COURRIER
    // ═══════════════════════════════════════
    
    $(document).off('click', '.btn-delete').on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        
        // Confirmation SweetAlert2
        Swal.fire({
            title: 'Supprimer définitivement ?',
            text: 'Le fichier joint sera également supprimé. Cette action est irréversible.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            background: '#fff',
            backdrop: 'rgba(0,0,0,0.4)'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/courriers/${id}`,
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN },
                    success: function(res) {
                        // Notification de succès
                        window.showNotification(res.message || 'Courrier supprimé.', 'success').then(() => {
                            // Suppression visuelle de la ligne dans DataTable
                            if (window.courrierTable) {
                                const row = window.courrierTable.row($(`tr[data-id="${id}"]`));
                                if (row.nodes().length > 0) {
                                    row.remove().draw();
                                    // Mise à jour du compteur si la fonction existe
                                    if (typeof updateCount === 'function') updateCount();
                                } else {
                                    location.reload();
                                }
                            } else {
                                location.reload();
                            }
                        });
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message || 'Erreur lors de la suppression.';
                        window.showNotification(msg, 'error');
                    }
                });
            }
        });
    });

    // ═══════════════════════════════════════
    // 📦 ARCHIVER UN COURRIER (Optionnel)
    // ═══════════════════════════════════════
    
    $(document).off('click', '.btn-archiver').on('click', '.btn-archiver', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Archiver ce courrier ?',
            text: 'Il ne sera plus visible dans la liste active.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#64748b',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Oui, archiver',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(`/courriers/${id}/archiver`, { _token: window.CSRF_TOKEN })
                    .done(res => {
                        window.showNotification(res.message || 'Courrier archivé.', 'success')
                            .then(() => location.reload());
                    })
                    .fail(xhr => {
                        window.showNotification(xhr.responseJSON?.message || 'Erreur lors de l\'archivage', 'error');
                    });
            }
        });
    });

    // ═══════════════════════════════════════
    // 📤 EXPORT (Confirmation avant téléchargement)
    // ═══════════════════════════════════════
    
    $('#exportExcel, #exportPDF, #exportCSV').off('click').on('click', function(e) {
        e.preventDefault();
        
        const format = this.id.replace('export', '').toUpperCase();
        
        Swal.fire({
            title: `Exporter en ${format} ?`,
            text: 'Les filtres actifs seront appliqués à l\'export.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            confirmButtonText: `Télécharger ${format}`,
            cancelButtonText: 'Annuler'
        }).then(result => {
            if (result.isConfirmed) {
                window.showNotification(`Préparation de l'export ${format}...`, 'info');
                
                // Construction des paramètres
                const filters = {
                    search: $('#searchInput').val(),
                    type: $('.type-pill.active')?.data('type'),
                    statut: $('#filterStatut')?.val(),
                    priorite: $('#filterPriorite')?.val()
                };
                
                const params = new URLSearchParams({
                    format: format.toLowerCase(),
                    filters: JSON.stringify(filters)
                });
                
                // Lancement du téléchargement
                window.location.href = `/courriers/export?${params.toString()}`;
            }
        });
    });
}
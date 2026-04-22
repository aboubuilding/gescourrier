/**
 * services/actions.js
 * DataTable + Actions CRUD : Services
 * Version corrigée et sécurisée
 */

// ═══════════════════════════════════════
// 📊 DATATABLE
// ═══════════════════════════════════════

window.servicesTable = null;

function initServicesTable() {
    const $table = $('#servicesTable');
    
    // ✅ 1. Détruire l'instance existante pour éviter l'erreur _DT_CellIndex
    if ($.fn.DataTable.isDataTable($table)) {
        $table.DataTable().destroy();
    }
    
    // ✅ 2. Vérifier que le tableau existe dans le DOM
    if ($table.length === 0) {
        console.warn('Tableau #servicesTable non trouvé');
        return;
    }

    window.servicesTable = $table.DataTable({
        // ✅ Préserver l'ordre serveur
        order: [],
        
        // Désactiver le tri sur la colonne Actions (dernière)
        columnDefs: [
            { orderable: false, targets: -1, className: 'text-center' }
        ],
        
        responsive: true,
        pageLength: 15,
        lengthMenu: [[15, 30, 50, -1], [15, 30, 50, 'Tous']],
        
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
        },
        
        dom: '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
        
        initComplete: function() {
            updateServicesCount();
            
            // Recherche personnalisée
            $('#tableSearch').off('keyup').on('keyup', function() {
                if (window.servicesTable) {
                    window.servicesTable.search(this.value).draw();
                    updateServicesCount();
                }
            });
        },
        
        // Mise à jour du compteur à chaque redraw
        drawCallback: function() {
            updateServicesCount();
        }
    });
}

// ═══════════════════════════════════════
// 📊 COUNT
// ═══════════════════════════════════════

function updateServicesCount() {
    if (!window.servicesTable) return;
    
    const count = window.servicesTable
        .rows({ search: 'applied' })
        .data()
        .length;
    
    $('#servicesCount').text(count);
}

// ═══════════════════════════════════════
// 📋 UTILITAIRES
// ═══════════════════════════════════════

function getSelectedServiceIds() {
    const ids = [];
    $('.select-service:checked').each(function() {
        ids.push($(this).val());
    });
    return ids;
}

/**
 * Règle métier : suppression autorisée uniquement si aucun agent et aucun courrier
 */
function canDeleteService($row) {
    const agents = parseInt($row.data('agents') || 0);
    const courriers = parseInt($row.data('courriers') || 0);
    return agents === 0 && courriers === 0;
}

// ═══════════════════════════════════════
// ⚙️ ACTIONS
// ═══════════════════════════════════════

function initServiceActions() {
    
    // Vérifier que les éléments existent avant de binder
    if ($('#servicesTable').length === 0) {
        console.warn('Tableau services non trouvé, skip actions');
        return;
    }

    // ─────────────────────────────
    // 🗑️ DELETE (Simple)
    // ─────────────────────────────

    $(document)
        .off('click', '.btn-delete-service')
        .on('click', '.btn-delete-service', function() {
            
            const id = $(this).data('id');
            const nom = $(this).data('nom') || 'ce service';
            const $row = $(`tr[data-id="${id}"]`);
            
            if (!$row.length) {
                return showNotification("Ligne introuvable", "error");
            }
            
            const agents = parseInt($row.data('agents') || 0);
            const courriers = parseInt($row.data('courriers') || 0);
            
            // 🚨 Règle métier : blocage si lié
            if (agents > 0 || courriers > 0) {
                return showNotification(
                    `Impossible de supprimer : ${agents} agent(s) et/ou ${courriers} courrier(s) lié(s)`,
                    'warning',
                    { modal: true }
                );
            }
            
            Swal.fire({
                title: 'Supprimer ce service ?',
                html: `Vous allez supprimer <strong>${nom}</strong>.<br><small class="text-muted">Cette action est irréversible.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                backdrop: 'rgba(0,0,0,0.4)'
            }).then(result => {
                
                if (!result.isConfirmed) return;
                
                // Loading state
                Swal.fire({
                    title: 'Suppression...',
                    text: 'Veuillez patienter',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                
                // ✅ Correction : template literal avec slash
                $.ajax({
                    url: `${window.servicesConfig?.routes?.delete || '/services/'}${id}`,
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN },
                    
                    success: function(res) {
                        Swal.fire({
                            title: 'Supprimé !',
                            text: res.message || 'Service supprimé',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        // Suppression visuelle de la ligne
                        if (window.servicesTable) {
                            window.servicesTable
                                .row($row)
                                .remove()
                                .draw();
                            updateServicesCount();
                        } else {
                            location.reload();
                        }
                    },
                    
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message || 'Erreur lors de la suppression';
                        Swal.fire('Erreur', msg, 'error');
                    }
                });
            });
        });

    // ─────────────────────────────
    // 👁️ VIEW (Modal)
    // ─────────────────────────────

    $(document)
        .off('click', '.btn-view-service')
        .on('click', '.btn-view-service', function() {
            
            const id = $(this).data('id');
            const $modal = $('#modalShowService');
            const $loading = $('#showServiceLoading');
            const $content = $('#showServiceContent');
            
            // Reset du modal avant ouverture
            $content.hide();
            $loading.show();
            $modal.find('.is-invalid').removeClass('is-invalid');
            $modal.find('.field-error').remove();
            
            $modal.modal('show');
            
            $.ajax({
                url: `${window.servicesConfig?.routes?.show || '/services/'}${id}`,
                method: 'GET',
                headers: { 'Accept': 'application/json' },
                
                success: function(res) {
                    const s = res.data || res;
                    
                    if (!s) {
                        $loading.hide();
                        return showNotification('Données invalides', 'error');
                    }
                    
                    // Remplissage des champs
                    $('#showServiceNom').text(s.nom || '—');
                    $('#showServiceSigle').text(s.sigle ? `(${s.sigle})` : '');
                    $('#showServiceAgents').text(s.agents_count ?? s.total_agents ?? 0);
                    $('#showServiceCourriers').text(s.total_courriers ?? 0);
                    $('#showTopAgent').text(s.top_agent?.nom || '—');
                    
                    // Statut dynamique
                    const actif = (s.etat === 'actif') || (s.agents_count > 0) || (s.total_courriers > 0);
                    const $badge = $('#showServiceStatus');
                    $badge
                        .text(actif ? 'Actif' : 'Inactif')
                        .removeClass('bg-success bg-secondary text-white')
                        .addClass(actif ? 'bg-success' : 'bg-secondary')
                        .css('color', actif ? '#fff' : '#64748b');
                    
                    // Affichage
                    $loading.hide();
                    $content.fadeIn(200);
                },
                
                error: function(xhr) {
                    $loading.hide();
                    const msg = xhr.responseJSON?.message || 'Erreur de chargement';
                    showNotification(msg, 'error');
                    $modal.modal('hide');
                }
            });
        });
    
    // ✅ Reset du modal View à la fermeture
    $('#modalShowService').off('hidden.bs.modal').on('hidden.bs.modal', function() {
        $(this).find('#showServiceContent').hide();
        $(this).find('#showServiceLoading').show();
    });

    // ─────────────────────────────
    // 🔄 BULK DELETE
    // ─────────────────────────────

    $(document)
        .off('click', '#bulkDeleteServices')
        .on('click', '#bulkDeleteServices', function() {
            
            const ids = getSelectedServiceIds();
            
            if (!Array.isArray(ids) || ids.length === 0) {
                return showNotification('Aucun service sélectionné', 'info');
            }
            
            Swal.fire({
                title: `Supprimer ${ids.length} service(s) ?`,
                text: 'Cette action est irréversible.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then(result => {
                
                if (!result.isConfirmed) return;
                
                // Loading
                Swal.fire({
                    title: 'Suppression...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                
                $.ajax({
                    url: window.servicesConfig?.routes?.bulkDelete || '/services/bulk-delete',
                    method: 'POST',
                    data: {
                        _token: window.CSRF_TOKEN,
                        ids: ids,
                        _method: 'DELETE'
                    },
                    
                    success: function(res) {
                        showNotification(res.message || 'Services supprimés', 'success');
                        
                        // Recharger pour mettre à jour le tableau
                        if (window.servicesTable) {
                            location.reload();
                        }
                    },
                    
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message || 'Erreur lors de la suppression multiple';
                        Swal.fire('Erreur', msg, 'error');
                    }
                });
            });
        });
}
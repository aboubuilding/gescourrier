/**
 * organisations/actions.js
 * DataTable + Actions CRUD : Supprimer, Désactiver, Réactiver, Bulk, Export
 */

// ═══════════════════════════════════════
// 📊 DATATABLE
// ═══════════════════════════════════════

/**
 * Initialisation du DataTable Organisations
 * Préserve l'ordre trié envoyé par le contrôleur Laravel
 */
window.organisationsTable = null;

function initOrganisationsTable() {
    window.organisationsTable = $('#organisationsTable').DataTable({
        // ✅ IMPORTANT : order: [] préserve le tri serveur (pas de re-tri client)
        order: [],
        
        // Désactiver le tri sur la colonne Actions (dernière colonne)
        columnDefs: [
            { orderable: false, targets: -1 }
        ],
        
        // Configuration de base
        responsive: true,
        language: { 
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json' 
        },
        pageLength: 15,
        lengthMenu: [[15, 30, 50, -1], [15, 30, 50, 'Tous']],
        
        // Layout du tableau
        dom: '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
        
        // Recherche personnalisée via l'attribut data-search
        initComplete: function() {
            // Mise à jour du compteur initial
            updateOrganisationsCount();
            
            // Liaison du champ de recherche personnalisé
            $('#tableSearch').on('keyup', function() {
                window.organisationsTable.search(this.value).draw();
                updateOrganisationsCount();
            });
        }
    });
}

/**
 * Met à jour le compteur d'organisations affichées
 */
function updateOrganisationsCount() {
    const count = window.organisationsTable?.rows({ search: 'applied' }).count() ?? 0;
    $('#organisationsCount').text(count);
}

// ═══════════════════════════════════════
// 📋 UTILITAIRES GLOBAUX
// ═══════════════════════════════════════

function updateOrganisationCount() {
    if (window.organisationTable) {
        const count = window.organisationTable.rows({ search: 'applied' }).count();
        $('#organisationsCount').text(count);
    }
}

function getSelectedOrganisationIds() {
    const ids = [];
    $('.select-organisation:checked').each(function () {
        ids.push($(this).val());
    });
    return ids;
}

function updateOrganisationStatus(id, status) {
    const $row   = $(`tr[data-id="${id}"]`);
    const $badge = $row.find('.status-badge');

    if (status === 'active') {
        $badge.removeClass('bg-secondary').addClass('bg-success')
              .html('<i class="fas fa-check-circle me-1"></i> Active');
        $row.find('.btn-disable-organisation').show();
        $row.find('.btn-enable-organisation').hide();
    } else {
        $badge.removeClass('bg-success').addClass('bg-secondary')
              .html('<i class="fas fa-ban me-1"></i> Inactive');
        $row.find('.btn-disable-organisation').hide();
        $row.find('.btn-enable-organisation').show();
    }
}

// ═══════════════════════════════════════
// ⚙️ INIT PRINCIPALE
// ═══════════════════════════════════════

function initOrganisationActions() {

    // ─────────────────────────────────────
    // 🗑️ SUPPRIMER
    // ─────────────────────────────────────

    $(document).off('click', '.btn-delete-organisation').on('click', '.btn-delete-organisation', function () {
        const id           = $(this).data('id');
        const nom          = $(this).data('nom') || 'cette organisation';
        const hasCourriers = $(this).data('has-courriers') === true;

        const warningText = hasCourriers
            ? 'Attention : Cette organisation a des courriers associés. La suppression est irréversible.'
            : 'Cette action est irréversible.';

        Swal.fire({
            title: 'Supprimer cette organisation ?',
            html: `Vous allez supprimer <strong>${nom}</strong>.<br>${warningText}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            background: '#fff',
            backdrop: 'rgba(0,0,0,0.4)'
        }).then((result) => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Suppression en cours...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: `${window.organisationsConfig.routes.delete}${id}`,
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN },
                success: function (res) {
                    Swal.fire({
                        title: 'Supprimée !',
                        text: res.message || 'Organisation supprimée avec succès.',
                        icon: 'success',
                        confirmButtonColor: '#10b981',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        if (window.organisationTable) {
                            const row = window.organisationTable.row($(`tr[data-id="${id}"]`));
                            if (row.nodes().length > 0) {
                                row.remove().draw();
                                updateOrganisationCount();
                            } else {
                                location.reload();
                            }
                        } else {
                            location.reload();
                        }
                    });
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || 'Erreur lors de la suppression.';
                    Swal.fire({ title: 'Erreur !', text: msg, icon: 'error', confirmButtonColor: '#ef4444' });
                }
            });
        });
    });

    // ─────────────────────────────────────
    // 🔒 DÉSACTIVER
    // ─────────────────────────────────────

    $(document).off('click', '.btn-disable-organisation').on('click', '.btn-disable-organisation', function () {
        const id  = $(this).data('id');
        const nom = $(this).data('nom') || 'cette organisation';

        Swal.fire({
            title: 'Désactiver cette organisation ?',
            html: `Vous allez désactiver <strong>${nom}</strong>.<br>Les courriers associés ne seront plus affectables.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Oui, désactiver',
            cancelButtonText: 'Annuler',
            background: '#fff',
            backdrop: 'rgba(0,0,0,0.4)'
        }).then((result) => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Désactivation en cours...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: `${window.organisationsConfig.routes.disable}${id}/disable`,
                method: 'POST',
                data: { _token: window.CSRF_TOKEN, _method: 'PUT' },
                headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN },
                success: function (res) {
                    Swal.fire({
                        title: 'Désactivée !',
                        text: res.message || 'Organisation désactivée avec succès.',
                        icon: 'success',
                        confirmButtonColor: '#10b981',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        updateOrganisationStatus(id, 'inactive');
                        location.reload();
                    });
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || 'Erreur lors de la désactivation.';
                    Swal.fire({ title: 'Erreur !', text: msg, icon: 'error', confirmButtonColor: '#ef4444' });
                }
            });
        });
    });

    // ─────────────────────────────────────
    // 🔓 RÉACTIVER
    // ─────────────────────────────────────

    $(document).off('click', '.btn-enable-organisation').on('click', '.btn-enable-organisation', function () {
        const id  = $(this).data('id');
        const nom = $(this).data('nom') || 'cette organisation';

        Swal.fire({
            title: 'Réactiver cette organisation ?',
            html: `Vous allez réactiver <strong>${nom}</strong>.<br>Les courriers pourront à nouveau lui être affectés.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Oui, réactiver',
            cancelButtonText: 'Annuler',
            background: '#fff',
            backdrop: 'rgba(0,0,0,0.4)'
        }).then((result) => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Réactivation en cours...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: `${window.organisationsConfig.routes.enable}${id}/enable`,
                method: 'POST',
                data: { _token: window.CSRF_TOKEN, _method: 'PUT' },
                headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN },
                success: function (res) {
                    Swal.fire({
                        title: 'Réactivée !',
                        text: res.message || 'Organisation réactivée avec succès.',
                        icon: 'success',
                        confirmButtonColor: '#10b981',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        updateOrganisationStatus(id, 'active');
                        location.reload();
                    });
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || 'Erreur lors de la réactivation.';
                    Swal.fire({ title: 'Erreur !', text: msg, icon: 'error', confirmButtonColor: '#ef4444' });
                }
            });
        });
    });

    // ─────────────────────────────────────
    // 🔄 SUPPRESSION MULTIPLE
    // ─────────────────────────────────────

    $(document).off('click', '#bulkDeleteOrganisations').on('click', '#bulkDeleteOrganisations', function () {
        const selectedIds = getSelectedOrganisationIds();

        if (!selectedIds.length) {
            Swal.fire({
                title: 'Aucune sélection',
                text: 'Veuillez sélectionner au moins une organisation.',
                icon: 'info',
                confirmButtonColor: '#64748b'
            });
            return;
        }

        Swal.fire({
            title: 'Supprimer plusieurs organisations ?',
            html: `Vous allez supprimer <strong>${selectedIds.length} organisation(s)</strong>.<br>Cette action est irréversible.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Oui, tout supprimer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Suppression en cours...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: window.organisationsConfig.routes.bulkDelete,
                method: 'POST',
                data: { _token: window.CSRF_TOKEN, ids: selectedIds, _method: 'DELETE' },
                success: function (res) {
                    Swal.fire({
                        title: 'Supprimées !',
                        text: res.message || `${selectedIds.length} organisation(s) supprimée(s).`,
                        icon: 'success',
                        confirmButtonColor: '#10b981',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => location.reload());
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || 'Erreur lors de la suppression multiple.';
                    Swal.fire({ title: 'Erreur !', text: msg, icon: 'error', confirmButtonColor: '#ef4444' });
                }
            });
        });
    });

    // ─────────────────────────────────────
    // 🔄 DÉSACTIVATION MULTIPLE
    // ─────────────────────────────────────

    $(document).off('click', '#bulkDisableOrganisations').on('click', '#bulkDisableOrganisations', function () {
        const selectedIds = getSelectedOrganisationIds();

        if (!selectedIds.length) {
            Swal.fire({
                title: 'Aucune sélection',
                text: 'Veuillez sélectionner au moins une organisation.',
                icon: 'info',
                confirmButtonColor: '#64748b'
            });
            return;
        }

        Swal.fire({
            title: 'Désactiver plusieurs organisations ?',
            html: `Vous allez désactiver <strong>${selectedIds.length} organisation(s)</strong>.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Oui, désactiver',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Désactivation en cours...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: window.organisationsConfig.routes.bulkDisable,
                method: 'POST',
                data: { _token: window.CSRF_TOKEN, ids: selectedIds },
                success: function (res) {
                    Swal.fire({
                        title: 'Désactivées !',
                        text: res.message || `${selectedIds.length} organisation(s) désactivée(s).`,
                        icon: 'success',
                        confirmButtonColor: '#10b981',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => location.reload());
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || 'Erreur lors de la désactivation multiple.';
                    Swal.fire({ title: 'Erreur !', text: msg, icon: 'error', confirmButtonColor: '#ef4444' });
                }
            });
        });
    });

    // ─────────────────────────────────────
    // 📊 EXPORT
    // ─────────────────────────────────────

    $(document).off('click', '#exportOrganisationsExcel, #exportOrganisationsPDF, #exportOrganisationsCSV')
               .on('click',  '#exportOrganisationsExcel, #exportOrganisationsPDF, #exportOrganisationsCSV', function (e) {
        e.preventDefault();

        const format = this.id.replace('exportOrganisations', '').toUpperCase();

        Swal.fire({
            title: `Exporter en ${format} ?`,
            text: "Toutes les organisations seront incluses dans l'export.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            confirmButtonText: `Télécharger ${format}`,
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Export en cours...',
                text: 'Préparation du fichier...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            window.location.href = `${window.organisationsConfig.routes.export}?format=${format.toLowerCase()}`;
            setTimeout(() => Swal.close(), 2000);
        });
    });
}
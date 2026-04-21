/**
 * 🎨 Services Index — JavaScript spécifique
 * 
 * Fonctionnalités :
 * - DataTables avec filtres personnalisés (organisation, état, recherche)
 * - CRUD AJAX (Create, Edit, Delete, Suspend/Restore)
 * - Export Excel/PDF/CSV avec filtres appliqués
 * - Toast notifications & SweetAlert confirmations
 * - Select2 dans les modals Bootstrap
 * 
 * @version 1.0.0
 * @requires jQuery, DataTables, Toastr, SweetAlert2, Select2
 * @author Équipe Courriers Officiels
 */

$(document).ready(function() {
    
    // ═══════════════════════════════════════
    // ⚙️ CONFIG & UTILS
    // ═══════════════════════════════════════
    
    // CSRF Token avec fallbacks multiples
    const getCSRF = () => {
        return $('meta[name="csrf-token"]').attr('content') || 
               document.querySelector('meta[name="csrf-token"]')?.content || 
               '';
    };
    const CSRF = getCSRF();
    
    // Configuration Toastr
    if (typeof toastr !== 'undefined') {
        toastr.options = {
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 3500,
            closeButton: true,
            preventDuplicates: true
        };
    }
    
    // Helper: Afficher un toast (fallback si toastr non chargé)
    const showToast = (message, type = 'info') => {
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
        } else {
            console.log(`[${type.toUpperCase()}] ${message}`);
        }
    };
    
    // Helper: Désactiver/activer un bouton avec spinner
    const toggleButton = ($btn, $spinner, loading) => {
        $btn.prop('disabled', loading);
        if ($spinner) $spinner.toggleClass('d-none', !loading);
    };
    
    // Helper: Initialiser Select2 dans un modal
    const initSelect2InModal = ($modal) => {
        if (typeof $.fn.select2 === 'undefined') return;
        $modal.find('select.select2').each(function() {
            const $el = $(this);
            if (!$el.data('select2')) {
                $el.select2({
                    width: '100%',
                    dropdownParent: $modal,
                    language: 'fr'
                });
            }
        });
    };

    // ═══════════════════════════════════════
    // 📊 DATATABLES INIT
    // ═══════════════════════════════════════
    
    const table = $('#servicesTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
        },
        pageLength: 15,
        lengthMenu: [[15, 30, 50, -1], [15, 30, 50, 'Tous']],
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: -1 } // Actions column
        ],
        dom: '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn-success btn-sm',
                exportOptions: { columns: [0,1,2,3] }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn-danger btn-sm',
                exportOptions: { columns: [0,1,2,3] }
            },
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn-primary btn-sm',
                exportOptions: { columns: [0,1,2,3] }
            }
        ],
        initComplete: function() {
            // Initialiser Select2 après DataTables
            initSelect2InModal($('#modalCreate, #modalEdit'));
        }
    });
    
    // ═══════════════════════════════════════
    // 🔍 FILTRES PERSONNALISÉS
    // ═══════════════════════════════════════
    
    function applyFilters() {
        const search = $('#searchInput').val().toLowerCase();
        const organisation = $('#filterOrganisation').val();
        const etat = $('#filterEtat').val();
        
        // Réinitialiser les filtres personnalisés
        $.fn.dataTable.ext.search = [];
        
        // Ajouter nos filtres
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            const row = $(table.row(dataIndex).node());
            const matchOrg = !organisation || String(row.data('organisation')) === String(organisation);
            const matchEtat = !etat || String(row.data('etat')) === String(etat);
            return matchOrg && matchEtat;
        });
        
        // Appliquer la recherche texte + redraw
        table.search(search).draw();
        updateCount();
    }
    
    function updateCount() {
        const count = table.rows({ search: 'applied' }).count();
        $('#tableCount').text(`${count} service${count !== 1 ? 's' : ''}`);
    }
    
    // Event listeners pour les filtres
    $('#searchInput').on('keyup', applyFilters);
    $('#filterOrganisation, #filterEtat').on('change', applyFilters);
    
    $('#btnResetFilters').on('click', function() {
        $('#searchInput').val('');
        $('#filterOrganisation, #filterEtat').val('');
        $.fn.dataTable.ext.search = [];
        table.search('').columns().search('').draw();
        updateCount();
    });

    // ═══════════════════════════════════════
    // ➕ CRÉATION AJAX
    // ═══════════════════════════════════════
    
    $('#formCreate').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $btn = $('#btnSubmitCreate');
        const $spinner = $('#spinnerCreate');
        
        toggleButton($btn, $spinner, true);
        
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            headers: { 'X-CSRF-TOKEN': CSRF },
            success: function(res) {
                showToast(res.message || 'Service créé avec succès', 'success');
                $('#modalCreate').modal('hide');
                setTimeout(() => location.reload(), 800);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.values(errors).flat().forEach(msg => showToast(msg, 'error'));
                } else {
                    showToast(xhr.responseJSON?.message || 'Erreur lors de la création', 'error');
                }
            },
            complete: function() {
                toggleButton($btn, $spinner, false);
            }
        });
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
        
        $.get(`/services/${id}/edit`, function(data) {
            // Remplir les champs du modal
            $('#editId').val(data.id);
            $('#editNom').val(data.nom);
            
            // Select2 : mettre à jour + trigger change pour refresh UI
            if (data.organisation_id) {
                $('#editOrganisation').val(data.organisation_id).trigger('change');
            }
            
            $('#formEdit').attr('action', `/services/${data.id}`);
            
            // Afficher le modal + réinitialiser Select2
            $('#modalEdit').modal('show');
            initSelect2InModal($('#modalEdit'));
        })
        .fail(function() {
            showToast('Impossible de charger les données du service', 'error');
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
                showToast(res.message || 'Service mis à jour', 'success');
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
    // 👥 VOIR LES AGENTS D'UN SERVICE
    // ═══════════════════════════════════════
    
    $(document).on('click', '.btn-agents', function() {
        const id = $(this).data('id');
        window.location.href = `/agents?service_id=${id}`;
    });

    // ═══════════════════════════════════════
    // ⏸️ SUSPENDRE / ▶️ RÉACTIVER
    // ═══════════════════════════════════════
    
    $(document).on('click', '.btn-suspend', function() {
        const id = $(this).data('id');
        
        if (typeof Swal === 'undefined') {
            if (confirm('Désactiver ce service ? Il sera masqué des listes.')) {
                suspendService(id);
            }
            return;
        }
        
        Swal.fire({
            title: 'Désactiver ce service ?',
            text: 'Les agents liés resteront accessibles mais le service sera masqué des listes.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'var(--text-muted, #64748b)',
            cancelButtonColor: 'var(--border, #e2e8f0)',
            confirmButtonText: 'Désactiver',
            cancelButtonText: 'Annuler',
            background: 'var(--bg-card, #fff)',
            color: 'var(--text-primary, #1e293b)'
        }).then(result => {
            if (result.isConfirmed) {
                suspendService(id);
            }
        });
    });
    
    function suspendService(id) {
        $.ajax({
            url: `/services/${id}`,
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF },
            success: function(res) {
                showToast(res.message || 'Service désactivé', 'success');
                setTimeout(() => location.reload(), 800);
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Erreur lors de la désactivation', 'error');
            }
        });
    }
    
    $(document).on('click', '.btn-restore', function() {
        const id = $(this).data('id');
        
        if (typeof Swal === 'undefined') {
            if (confirm('Réactiver ce service ?')) {
                restoreService(id);
            }
            return;
        }
        
        Swal.fire({
            title: 'Réactiver ce service ?',
            text: 'Le service réapparaîtra dans les listes et sélections.',
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
                restoreService(id);
            }
        });
    });
    
    function restoreService(id) {
        $.post(`/services/${id}/restaurer`, { _token: CSRF })
            .done(function(res) {
                showToast(res.message || 'Service réactivé', 'success');
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
            organisation: $('#filterOrganisation').val(),
            etat: $('#filterEtat').val()
        };
        
        // Feedback utilisateur
        showToast(`Préparation de l'export ${format.toUpperCase()}...`, 'info');
        
        // Redirection vers l'URL d'export
        const params = new URLSearchParams({
            format: format,
            filters: JSON.stringify(filters)
        });
        window.location.href = `/services/export?${params.toString()}`;
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
    $('#modalCreate, #modalEdit').on('shown.bs.modal', function() {
        initSelect2InModal($(this));
    });
    
    // Nettoyer Select2 à la fermeture pour éviter les fuites mémoire
    $('#modalCreate, #modalEdit').on('hidden.bs.modal', function() {
        $(this).find('select.select2').select2('destroy');
    });

    // ═══════════════════════════════════════
    // 🔔 FLASH MESSAGES (via data attributes)
    // ═══════════════════════════════════════
    
    // Les messages flash sont passés via data attributes sur le body
    // Ex: <body data-flash-success="Message" data-flash-error="Erreur">
    const $body = $('body');
    const flashSuccess = $body.data('flash-success');
    const flashError = $body.data('flash-error');
    
    if (flashSuccess) showToast(flashSuccess, 'success');
    if (flashError) showToast(flashError, 'error');

    // ═══════════════════════════════════════
    // ⌨️ RACCOURCIS CLAVIER (optionnel)
    // ═══════════════════════════════════════
    
    $(document).on('keydown', function(e) {
        // Ctrl/Cmd + N : Nouveau service
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'n') {
            // Seulement si on n'est pas dans un input
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
    // 🎯 INIT : Message de bienvenue (première visite)
    // ═══════════════════════════════════════
    
    if (!sessionStorage.getItem('services_welcome') && typeof toastr !== 'undefined') {
        setTimeout(() => {
            showToast('💡 Astuce : Utilisez les filtres pour trouver rapidement un service', 'info');
            sessionStorage.setItem('services_welcome', 'true');
        }, 2000);
    }
    
}); // ← FIN DU $(document).ready()
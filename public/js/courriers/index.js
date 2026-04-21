/**
 * 🎨 Courriers Index — JavaScript spécifique
 * 
 * Fonctionnalités :
 * - DataTables avec filtres personnalisés
 * - CRUD AJAX (Create, Edit, Delete, Archive, Affect)
 * - Upload drag & drop
 * - Export Excel/PDF/CSV
 * - Toast notifications & SweetAlert confirmations
 * 
 * @version 1.0.0
 * @requires jQuery, DataTables, Toastr, SweetAlert2, Select2
 * @author Équipe Courriers Officiels
 */

$(document).ready(function() {
    
    // ═══════════════════════════════════════
    // ⚙️ CONFIG & UTILS
    // ═══════════════════════════════════════
    
    // CSRF Token avec fallback sécurisé
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
        $spinner?.toggleClass('d-none', !loading);
    };

    // ═══════════════════════════════════════
    // 📊 DATATABLES INIT
    // ═══════════════════════════════════════
    
    const table = $('#courriersTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
        },
        pageLength: 15,
        lengthMenu: [[15, 30, 50, -1], [15, 30, 50, 'Tous']],
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: -1 } // Actions column
        ],
        dom: '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn-success btn-sm',
                exportOptions: { columns: [0,1,2,3,4,5] }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn-danger btn-sm',
                exportOptions: { columns: [0,1,2,3,4,5] }
            },
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn-primary btn-sm',
                exportOptions: { columns: [0,1,2,3,4,5] }
            }
        ],
        initComplete: function() {
            // Initialiser Select2 dans les modals après DataTables
            initSelect2InModals();
        }
    });
    
    // ═══════════════════════════════════════
    // 🔍 FILTRES PERSONNALISÉS
    // ═══════════════════════════════════════
    
    function applyFilters() {
        const search = $('#searchInput').val().toLowerCase();
        const type = $('.type-pill.active').data('type');
        const statut = $('#filterStatut').val();
        const priorite = $('#filterPriorite').val();
        
        // Réinitialiser les filtres personnalisés
        $.fn.dataTable.ext.search = [];
        
        // Ajouter nos filtres
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            const row = $(table.row(dataIndex).node());
            const matchType = !type || String(row.data('type')) === String(type);
            const matchStatut = !statut || String(row.data('statut')) === String(statut);
            const matchPriorite = !priorite || String(row.data('priorite')) === String(priorite);
            return matchType && matchStatut && matchPriorite;
        });
        
        // Appliquer la recherche texte + redraw
        table.search(search).draw();
        updateCount();
    }
    
    function updateCount() {
        const count = table.rows({ search: 'applied' }).count();
        $('#tableCount').text(`${count} courrier${count !== 1 ? 's' : ''}`);
    }
    
    // Event listeners pour les filtres
    $('#searchInput').on('keyup', applyFilters);
    
    $('.type-pill').on('click', function() {
        $('.type-pill').removeClass('active');
        $(this).addClass('active');
        applyFilters();
    });
    
    $('#filterStatut, #filterPriorite').on('change', applyFilters);
    
    $('#btnResetFilters').on('click', function() {
        $('#searchInput').val('');
        $('.type-pill').removeClass('active').first().addClass('active');
        $('#filterStatut, #filterPriorite').val('');
        $.fn.dataTable.ext.search = [];
        table.search('').columns().search('').draw();
        updateCount();
    });

    // ═══════════════════════════════════════
    // 📁 UPLOAD DRAG & DROP
    // ═══════════════════════════════════════
    
    const $fileDrop = $('#fileDrop');
    const $fileInput = $('#fileInput');
    const $fileName = $('#fileName');
    
    if ($fileDrop.length && $fileInput.length) {
        $fileDrop.on('click', () => $fileInput.click());
        
        $fileInput.on('change', function() {
            if (this.files?.[0]) {
                $fileName.text(this.files[0].name);
                $fileDrop.addClass('dragover');
            }
        });
        
        $fileDrop.on('dragover dragenter', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).addClass('dragover');
        });
        
        $fileDrop.on('dragleave dragend drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('dragover');
        });
        
        $fileDrop.on('drop', function(e) {
            const files = e.originalEvent?.dataTransfer?.files;
            if (files?.[0] && $fileInput[0]) {
                $fileInput[0].files = files;
                $fileName.text(files[0].name);
                // Déclencher l'event change pour les listeners
                $fileInput.trigger('change');
            }
        });
    }

    // ═══════════════════════════════════════
    // ➕ CRÉATION AJAX
    // ═══════════════════════════════════════
    
    $('#formCreate').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $btn = $('#btnSubmitCreate');
        const $spinner = $('#spinnerCreate');
        
        toggleButton($btn, $spinner, true);
        
        const formData = new FormData($form[0]);
        
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': CSRF },
            success: function(res) {
                showToast(res.message || 'Courrier créé avec succès', 'success');
                $('#modalCreate').modal('hide');
                // Recharger après un court délai pour UX
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
        const originalIcon = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
        
        $.get(`/courriers/${id}/edit`, function(data) {
            // Remplir les champs du modal
            $('#editId').val(data.id);
            $('#editType').val(data.type);
            $('#editPriorite').val(data.priorite);
            $('#editReference').val(data.reference);
            $('#editNumero').val(data.numero);
            $('#editObjet').val(data.objet);
            $('#editDescription').val(data.description);
            $('#editExpediteur').val(data.expediteur);
            $('#editDestinataire').val(data.destinataire);
            $('#editDateReception').val(data.date_reception);
            $('#editDateEnvoi').val(data.date_envoi);
            
            // Select2 : mettre à jour + trigger change pour refresh UI
            if (data.service_id) {
                $('#editService').val(data.service_id).trigger('change');
            }
            if (data.organisation_id) {
                $('#editOrganisation').val(data.organisation_id).trigger('change');
            }
            
            $('#editFileCurrent').text(data.fichier_nom_original || 'Aucun fichier');
            $('#formEdit').attr('action', `/courriers/${data.id}`);
            
            // Afficher le modal + réinitialiser Select2
            $('#modalEdit').modal('show');
            initSelect2InModals();
        })
        .fail(function() {
            showToast('Impossible de charger les données du courrier', 'error');
        })
        .always(function() {
            // Restaurer le bouton
            $btn.html(originalIcon).prop('disabled', false);
        });
    });
    
    // ✏️ MODIFICATION : Soumettre le formulaire
    $('#formEdit').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $btn = $('#btnSubmitEdit');
        const $spinner = $('#spinnerEdit');
        
        toggleButton($btn, $spinner, true);
        
        const formData = new FormData($form[0]);
        
        $.ajax({
            url: $form.attr('action'),
            method: 'POST', // Laravel accepte POST avec @method('PUT')
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': CSRF },
            success: function(res) {
                showToast(res.message || 'Courrier mis à jour', 'success');
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
    // 📤 AFFECTATION
    // ═══════════════════════════════════════
    
    $(document).on('click', '.btn-affecter', function() {
        const id = $(this).data('id');
        $('#affecterCourrierId').val(id);
        
        // Réinitialiser le formulaire avant ouverture
        $('#formAffecter')[0]?.reset();
        if (typeof $.fn.select2 !== 'undefined') {
            $('#formAffecter select.select2').trigger('change');
        }
        
        $('#modalAffecter').modal('show');
    });
    
    $('#formAffecter').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $btn = $form.find('button[type="submit"]');
        const $spinner = $('#spinnerAffecter');
        const id = $('#affecterCourrierId').val();
        
        toggleButton($btn, $spinner, true);
        
        $.ajax({
            url: `/courriers/${id}/affecter`,
            method: 'POST',
            data: $form.serialize(),
            headers: { 'X-CSRF-TOKEN': CSRF },
            success: function(res) {
                showToast(res.message || 'Affectation réussie', 'success');
                $('#modalAffecter').modal('hide');
                setTimeout(() => location.reload(), 800);
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Erreur lors de l\'affectation', 'error');
            },
            complete: function() {
                toggleButton($btn, $spinner, false);
            }
        });
    });

    // ═══════════════════════════════════════
    // 📦 ARCHIVAGE (SweetAlert)
    // ═══════════════════════════════════════
    
    $(document).on('click', '.btn-archiver', function() {
        const id = $(this).data('id');
        
        if (typeof Swal === 'undefined') {
            // Fallback si SweetAlert2 non chargé
            if (confirm('Archiver ce courrier ? Cette action est irréversible.')) {
                archiveCourrier(id);
            }
            return;
        }
        
        Swal.fire({
            title: 'Archiver ce courrier ?',
            text: 'Cette action est irréversible.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'var(--text-muted, #64748b)',
            cancelButtonColor: 'var(--border, #e2e8f0)',
            confirmButtonText: 'Archiver',
            cancelButtonText: 'Annuler',
            background: 'var(--bg-card, #fff)',
            color: 'var(--text-primary, #1e293b)'
        }).then(result => {
            if (result.isConfirmed) {
                archiveCourrier(id);
            }
        });
    });
    
    function archiveCourrier(id) {
        $.post(`/courriers/${id}/archiver`, { _token: CSRF })
            .done(function(res) {
                showToast(res.message || 'Courrier archivé', 'success');
                setTimeout(() => location.reload(), 800);
            })
            .fail(function(xhr) {
                showToast(xhr.responseJSON?.message || 'Erreur lors de l\'archivage', 'error');
            });
    }

    // ═══════════════════════════════════════
    // 🗑️ SUPPRESSION (SweetAlert)
    // ═══════════════════════════════════════
    
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        
        if (typeof Swal === 'undefined') {
            if (confirm('Supprimer définitivement ? Cette action est irréversible.')) {
                deleteCourrier(id);
            }
            return;
        }
        
        Swal.fire({
            title: 'Supprimer définitivement ?',
            text: 'Cette action est irréversible.',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: 'var(--danger, #ef4444)',
            cancelButtonColor: 'var(--border, #e2e8f0)',
            confirmButtonText: 'Supprimer',
            cancelButtonText: 'Annuler',
            background: 'var(--bg-card, #fff)',
            color: 'var(--text-primary, #1e293b)'
        }).then(result => {
            if (result.isConfirmed) {
                deleteCourrier(id);
            }
        });
    });
    
    function deleteCourrier(id) {
        $.ajax({
            url: `/courriers/${id}`,
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF },
            success: function(res) {
                showToast(res.message || 'Courrier supprimé', 'success');
                // Supprimer la ligne du tableau sans recharger toute la page
                table.row($(`tr[data-id="${id}"]`)).remove().draw();
                updateCount();
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Erreur lors de la suppression', 'error');
            }
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
            type: $('.type-pill.active').data('type'),
            statut: $('#filterStatut').val(),
            priorite: $('#filterPriorite').val()
        };
        
        // Feedback utilisateur
        showToast(`Préparation de l'export ${format.toUpperCase()}...`, 'info');
        
        // Redirection vers l'URL d'export
        const params = new URLSearchParams({
            format: format,
            filters: JSON.stringify(filters)
        });
        window.location.href = `/courriers/export?${params.toString()}`;
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
    
    function initSelect2InModals() {
        if (typeof $.fn.select2 === 'undefined') return;
        
        // Initialiser uniquement les select2 non encore initialisés dans les modals ouverts
        $('.modal.show select.select2').each(function() {
            const $el = $(this);
            if (!$el.data('select2')) {
                $el.select2({
                    width: '100%',
                    dropdownParent: $el.closest('.modal'),
                    language: 'fr'
                });
            }
        });
    }
    
    // Réinitialiser Select2 à l'ouverture de chaque modal
    $('#modalCreate, #modalEdit, #modalAffecter').on('shown.bs.modal', function() {
        initSelect2InModals();
    });
    
    // Nettoyer Select2 à la fermeture pour éviter les fuites mémoire
    $('#modalCreate, #modalEdit, #modalAffecter').on('hidden.bs.modal', function() {
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
        // Ctrl/Cmd + N : Nouveau courrier
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
    
    if (!sessionStorage.getItem('courriers_welcome') && typeof toastr !== 'undefined') {
        setTimeout(() => {
            showToast('💡 Astuce : Utilisez les filtres pour trouver rapidement un courrier', 'info');
            sessionStorage.setItem('courriers_welcome', 'true');
        }, 2000);
    }
    
}); // ← FIN DU $(document).ready()
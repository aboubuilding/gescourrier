/**
 * Gestion du Modal "Voir le Dossier" (Show) - Sécurisé & Corrigé
 */

function initShowModal() {
    
    // Gestion du clic sur "Voir le dossier"
    $(document).off('click', '.btn-view-dossier').on('click', '.btn-view-dossier', function() {
        const id = $(this).data('id');
        if (id) openShowModal(id);
    });

    // Bouton Imprimer (dans le modal)
    $('#btnPrintShow').off('click').on('click', function() {
        window.print();
    });
}

// Fonction privée pour ouvrir et remplir le modal
function openShowModal(id) {
    const $modal = $('#modalShow');
    const $loading = $('#showLoading');
    const $content = $('#showContent');
    
    // Reset UI & Show Loading
    $content.addClass('d-none');
    $loading.removeClass('d-none');
    $modal.modal('show');

    // Appel AJAX
    // Note: Vérifie bien l'URL. Si ta route est 'courriers.show', l'URL est souvent /courriers/{id}
    $.get(`/courriers/show/${id}`, function(response) {
        
        // --- CORRECTION MAJEURE ICI ---
        // On extrait les données réelles de la réponse JSON standardisée
        // Si ta réponse est { success: true, data: {...} }, on prend response.data
        // Sinon, on prend response tout court si c'est direct.
        const c = response.data || response; 
        
        if (!c) {
            console.error("Aucune donnée reçue", response);
            $loading.addClass('d-none');
            $content.html('<div class="text-center text-danger">Erreur: Données vides</div>').removeClass('d-none');
            return;
        }

        // --- SÉCURISATION DES DONNÉES ---
        const typeCode = c.type?.code ?? c.type ?? '0'; 
        const statutCode = c.statut?.code ?? c.statut ?? 0;
        const statutLibelle = c.statut?.libelle || 'Inconnu';
        const typeLibelle = c.type?.libelle || '—';
        
        // Attention à la structure de 'acteurs'. 
        // Si ton formatCourrier renvoie c.acteurs.organisation.nom, c'est bon.
        // Sinon adapte selon ta structure réelle (ex: c.organisation.nom)
        const orgNom = c.acteurs?.organisation?.nom || c.organisation?.nom || '—';
        const serviceNom = c.acteurs?.service?.nom || c.service?.nom || 'Non assigné';
        const agentNom = c.acteurs?.agent?.nom_complet || c.agent?.nom_complet || 'En attente';
        
        const dateRec = c.dates?.reception;
        const dateEnv = c.dates?.envoi;
        const dateStr = dateRec || dateEnv || 'N/A';

        // 1. Header Dynamique (Couleur selon Type)
        const headerColors = {
            '0': 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)', // Entrant (Bleu)
            '1': 'linear-gradient(135deg, #10b981 0%, #059669 100%)', // Sortant (Vert)
            '2': 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)'  // Interne (Orange)
        };
        const color = headerColors[String(typeCode)] || headerColors['0'];
        $('#modalShowHeader').css('background', color);

        // 2. Remplissage des champs
        $('#showReference').text(c.reference || 'Sans référence');
        $('#showNumero').text(c.numero ? `N° ${c.numero}` : 'N° —');
        $('#showObjet').text(c.objet || '—');
        $('#showDescription').html(c.description ? c.description.replace(/\n/g, '<br>') : '<span class="text-muted fst-italic">Aucune description détaillée.</span>');
        
        // Dates
        $('#showDate').html(`<i class="far fa-calendar-alt me-1"></i> ${dateStr}`);

        // Badges & Types
        $('#showStatutBadge')
            .text(statutLibelle)
            .removeClass()
            .addClass('badge rounded-pill px-3 py-2 mb-1 text-white')
            .css('background-color', getStatusColor(statutCode));

        $('#showType').html(`<span class="badge bg-secondary bg-opacity-10 text-secondary">${typeLibelle}</span>`);
        
        // Priorité (Sécurisée)
        const prioLibelle = c.priorite?.libelle || 'Normale';
        const isUrgent = prioLibelle.includes('Urgente');
        const prioClass = isUrgent ? 'bg-danger bg-opacity-10 text-danger' : 'bg-success bg-opacity-10 text-success';
        $('#showPriorite').html(`<span class="badge ${prioClass}">${prioLibelle}</span>`);

        // Acteurs
        $('#showOrganisation').text(orgNom);
        $('#showService').text(serviceNom);
        $('#showAgent').text(agentNom);

        // Fichier (Sécurisé)
        if (c.fichier && c.fichier.url) {
            $('#showFileLink').attr('href', c.fichier.url);
            $('#showFileName').text(c.fichier.nom_original || 'Document joint');
            $('#showFileSize').text(c.fichier.taille_formatee || '');
            $('#showFileContainer').removeClass('d-none');
            $('#showNoFile').addClass('d-none');
        } else {
            $('#showFileContainer').addClass('d-none');
            $('#showNoFile').removeClass('d-none');
        }

        // Afficher le contenu
        $loading.addClass('d-none');
        $content.removeClass('d-none');

    }).fail(function(xhr) {
        console.error("Erreur chargement courrier:", xhr);
        $loading.addClass('d-none');
        $content.html(`
            <div class="text-center text-danger py-5">
                <i class="fas fa-exclamation-triangle fa-3x mb-3 opacity-50"></i>
                <h5>Erreur de chargement</h5>
                <p class="text-muted">Impossible de récupérer les détails du courrier.</p>
            </div>
        `).removeClass('d-none');
    });
}

// Helper pour la couleur du statut
function getStatusColor(code) {
    const colors = {
        0: '#64748b', // Non affecté (Gris)
        1: '#3b82f6', // Affecté (Bleu)
        2: '#10b981', // Traité (Vert)
        3: '#ef4444'  // Archivé (Rouge)
    };
    return colors[code] || '#64748b';
}
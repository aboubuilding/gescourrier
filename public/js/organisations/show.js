/**
 * Gestion du Modal "Voir Organisation" (Show) - Sécurisé
 */

function initShowOrganisationModal() {
    
    // Gestion du clic sur "Voir détails"
    $(document).off('click', '.btn-view-organisation').on('click', '.btn-view-organisation', function() {
        const id = $(this).data('id');
        if (id) openShowOrganisation(id);
    });

    // Bouton Imprimer (dans le modal)
    $('#btnPrintShowOrg').off('click').on('click', function() {
        window.print();
    });
}

// Fonction privée pour ouvrir et remplir le modal
function openShowOrganisation(id) {
    const $modal = $('#modalShowOrganisation');
    const $loading = $('#showOrgLoading');
    const $content = $('#showOrgContent');
    
    // Reset UI & Show Loading
    $content.addClass('d-none');
    $loading.removeClass('d-none');
    $modal.modal('show');

    // Appel AJAX - ✅ Vérifie que l'URL correspond à ta route Laravel
    // Si ta route est 'organisations.show', l'URL est souvent /organisations/{id}
    $.get(`/organisations/show/${id}`, function(response) {
        
        // ✅ Extraction sécurisée des données (gère {data: {...}} ou {...} direct)
        const org = response.data || response;
        
        if (!org) {
            console.error("Aucune donnée reçue", response);
            $loading.addClass('d-none');
            $content.html('<div class="text-center text-danger">Erreur: Données vides</div>').removeClass('d-none');
            return;
        }

        // --- SÉCURISATION DES DONNÉES (Optional Chaining) ---
        const typeCode = org.type?.code ?? org.type ?? 0;
        const typeLibelle = org.type?.libelle || '—';
        
        const statutLibelle = org.etat === 'actif' ? 'Actif' : 'Inactif';
        const statutColor = org.etat === 'actif' ? '#10b981' : '#64748b';
        
        const contact = org.contact || {};
        const stats = org.statistiques?.courriers || {};

        // 1. Header Dynamique (Couleur selon Type)
        const headerColors = {
            0: 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', // Externe (Orange)
            1: 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)', // Interne (Bleu)
            2: 'linear-gradient(135deg, #10b981 0%, #059669 100%)', // Gouvernementale (Vert)
            3: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)', // Privée (Rouge)
            4: 'linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)', // ONG (Violet)
        };
        const color = headerColors[typeCode] || headerColors[0];
        $('#modalShowOrgHeader').css('background', color);

        // 2. Remplissage des champs
        $('#showOrgNom').text(org.nom || '—');
        $('#showOrgSigle').text(org.sigle ? `(${org.sigle})` : '');
        $('#showOrgAdresse').text(org.adresse || '—');
        
        // Contact
        $('#showOrgEmail').html(contact.email ? `<a href="mailto:${contact.email}" class="text-decoration-none">${contact.email}</a>` : '—');
        $('#showOrgTelephone').html(contact.telephone ? `<a href="tel:${contact.telephone}" class="text-decoration-none">${contact.telephone}</a>` : '—');
        
        // Type & Statut
        $('#showOrgType').html(`<span class="badge bg-secondary bg-opacity-10 text-secondary">${typeLibelle}</span>`);
        $('#showOrgStatut')
            .text(statutLibelle)
            .css('color', statutColor)
            .css('background', `${statutColor}15`)
            .css('border-color', `${statutColor}40`);

        // Statistiques
        $('#showOrgTotalCourriers').text(stats.total ?? 0);
        $('#showOrgEntrants').text(stats.entrants ?? 0);
        $('#showOrgSortants').text(stats.sortants ?? 0);
        $('#showOrgInternes').text(stats.internes ?? 0);
        $('#showOrgUrgents').text(stats.urgents ?? 0);

        // Dates
        const createdAt = org.created_at ? new Date(org.created_at).toLocaleDateString('fr-FR') : '—';
        $('#showOrgCreatedAt').text(createdAt);

        // Afficher le contenu
        $loading.addClass('d-none');
        $content.removeClass('d-none');

    }).fail(function(xhr) {
        console.error("Erreur chargement organisation:", {
            status: xhr.status,
            statusText: xhr.statusText,
            response: xhr.responseJSON?.message || xhr.responseText?.substring(0, 200)
        });
        
        $loading.addClass('d-none');
        
        // Message d'erreur adapté selon le code HTTP
        let errorMsg = 'Impossible de récupérer les détails de l\'organisation.';
        if (xhr.status === 404) errorMsg = 'Organisation non trouvée.';
        else if (xhr.status === 500) errorMsg = 'Erreur serveur interne.';
        else if (xhr.responseJSON?.message) errorMsg = xhr.responseJSON.message;
        
        $content.html(`
            <div class="text-center text-danger py-5">
                <i class="fas fa-exclamation-triangle fa-3x mb-3 opacity-50"></i>
                <h5>Erreur de chargement</h5>
                <p class="text-muted">${errorMsg}</p>
                <small class="text-muted">Code: ${xhr.status} ${xhr.statusText}</small>
            </div>
        `).removeClass('d-none');
    });
}
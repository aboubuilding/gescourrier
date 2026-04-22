/**
 * Gestion du Modal "Voir Service" (Show)
 */

function initShowServiceModal() {

    // Clic bouton voir service
    $(document)
        .off('click', '.btn-view-service')
        .on('click', '.btn-view-service', function () {
            const id = $(this).data('id');
            if (id) openShowService(id);
        });
}

/**
 * Ouvre et charge le modal Service
 */
function openShowService(id) {

    const $modal   = $('#modalShowService');
    const $loading = $('#showServiceLoading');
    const $content = $('#showServiceContent');

    // reset UI
    $content.hide();
    $loading.show();
    $modal.modal('show');

    $.get(`/services/${id}`, function (response) {

        const data = response.data || response;

        if (!data) {
            $loading.hide();
            $content.html(`
                <div class="text-center text-danger py-4">
                    Données introuvables
                </div>
            `).show();
            return;
        }

        // ─────────────────────────────
        // HEADER
        // ─────────────────────────────
        $('#showServiceNom').text(data.nom || '—');
        $('#showServiceNomFull').text(data.nom || '—');

        $('#showServiceAgentsBadge')
            .text(`${data.total_agents || 0} agents`);

        $('#showServiceCourriersBadge')
            .text(`${data.total_courriers || 0} courriers`);

        $('#showServiceCreatedAt').text(
            data.created_at
                ? new Date(data.created_at).toLocaleDateString('fr-FR')
                : '—'
        );

        // ─────────────────────────────
        // INFOS
        // ─────────────────────────────
        $('#showServiceNomInfo').text(data.nom || '—');

        $('#showServiceOrganisation').text(
            data.organisation?.nom || '—'
        );

        // ⚠️ ton controller ne renvoie pas email/tel => fallback
        $('#showServiceEmail').text('—');
        $('#showServiceTelephone').text('—');

        // ─────────────────────────────
        // STATS
        // ─────────────────────────────
        $('#showTotalAgents').text(data.total_agents || 0);
        $('#showTotalCourriers').text(data.total_courriers || 0);

        $('#showTopAgent').text(
            data.top_agent?.nom || '—'
        );

        // ─────────────────────────────
        // LISTE AGENTS
        // ─────────────────────────────
        const $list = $('#agentsList');
        $list.empty();

        if (!data.agents || data.agents.length === 0) {
            $('#noAgents').removeClass('d-none');
        } else {
            $('#noAgents').addClass('d-none');

            data.agents.forEach(agent => {
                $list.append(`
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <div>
                            <div class="fw-semibold">${agent.nom}</div>
                            <small class="text-muted">
                                ${agent.courriers_affectes || 0} courriers
                            </small>
                        </div>
                        <span class="badge bg-light text-dark">
                            #${agent.id}
                        </span>
                    </div>
                `);
            });
        }

        // ─────────────────────────────
        // FIN LOADING
        // ─────────────────────────────
        $loading.hide();
        $content.show();

    }).fail(function (xhr) {

        let msg = "Erreur lors du chargement du service";

        if (xhr.status === 404) msg = "Service introuvable";
        if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;

        $loading.hide();
        $content.html(`
            <div class="text-center text-danger py-5">
                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                <div>${msg}</div>
            </div>
        `).show();
    });
}
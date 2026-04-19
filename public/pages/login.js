$(document).ready(function () {
    // Configuration de Toastr pour l'affichage en haut à droite
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "4000",
        "extendedTimeOut": "1000",
        "showMethod": "slideDown",
        "hideMethod": "slideUp"
    };

    $("#form-login").on("submit", function (e) {
        e.preventDefault(); // Empêche le rechargement de la page

        let form = $(this);
        let url = form.attr("action");
        let btn = $("#btn-login");
        let btnText = $("#btn-text");
        let spinner = $("#spinner");

        // Désactiver le bouton et montrer le chargement
        btn.prop("disabled", true);
        btnText.addClass("d-none");
        spinner.removeClass("d-none");

        // Nettoyer les messages d'erreurs et styles précédents
        $(".is-invalid").removeClass("is-invalid");
        
        $.ajax({
            url: url,
            type: "POST",
            data: form.serialize(),
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                // Succès : Notification et redirection vers le dashboard
                toastr.success("Authentification réussie ! Bienvenue sur Gesfinance.", "Succès");
                
                setTimeout(function () {
                    // Utilise l'URL de redirection envoyée par le contrôleur ou le dashboard par défaut
                    window.location.href = response.redirect || "/dashboard";
                }, 1200);
            },
            error: function (xhr) {
                // Réactiver le bouton en cas d'échec
                btn.prop("disabled", false);
                btnText.removeClass("d-none");
                spinner.addClass("d-none");

                if (xhr.status === 422) {
                    // Erreurs de validation (identifiants incorrects, champs manquants)
                    let errors = xhr.responseJSON.errors;
                    
                    if (errors.login_utilisateur) {
                        toastr.error(errors.login_utilisateur[0], "Erreur d'accès");
                        $("#login_utilisateur").addClass("is-invalid");
                    }
                    
                    if (errors.mot_passe) {
                        // On affiche aussi l'erreur si le mot de passe est trop court par exemple
                        toastr.warning(errors.mot_passe[0], "Sécurité");
                        $("#mot_passe").addClass("is-invalid");
                    }
                } else if (xhr.status === 419) {
                    // Session expirée (CSRF token mismatch)
                    toastr.error("Votre session a expiré. Veuillez rafraîchir la page.", "Erreur");
                } else {
                    // Erreur serveur (500) ou autre
                    toastr.error("Impossible de contacter le serveur. Veuillez réessayer plus tard.", "Erreur système");
                }
            }
        });
    });
});
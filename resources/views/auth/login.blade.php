<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Connexion sécurisée — Système de Gestion des Courriers Officiels, République du Togo">
    
    <title>Connexion | Courriers Officiels</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Kumbh+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Core CSS (Bootstrap + FontAwesome) -->
    <link rel="stylesheet" href="{{ asset('app/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('app/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('app/assets/plugins/fontawesome/css/all.min.css') }}">
    
    <!-- Login CSS personnalisé (dans public/) -->
    <link rel="stylesheet" href="{{ asset('css/auth/login.css?v=1.0') }}">
</head>
<body>

<!-- Background animé -->
<div class="bg-scene"></div>
<div class="bg-grid"></div>
<div class="orb orb-1"></div>
<div class="orb orb-2"></div>
<div class="orb orb-3"></div>

<!-- Toast container -->
<div id="toast-container" aria-live="polite" aria-atomic="true"></div>

<!-- Modal Forgot Password -->
<div class="modal-overlay" id="forgotModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">🔐 Mot de passe oublié ?</h3>
            <button class="modal-close" id="modalClose" aria-label="Fermer">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <p style="font-size:14px;color:var(--texte-3);margin-bottom:20px;line-height:1.6;">
                Entrez votre adresse email ou identifiant. Vous recevrez un lien pour réinitialiser votre mot de passe.
            </p>
            <form id="forgotForm">
                <div class="field-group">
                    <label class="field-label" for="forgotEmail">
                        <i class="fas fa-envelope"></i> Email ou identifiant
                    </label>
                    <div class="input-shell">
                        <i class="input-icon fas fa-user"></i>
                        <input type="text" id="forgotEmail" name="email" class="field-input" 
                               placeholder="Ex : agent_01 ou email@exemple.tg" required>
                    </div>
                    <div class="error-message" id="forgotError"></div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-modal secondary" id="modalCancel">Annuler</button>
            <button class="btn-modal primary" id="modalSubmit">
                <span class="btn-text">Envoyer le lien</span>
                <span class="spinner"></span>
            </button>
        </div>
    </div>
</div>

<div class="page-wrapper">
    
    <!-- ══ Panneau gauche : Branding ══ -->
    <div class="left-panel">
        <div class="badge-republic animate-in">République Togolaise</div>
        
        <div class="armoirie-wrap animate-in delay-1">
            <img src="{{ asset('app/assets/img/armoirie.png') }}" alt="Armoirie du Togo">
        </div>
        
        <div class="flag-strip animate-in delay-2">
            <span class="fv"></span><span class="fo"></span><span class="fr"></span>
        </div>
        
        <h1 class="headline animate-in delay-3">
            Traçabilité<br>des <em>Courriers</em><br>Officiels
        </h1>
        <p class="sub-headline animate-in delay-3">
            Plateforme numérique sécurisée pour la gestion, le suivi et la traçabilité des courriers administratifs de l'État.
        </p>
        
        <div class="stats-row animate-in delay-3">
            <div class="stat-item"><div class="stat-value">100%</div><div class="stat-label">Dématérialisé</div></div>
            <div class="stat-item"><div class="stat-value">24/7</div><div class="stat-label">Disponibilité</div></div>
            <div class="stat-item"><div class="stat-value">SSL</div><div class="stat-label">Sécurisé</div></div>
        </div>
    </div>

    <!-- ══ Panneau droit : Formulaire ══ -->
    <div class="right-panel">
        <p class="form-eyebrow animate-in"><i class="fas fa-lock"></i> Portail d'accès</p>
        <h2 class="form-title animate-in delay-1">Connexion</h2>
        <p class="form-subtitle animate-in delay-2">Entrez vos identifiants pour accéder au système de gestion des courriers.</p>

        <form action="{{ route('login.attempt') }}" method="POST" id="form-login" autocomplete="off" class="animate-in delay-3">
            @csrf

            {{-- Erreurs Laravel (session) --}}
            @if($errors->any())
            <div class="alert-box" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <strong>Erreur de connexion</strong><br>
                    {{ $errors->first() }}
                </div>
                <button type="button" class="alert-close" aria-label="Fermer">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif

            <!-- Identifiant -->
            <div class="field-group">
                <label class="field-label" for="login_utilisateur">
                    <i class="fas fa-user"></i> Identifiant ou Email
                </label>
                <div class="input-shell">
                    <i class="input-icon far fa-user"></i>
                    <input type="text" id="email" name="email" class="field-input" 
                           placeholder="Ex : email@exemple.tg" 
                           value="{{ old('email') }}" 
                           required autocomplete="username" aria-required="true">
                </div>
                <div class="error-message" id="error-identifiant">Ce champ est requis</div>
            </div>

            <!-- Mot de passe -->
            <div class="field-group">
                <label class="field-label" for="mot_passe">
                    <i class="fas fa-lock"></i> Mot de passe
                </label>
                <div class="input-shell">
                    <i class="input-icon fas fa-lock"></i>
                    <input type="password" id="password" name="password" class="field-input" 
                           placeholder="••••••••••" required autocomplete="current-password" aria-required="true" minlength="6">
                    <button type="button" class="toggle-pw" id="toggle-pw" aria-label="Afficher ou masquer le mot de passe">
                        <i class="far fa-eye" id="eye-icon"></i>
                    </button>
                </div>
                <div class="error-message" id="error-password">Le mot de passe est requis</div>
            </div>

            <!-- Options -->
            <div class="options-row">
                <label class="custom-check">
                    <input type="checkbox" name="remember" id="remember">
                    <span class="check-box"></span> Rester connecté
                </label>
                <a href="#" class="forgot-link" id="forgotLink">
                    <i class="fas fa-question-circle"></i> Mot de passe oublié ?
                </a>
            </div>

            <!-- Bouton de soumission -->
            <button type="submit" class="btn-submit" id="btn-login">
                <span class="spinner"></span>
                <span class="btn-text">Accéder au portail</span>
                <i class="fas fa-arrow-right btn-arrow"></i>
            </button>
        </form>

        <div class="form-footer animate-in delay-3">
            <span class="footer-brand">&copy; {{ date('Y') }} — République du Togo</span>
            <span class="footer-badge">
                <i class="fas fa-shield-alt"></i> Connexion chiffrée SSL/TLS
            </span>
        </div>
    </div>
</div>

<!-- Scripts requis -->
<script src="{{ asset('app/assets/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('app/assets/js/bootstrap.bundle.min.js') }}"></script>

<!-- Login JS personnalisé (dans public/) -->
<script src="{{ asset('js/auth/login.js?v=1.0') }}"></script>

</body>
</html>
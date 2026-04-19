<!-- ═══════════════════════════════════════
     🏗️ HEADER PRINCIPAL — Navigation horizontale
     ═══════════════════════════════════════ -->
<header id="header-top" class="header-main">

    <!-- Mobile hamburger -->
    <button class="hamburger-btn" id="hamburger" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="main-nav">
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
    </button>

    <!-- Logo & Branding -->
    <a href="{{ route('dashboard.index') }}" class="header-brand" title="Accueil">
        <img src="{{ asset('app/assets/img/armoirie.png') }}" alt="Armoirie du Togo" class="brand-logo" width="38" height="38">
        <div class="brand-text">
            <span class="brand-title">Courriers Officiels</span>
            <span class="brand-sub">République du Togo</span>
        </div>
    </a>

    <!-- Navigation horizontale -->
    <nav id="main-nav" class="main-navigation" aria-label="Navigation principale">

        {{-- Tableau de bord --}}
        <div class="nav-item-wrap">
            <a href="{{ route('dashboard.index') }}"
               class="nav-link-main {{ request()->routeIs('dashboard.*') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('dashboard.*') ? 'page' : false }}">
                <i class="fas fa-th-large nav-icon"></i>
                <span class="nav-label">Tableau de bord</span>
            </a>
        </div>

        {{-- Courriers --}}
        <div class="nav-item-wrap">
            <a href="{{ route('courriers.index') }}"
               class="nav-link-main {{ request()->routeIs('courriers.*') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('courriers.*') ? 'page' : false }}">
                <i class="fas fa-envelope nav-icon"></i>
                <span class="nav-label">Courriers</span>
            </a>
        </div>

        {{-- Organisations --}}
        <div class="nav-item-wrap">
            <a href="{{ route('organisations.index') }}"
               class="nav-link-main {{ request()->routeIs('organisations.*') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('organisations.*') ? 'page' : false }}">
                <i class="fas fa-building nav-icon"></i>
                <span class="nav-label">Organisations</span>
            </a>
        </div>

        {{-- Services --}}
        <div class="nav-item-wrap">
            <a href="{{ route('services.index') }}"
               class="nav-link-main {{ request()->routeIs('services.*') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('services.*') ? 'page' : false }}">
                <i class="fas fa-layer-group nav-icon"></i>
                <span class="nav-label">Services</span>
            </a>
        </div>

        {{-- Agents --}}
        <div class="nav-item-wrap">
            <a href="{{ route('agents.index') }}"
               class="nav-link-main {{ request()->routeIs('agents.*') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('agents.*') ? 'page' : false }}">
                <i class="fas fa-user-tie nav-icon"></i>
                <span class="nav-label">Agents</span>
            </a>
        </div>

        {{-- Reporting --}}
        <div class="nav-item-wrap has-dropdown" data-hover="true">
            <a href="#" class="nav-link-main {{ request()->routeIs('reportings.*') ? 'active' : '' }}" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-chart-bar nav-icon"></i>
                <span class="nav-label">Reporting</span>
                <i class="fas fa-chevron-down nav-caret" aria-hidden="true"></i>
            </a>
            <div class="nav-dropdown" role="menu" aria-label="Sous-menu Reporting">
                <a href="#" class="dropdown-item-custom" role="menuitem">
                    <i class="fas fa-envelope-open-text"></i>
                    <span>Courriers</span>
                </a>
                <a href="#" class="dropdown-item-custom" role="menuitem">
                    <i class="fas fa-users"></i>
                    <span>Agents</span>
                </a>
                <a href="#" class="dropdown-item-custom" role="menuitem">
                    <i class="fas fa-sitemap"></i>
                    <span>Organisations</span>
                </a>
                <div class="dropdown-divider-custom" role="separator"></div>
                <a href="#" class="dropdown-item-custom" role="menuitem">
                    <i class="fas fa-file-export"></i>
                    <span>Exporter les rapports</span>
                </a>
            </div>
        </div>

        {{-- Administration --}}
        <div class="nav-item-wrap has-dropdown" data-hover="true">
            <a href="#" class="nav-link-main {{ request()->routeIs('users.*') || request()->routeIs('roles.*') ? 'active' : '' }}" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-cogs nav-icon"></i>
                <span class="nav-label">Administration</span>
                <i class="fas fa-chevron-down nav-caret" aria-hidden="true"></i>
            </a>
            <div class="nav-dropdown" role="menu" aria-label="Sous-menu Administration">
                <a href="{{ route('users.index') }}" class="dropdown-item-custom" role="menuitem">
                    <i class="fas fa-users-cog"></i>
                    <span>Utilisateurs</span>
                </a>
                
                <div class="dropdown-divider-custom" role="separator"></div>
                <a href="#" class="dropdown-item-custom" role="menuitem">
                    <i class="fas fa-cog"></i>
                    <span>Paramètres système</span>
                </a>
                <a href="#" class="dropdown-item-custom" role="menuitem">
                    <i class="fas fa-database"></i>
                    <span>Sauvegardes</span>
                </a>
            </div>
        </div>

    </nav>

    <!-- Actions header droite -->
    <div class="header-actions">

        {{-- Toggle Dark Mode --}}
        <button class="action-btn theme-toggle" id="themeToggle" aria-label="Basculer le mode sombre" title="Mode sombre / clair">
            <i class="fas fa-moon theme-icon" id="themeIcon"></i>
            <span class="sr-only">Changer de thème</span>
        </button>

        {{-- Notifications --}}
        <a href="#" class="action-btn notif-btn" title="Notifications" aria-label="Voir les notifications">
            <i class="fas fa-bell"></i>
            <span class="notif-dot" aria-hidden="true"></span>
            <span class="sr-only">Nouvelles notifications</span>
        </a>

        {{-- Recherche rapide --}}
        <button class="action-btn search-btn" id="btnSearch" title="Recherche (Ctrl+K)" aria-label="Ouvrir la recherche">
            <i class="fas fa-search"></i>
            <span class="sr-only">Rechercher</span>
        </button>

        {{-- Avatar utilisateur --}}
        <div class="user-avatar-wrapper" tabindex="0" role="button" aria-haspopup="true" aria-expanded="false">
            <div class="user-avatar-btn">
                <div class="avatar-circle" aria-hidden="true">
                    {{ strtoupper(substr(auth()->user()->prenom ?? auth()->user()->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(auth()->user()->nom ?? '', 0, 1)) }}
                </div>
                <div class="user-info-text">
                    <span class="user-name">{{ auth()->user()->prenom ?? auth()->user()->name ?? 'Utilisateur' }}</span>
                    <span class="user-role">{{ auth()->user()->role_label ?? auth()->user()->role ?? 'Agent' }}</span>
                </div>
                <i class="fas fa-chevron-down user-caret" aria-hidden="true"></i>
            </div>

            <div class="user-dropdown" role="menu" aria-label="Menu utilisateur">
                <div class="user-dropdown-header">
                    <div class="udh-name">{{ auth()->user()->prenom ?? auth()->user()->name ?? '' }} {{ auth()->user()->nom ?? '' }}</div>
                    <div class="udh-email">{{ auth()->user()->email ?? '' }}</div>
                    <div class="udh-role">{{ auth()->user()->role_label ?? auth()->user()->role ?? '' }}</div>
                </div>
                
                <a href="#" class="udropdown-item" role="menuitem">
                    <i class="fas fa-user-circle"></i>
                    <span>Mon profil</span>
                </a>
                <a href="#" class="udropdown-item" role="menuitem">
                    <i class="fas fa-history"></i>
                    <span>Mes activités</span>
                </a>
                <a href="#" class="udropdown-item" role="menuitem">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                    <span class="badge-notif">3</span>
                </a>
                <a href="#" class="udropdown-item" role="menuitem">
                    <i class="fas fa-cog"></i>
                    <span>Paramètres</span>
                </a>
                
                <div class="dropdown-divider-custom" role="separator"></div>
                
                <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" class="udropdown-item danger" role="menuitem">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Déconnexion</span>
                    </button>
                </form>
            </div>
        </div>

    </div>
</header>
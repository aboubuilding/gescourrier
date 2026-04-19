/**
 * 🎨 Layout JavaScript — Système de Gestion des Courriers Officiels
 * 
 * Fonctionnalités :
 * - Toast notifications avec animations
 * - Dark/Light mode toggle avec persistance + préférence système
 * - Search modal avec raccourcis clavier (Ctrl+K)
 * - Dropdowns header : hover (desktop) + click (mobile)
 * - Mobile hamburger menu avec animations
 * - Auto-hide header au scroll
 * - Initialisation des plugins (Feather, Select2)
 * - CSRF token pour AJAX
 * - Utilitaires globaux (timeAgo, formatNumber, copyToClipboard)
 * 
 * @version 1.0.0
 * @requires jQuery, Bootstrap, Feather Icons, Select2
 * @author Équipe Courriers Officiels
 */

// ═══════════════════════════════════════
// ⚙️ INIT : Attendre que le DOM soit prêt
// ═══════════════════════════════════════
document.addEventListener('DOMContentLoaded', function() {
    
    // ───────────────────────────────────
    // 🍞 TOAST NOTIFICATIONS
    // ───────────────────────────────────
    function showToast(message, type = 'info', title = '') {
        const container = document.getElementById('toast-container');
        if (!container) {
            console.warn('Toast container not found');
            return;
        }
        
        const icons = { 
            success: 'fa-check-circle', 
            error: 'fa-exclamation-circle', 
            warning: 'fa-exclamation-triangle', 
            info: 'fa-info-circle' 
        };
        
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="toast-icon"><i class="fas ${icons[type] || icons.info}"></i></div>
            <div class="toast-content">
                ${title ? `<div class="toast-title">${title}</div>` : ''}
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" aria-label="Fermer" type="button">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        // Gestion du bouton fermer avec animation
        const closeBtn = toast.querySelector('.toast-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                toast.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(20px)';
                setTimeout(() => toast.remove(), 200);
            });
        }
        
        container.appendChild(toast);
        
        // Auto-remove avec animation fluide
        setTimeout(() => {
            if (toast.parentNode) {
                toast.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(20px)';
                setTimeout(() => { if (toast.parentNode) toast.remove(); }, 200);
            }
        }, 3000);
    }

    // ───────────────────────────────────
    // 🌙 DARK MODE TOGGLE
    // ───────────────────────────────────
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const html = document.documentElement;
    
    // Load saved preference avec fallback sur préférence système
    const getPreferredTheme = () => {
        const saved = localStorage.getItem('theme');
        if (saved) return saved;
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    };
    
    const applyTheme = (theme) => {
        html.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        updateThemeIcon(theme);
        // Dispatch custom event pour que d'autres scripts puissent réagir
        document.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme } }));
    };
    
    // Appliquer le thème au chargement
    applyTheme(getPreferredTheme());
    
    function updateThemeIcon(theme) {
        if (themeIcon) {
            themeIcon.className = `fas fa-${theme === 'light' ? 'moon' : 'sun'}`;
            themeIcon.setAttribute('aria-label', `Basculer vers le mode ${theme === 'light' ? 'sombre' : 'clair'}`);
        }
    }
    
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const current = html.getAttribute('data-theme');
            const next = current === 'light' ? 'dark' : 'light';
            applyTheme(next);
            showToast(`Mode ${next === 'light' ? 'clair' : 'sombre'} activé`, 'info');
        });
    }

    // ───────────────────────────────────
    // 🔍 SEARCH MODAL
    // ───────────────────────────────────
    const searchModal = document.getElementById('searchModal');
    const btnSearch = document.getElementById('btnSearch');
    const searchClose = document.getElementById('searchModalClose');
    const searchInput = searchModal?.querySelector('.search-modal-input');
    let searchResultsContainer = null;
    
    function openSearch() {
        if (!searchModal) return;
        searchModal.classList.add('active');
        // Focus dans l'input avec délai pour l'animation CSS
        setTimeout(() => {
            searchInput?.focus();
            searchInput?.select();
        }, 150);
        // Bloquer le scroll du body
        document.body.style.overflow = 'hidden';
    }
    
    function closeSearch() {
        if (!searchModal) return;
        searchModal.classList.remove('active');
        document.body.style.overflow = '';
        // Clear input et results on close
        if (searchInput) searchInput.value = '';
        if (searchResultsContainer) searchResultsContainer.innerHTML = '';
    }
    
    // Event listeners pour la search modal
    if (btnSearch) {
        btnSearch.addEventListener('click', (e) => {
            e.preventDefault();
            openSearch();
        });
    }
    
    if (searchClose) {
        searchClose.addEventListener('click', closeSearch);
    }
    
    if (searchModal) {
        // Fermer en cliquant en dehors du contenu modal
        searchModal.addEventListener('click', (e) => {
            if (e.target === searchModal) closeSearch();
        });
        
        // Fermer avec la touche Escape
        searchModal.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                e.preventDefault();
                closeSearch();
            }
        });
    }
    
    // Raccourci clavier: Ctrl+K ou Cmd+K (uniquement si pas dans un input/textarea)
    document.addEventListener('keydown', (e) => {
        const tag = e.target.tagName.toLowerCase();
        const isEditable = tag === 'input' || tag === 'textarea' || e.target.isContentEditable;
        
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k' && !isEditable) {
            e.preventDefault();
            openSearch();
        }
    });
    
    // Helper: Debounce pour la recherche (si activation future)
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // ───────────────────────────────────
    // 🖱️ DROPDOWN HEADER : Hover desktop + Click mobile
    // ───────────────────────────────────
    
    const dropdownWraps = document.querySelectorAll('.nav-item-wrap.has-dropdown');
    let hoverTimeout = null;
    const HOVER_DELAY = 150; // Délai anti-flicker en ms
    
    // ── Desktop : Hover avec délai ──
    if (window.innerWidth > 1024) {
        dropdownWraps.forEach(wrap => {
            const link = wrap.querySelector('.nav-link-main');
            const dropdown = wrap.querySelector('.nav-dropdown');
            
            if (!link || !dropdown) return;
            
            // Au survol : ouvrir après délai
            wrap.addEventListener('mouseenter', () => {
                clearTimeout(hoverTimeout);
                hoverTimeout = setTimeout(() => {
                    // Fermer les autres dropdowns ouverts
                    dropdownWraps.forEach(other => {
                        if (other !== wrap && other.classList.contains('dropdown-open')) {
                            other.classList.remove('dropdown-open');
                            other.querySelector('.nav-link-main')?.setAttribute('aria-expanded', 'false');
                        }
                    });
                    
                    // Ouvrir le dropdown courant
                    wrap.classList.add('dropdown-open');
                    link.setAttribute('aria-expanded', 'true');
                }, HOVER_DELAY);
            });
            
            // À la sortie : fermer après délai (pour éviter les fermetures accidentelles)
            wrap.addEventListener('mouseleave', () => {
                clearTimeout(hoverTimeout);
                hoverTimeout = setTimeout(() => {
                    wrap.classList.remove('dropdown-open');
                    link.setAttribute('aria-expanded', 'false');
                }, HOVER_DELAY);
            });
        });
    }
    
    // ── Mobile : Toggle au clic ──
    dropdownWraps.forEach(wrap => {
        const link = wrap.querySelector('.nav-link-main');
        
        if (link) {
            link.addEventListener('click', function(e) {
                // Seulement sur mobile (<1024px)
                if (window.innerWidth <= 1024) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const isOpen = wrap.classList.toggle('dropdown-open');
                    link.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                    
                    // Fermer les autres dropdowns mobiles
                    dropdownWraps.forEach(other => {
                        if (other !== wrap && other.classList.contains('dropdown-open')) {
                            other.classList.remove('dropdown-open');
                            other.querySelector('.nav-link-main')?.setAttribute('aria-expanded', 'false');
                        }
                    });
                }
            });
        }
    });
    
    // ── Fermer tous les dropdowns au clic extérieur (mobile) ──
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 1024) {
            if (!e.target.closest('.nav-item-wrap.has-dropdown')) {
                dropdownWraps.forEach(wrap => {
                    wrap.classList.remove('dropdown-open');
                    wrap.querySelector('.nav-link-main')?.setAttribute('aria-expanded', 'false');
                });
            }
        }
    });

    // ───────────────────────────────────
    // 📱 MOBILE HAMBURGER MENU
    // ───────────────────────────────────
    const hamburger = document.getElementById('hamburger');
    const mainNav = document.getElementById('main-nav');
    
    function toggleMobileMenu() {
        if (!mainNav || !hamburger) return;
        
        const isOpen = mainNav.classList.toggle('open');
        hamburger.setAttribute('aria-expanded', isOpen);
        
        // Animer le hamburger en croix (X)
        const spans = hamburger.querySelectorAll('span');
        if (isOpen) {
            spans[0]?.style.setProperty('transform', 'translateY(7px) rotate(45deg)');
            spans[1]?.style.setProperty('opacity', '0');
            spans[2]?.style.setProperty('transform', 'translateY(-7px) rotate(-45deg)');
        } else {
            spans.forEach(s => { 
                s.style.setProperty('transform', ''); 
                s.style.setProperty('opacity', ''); 
            });
        }
        
        // Gestion du focus pour l'accessibilité
        if (isOpen) {
            const firstLink = mainNav.querySelector('a, button');
            firstLink?.focus();
        }
    }
    
    if (hamburger) {
        hamburger.addEventListener('click', toggleMobileMenu);
    }

    // ───────────────────────────────────
    // 🖱️ Fermer les menus au clic extérieur
    // ───────────────────────────────────
    document.addEventListener('click', (e) => {
        // Fermer le menu mobile si on clique en dehors
        if (window.innerWidth < 1024 && mainNav?.classList.contains('open')) {
            const isInsideNav = e.target.closest('#main-nav');
            const isHamburger = e.target.closest('#hamburger');
            
            if (!isInsideNav && !isHamburger) {
                mainNav.classList.remove('open');
                hamburger?.setAttribute('aria-expanded', 'false');
                const spans = hamburger?.querySelectorAll('span');
                spans?.forEach(s => { 
                    s.style.setProperty('transform', ''); 
                    s.style.setProperty('opacity', ''); 
                });
            }
        }
        
        // Fermer les dropdowns utilisateur au clic extérieur
        const userDropdown = document.querySelector('.user-avatar-wrapper');
        const userMenu = document.querySelector('.user-dropdown');
        if (userDropdown && userMenu && !e.target.closest('.user-avatar-wrapper')) {
            userMenu.style.opacity = '';
            userMenu.style.visibility = '';
            userMenu.style.transform = '';
        }
    });

    // ───────────────────────────────────
    // 🎨 FEATHER ICONS INIT
    // ───────────────────────────────────
    if (typeof feather !== 'undefined') {
        // Petit délai pour s'assurer que tout le DOM est rendu
        setTimeout(() => {
            try {
                feather.replace();
            } catch (e) {
                console.warn('Feather icons error:', e);
            }
        }, 50);
    }

    // ───────────────────────────────────
    // 🔽 SELECT2 INIT
    // ───────────────────────────────────
    if (typeof jQuery !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
        // Initialiser uniquement les select2 qui n'ont pas encore été initialisés
        $('select.select2').each(function() {
            const $this = $(this);
            if (!$this.data('select2')) {
                $this.select2({ 
                    width: '100%', 
                    theme: 'classic',
                    language: 'fr', // Langue française
                    dropdownParent: $this.closest('.modal') || $(document.body) // Pour les modals
                });
            }
        });
    }

    // ───────────────────────────────────
    // 🔐 CSRF TOKEN POUR AJAX (jQuery)
    // ───────────────────────────────────
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (csrfMeta?.content && typeof jQuery !== 'undefined' && typeof $.ajaxSetup !== 'undefined') {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': csrfMeta.content }
        });
    }

    // ───────────────────────────────────
    // 📜 AUTO-HIDE HEADER AU SCROLL
    // ───────────────────────────────────
    let lastScrollY = 0;
    const scrollThreshold = 100; // Pixels avant de commencer à cacher le header
    
    function handleHeaderScroll() {
        const header = document.getElementById('header-top');
        if (!header) return;
        
        const currentScrollY = window.scrollY;
        
        // Ajouter la classe "scrolled" après un certain seuil
        if (currentScrollY > scrollThreshold) {
            header.classList.add('scrolled');
            
            // Cacher le header quand on scroll vers le bas (si le menu mobile n'est pas ouvert)
            if (currentScrollY > lastScrollY && !mainNav?.classList.contains('open')) {
                header.style.setProperty('transform', 'translateY(-100%)');
            } else {
                header.style.setProperty('transform', 'translateY(0)');
            }
        } else {
            header.classList.remove('scrolled');
            header.style.setProperty('transform', 'translateY(0)');
        }
        
        lastScrollY = currentScrollY;
    }
    
    // Throttle pour optimiser les performances du scroll
    function throttle(func, limit) {
        let inThrottle = false;
        return function(...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }
    
    // Ajouter l'event listener avec throttle et passive pour la performance
    window.addEventListener('scroll', throttle(handleHeaderScroll, 100), { passive: true });

    // ───────────────────────────────────
    // 🔄 Recalcul au resize : switch hover/click mode
    // ───────────────────────────────────
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            // Si on passe en desktop : fermer tous les dropdowns mobiles
            if (window.innerWidth > 1024) {
                dropdownWraps.forEach(wrap => {
                    wrap.classList.remove('dropdown-open', 'open-mobile');
                    wrap.querySelector('.nav-link-main')?.setAttribute('aria-expanded', 'false');
                });
                
                // Fermer le menu hamburger si ouvert
                if (mainNav?.classList.contains('open')) {
                    mainNav.classList.remove('open');
                    hamburger?.setAttribute('aria-expanded', 'false');
                    hamburger?.querySelectorAll('span').forEach(s => { 
                        s.style.setProperty('transform', ''); 
                        s.style.setProperty('opacity', ''); 
                    });
                }
            }
        }, 150);
    });

    // ───────────────────────────────────
    // 🎯 UTILITAIRES GLOBAUX
    // ───────────────────────────────────
    
    // Exposer showToast globalement pour usage depuis d'autres scripts
    window.showToast = showToast;
    
    // Helper: Formater une date en relatif ("Il y a 2 heures")
    window.timeAgo = function(date) {
        if (!date) return '';
        const now = new Date();
        const past = new Date(date);
        const seconds = Math.floor((now - past) / 1000);
        
        const intervals = [
            { label: 'an', seconds: 31536000 },
            { label: 'mois', seconds: 2592000 },
            { label: 'jour', seconds: 86400 },
            { label: 'heure', seconds: 3600 },
            { label: 'minute', seconds: 60 },
            { label: 'seconde', seconds: 1 }
        ];
        
        for (const interval of intervals) {
            const count = Math.floor(seconds / interval.seconds);
            if (count >= 1) {
                return `Il y a ${count} ${interval.label}${count > 1 ? 's' : ''}`;
            }
        }
        return 'À l\'instant';
    };
    
    // Helper: Formater un nombre avec séparateur de milliers
    window.formatNumber = function(num, decimals = 0) {
        return new Intl.NumberFormat('fr-FR', { 
            minimumFractionDigits: decimals, 
            maximumFractionDigits: decimals 
        }).format(num);
    };
    
    // Helper: Copier du texte dans le presse-papiers
    window.copyToClipboard = async function(text) {
        try {
            await navigator.clipboard.writeText(text);
            showToast('Copié dans le presse-papiers', 'success');
            return true;
        } catch (err) {
            // Fallback pour les anciens navigateurs
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.left = '-9999px';
            document.body.appendChild(textarea);
            textarea.focus();
            textarea.select();
            try {
                document.execCommand('copy');
                showToast('Copié dans le presse-papiers', 'success');
                return true;
            } catch (err) {
                showToast('Erreur de copie', 'error');
                return false;
            } finally {
                document.body.removeChild(textarea);
            }
        }
    };

    // ───────────────────────────────────
    // 🐛 DEBUG (à commenter en production)
    // ───────────────────────────────────
    // console.log('✅ Layout JS v1.0.0 loaded');
    // console.log('🎨 Theme:', html.getAttribute('data-theme'));
    // console.log('📱 Mobile:', window.innerWidth < 1024 ? 'yes' : 'no');
    
}); // ← FIN DU DOMContentLoaded
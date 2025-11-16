/* ===================================
   🚀 MODERN ADMIN JS - INTERACCIONES
   Manejo de sidebar, header y componentes modernos
   =================================== */

// === FUNCIÓN GLOBAL PARA ACCESIBILIDAD DE MODALES ===
window.setupModalAccessibility = function(modalElement) {
    modalElement.addEventListener('shown.bs.modal', function() {
        this.removeAttribute('aria-hidden');
        // Asegurar que el foco esté en el modal
        this.focus();
    });

    modalElement.addEventListener('hidden.bs.modal', function() {
        this.setAttribute('aria-hidden', 'true');
    });
};

document.addEventListener('DOMContentLoaded', function() {
    // === ELEMENTOS DEL DOM ===
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const headerUser = document.getElementById('headerUser');
    const mainContent = document.getElementById('mainContent');

    // === ESTADO DEL SIDEBAR ===
    let sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

    // Aplicar estado inicial
    if (sidebarCollapsed) {
        sidebar.classList.add('collapsed');
        if (mainContent) {
            mainContent.style.marginLeft = 'var(--sidebar-collapsed-width)';
        }
    }

    // === TOGGLE SIDEBAR DESKTOP ===
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebarCollapsed = !sidebarCollapsed;

            if (sidebarCollapsed) {
                sidebar.classList.add('collapsed');
                if (mainContent) {
                    mainContent.style.marginLeft = 'var(--sidebar-collapsed-width)';
                }
            } else {
                sidebar.classList.remove('collapsed');
                if (mainContent) {
                    mainContent.style.marginLeft = 'var(--sidebar-width)';
                }
            }

            // Guardar estado
            localStorage.setItem('sidebarCollapsed', sidebarCollapsed);
        });
    }

    // === TOGGLE SIDEBAR MÓVIL ===
    if (mobileSidebarToggle) {
        mobileSidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('mobile-open');
            sidebarOverlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('mobile-open') ? 'hidden' : '';
        });
    }

    // === CERRAR SIDEBAR MÓVIL AL HACER CLICK EN OVERLAY ===
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('mobile-open');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });
    }

    // === DROPDOWN DE USUARIO ===
    if (headerUser) {
        headerUser.addEventListener('click', function(e) {
            e.stopPropagation();
            headerUser.classList.toggle('active');
        });

        // Cerrar dropdown al hacer click fuera
        document.addEventListener('click', function() {
            headerUser.classList.remove('active');
        });
    }

    // === BÚSQUEDA EN TIEMPO REAL ===
    const searchInput = document.querySelector('.header-search-input');
    if (searchInput) {
        let searchTimeout;

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length >= 2) {
                searchTimeout = setTimeout(() => {
                    performSearch(query);
                }, 300);
            }
        });
    }

    // === FUNCIÓN DE BÚSQUEDA ===
    function performSearch(query) {
        // Aquí puedes implementar la lógica de búsqueda
        console.log('Buscando:', query);

        // Ejemplo: buscar en la navegación
        const sidebarLinks = document.querySelectorAll('.sidebar-text');
        sidebarLinks.forEach(link => {
            const text = link.textContent.toLowerCase();
            const item = link.closest('.sidebar-item');

            if (text.includes(query.toLowerCase())) {
                item.style.display = 'block';
                item.style.background = 'rgba(76, 175, 80, 0.1)';
            } else {
                item.style.background = '';
            }
        });
    }

    // === ANIMACIONES DE ENTRADA ===
    function animateElements() {
        const animatedElements = document.querySelectorAll('.animate-fade-in-up');
        animatedElements.forEach((element, index) => {
            element.style.animationDelay = `${index * 0.1}s`;
        });
    }

    // === TOOLTIPS PARA SIDEBAR COLAPSADO ===
    function initializeTooltips() {
        const sidebarLinks = document.querySelectorAll('.sidebar-link');

        sidebarLinks.forEach(link => {
            link.addEventListener('mouseenter', function() {
                if (sidebar.classList.contains('collapsed')) {
                    const tooltip = this.querySelector('.sidebar-tooltip');
                    if (tooltip) {
                        tooltip.style.opacity = '1';
                    }
                }
            });

            link.addEventListener('mouseleave', function() {
                const tooltip = this.querySelector('.sidebar-tooltip');
                if (tooltip) {
                    tooltip.style.opacity = '0';
                }
            });
        });
    }

    // === NOTIFICACIONES DINÁMICAS ===
    function updateNotificationBadge() {
        // Simulación de actualización de notificaciones
        const badge = document.querySelector('.header-notification-badge');
        if (badge) {
            // Aquí puedes conectar con tu API para obtener el número real
            // Por ahora es estático
        }
    }

    // === RELOJ EN TIEMPO REAL ===
    function updateDateTime() {
        const dateDay = document.querySelector('.header-date-day');
        const dateFull = document.querySelector('.header-date-full');

        if (dateDay && dateFull) {
            const now = new Date();
            const day = now.getDate().toString().padStart(2, '0');
            const month = now.toLocaleDateString('es-ES', { month: 'short' });
            const year = now.getFullYear();

            dateDay.textContent = day;
            dateFull.textContent = `${month} ${year}`;
        }
    }

    // === INICIALIZACIÓN ===
    animateElements();
    initializeTooltips();
    updateNotificationBadge();
    updateDateTime();

    // Actualizar fecha cada minuto
    setInterval(updateDateTime, 60000);

    // === RESPONSIVE HANDLING ===
    function handleResize() {
        const isMobile = window.innerWidth <= 768;

        if (isMobile && sidebar.classList.contains('mobile-open')) {
            // No hacer nada, mantener el estado móvil
        } else if (!isMobile) {
            // Desktop: cerrar overlay móvil si está abierto
            sidebar.classList.remove('mobile-open');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    window.addEventListener('resize', handleResize);

    // === SMOOTH SCROLLING ===
    function smoothScrollToSection(targetId) {
        const element = document.getElementById(targetId);
        if (element) {
            element.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }

    // === KEYBOARD SHORTCUTS ===
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + B: Toggle sidebar
        if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
            e.preventDefault();
            if (sidebarToggle) {
                sidebarToggle.click();
            }
        }

        // Escape: Cerrar dropdowns y overlays
        if (e.key === 'Escape') {
            headerUser?.classList.remove('active');
            sidebar.classList.remove('mobile-open');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Ctrl/Cmd + K: Focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchInput?.focus();
        }
    });

    // === CARDS HOVER EFFECTS ===
    const cards = document.querySelectorAll('.card-modern, .stat-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // === AUTO-HIDE ALERTS ===
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert.classList.contains('show')) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000); // 5 segundos
    });

    console.log('🚀 Modern Admin UI initialized successfully!');
});

// === UTILIDADES GLOBALES ===
window.ModernAdmin = {
    // Función para mostrar notificaciones toast
    showToast: function(message, type = 'success') {
        // Implementar sistema de toast personalizado
        console.log(`Toast [${type}]: ${message}`);
    },

    // Función para confirmar acciones
    confirm: function(message, callback) {
        if (confirm(message)) {
            callback();
        }
    },

    // Función para cargar contenido dinámico
    loadContent: function(url, containerId) {
        fetch(url)
            .then(response => response.text())
            .then(html => {
                document.getElementById(containerId).innerHTML = html;
            })
            .catch(error => {
                console.error('Error loading content:', error);
            });
    }
};

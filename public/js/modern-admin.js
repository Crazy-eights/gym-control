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
    const body = document.body;
    const html = document.documentElement;

    // === ESTADO DEL SIDEBAR ===
    // Recuperar estado guardado o usar false por defecto (expandido)
    const savedState = localStorage.getItem('sidebarCollapsed');
    const sidebarCollapsed = savedState === 'true';

    console.log('DOMContentLoaded - Estado guardado:', savedState, '-> Colapsado:', sidebarCollapsed);

    // Remover la clase temporal del HTML
    html.classList.remove('sidebar-collapsed-initial');

    // Aplicar estado definitivo al sidebar y body
    if (sidebar) {
        if (sidebarCollapsed) {
            sidebar.classList.add('collapsed');
            body.classList.add('sidebar-collapsed');
        } else {
            sidebar.classList.remove('collapsed');
            body.classList.remove('sidebar-collapsed');
        }
    }

    // === TOGGLE SIDEBAR DESKTOP ===
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            const isCurrentlyCollapsed = sidebar.classList.contains('collapsed');
            
            // Toggle la clase en sidebar y body
            sidebar.classList.toggle('collapsed');
            body.classList.toggle('sidebar-collapsed');
            
            // Guardar el nuevo estado
            const newState = !isCurrentlyCollapsed;
            localStorage.setItem('sidebarCollapsed', newState);
            
            console.log('Sidebar toggle -> Nuevo estado:', newState ? 'Colapsado' : 'Expandido');
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

    // === BÚSQUEDA GLOBAL ===
    const searchInput = document.getElementById('globalSearch');
    const searchResults = document.getElementById('searchResults');
    
    if (searchInput && searchResults) {
        let searchTimeout;

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length >= 2) {
                searchTimeout = setTimeout(() => {
                    performGlobalSearch(query);
                }, 300);
            } else {
                searchResults.style.display = 'none';
            }
        });

        // Cerrar resultados al hacer click fuera
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });
    }

    // === FUNCIÓN DE BÚSQUEDA GLOBAL ===
    async function performGlobalSearch(query) {
        try {
            const url = `/admin/search?q=${encodeURIComponent(query)}`;
            const response = await fetch(url);
            const data = await response.json();

            if (data.results && data.results.length > 0) {
                displaySearchResults(data.results);
            } else {
                displayNoResults();
            }
        } catch (error) {
            console.error('Error en búsqueda:', error);
            searchResults.innerHTML = `
                <div class="search-no-results">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>Error al realizar la búsqueda</div>
                </div>
            `;
            searchResults.style.display = 'block';
        }
    }

    // === MOSTRAR RESULTADOS DE BÚSQUEDA ===
    function displaySearchResults(results) {
        const html = results.map(result => `
            <a href="${result.url}" class="search-result-item">
                <div class="search-result-icon ${result.color}">
                    <i class="fas ${result.icon}"></i>
                </div>
                <div class="search-result-content">
                    <div class="search-result-title">${result.title}</div>
                    <div class="search-result-subtitle">${result.subtitle}</div>
                </div>
            </a>
        `).join('');

        searchResults.innerHTML = html;
        searchResults.style.display = 'block';
    }

    // === MOSTRAR "SIN RESULTADOS" ===
    function displayNoResults() {
        searchResults.innerHTML = `
            <div class="search-no-results">
                <i class="fas fa-search"></i>
                <div>No se encontraron resultados</div>
            </div>
        `;
        searchResults.style.display = 'block';
    }

    // === NOTIFICACIONES ===
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationsDropdown = document.getElementById('notificationsDropdown');
    const notificationBadge = document.getElementById('notificationBadge');
    const notificationsList = document.getElementById('notificationsList');
    const markAllRead = document.getElementById('markAllRead');

    if (notificationBtn && notificationsDropdown) {
        // Toggle dropdown
        notificationBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationsDropdown.style.display = 
                notificationsDropdown.style.display === 'block' ? 'none' : 'block';
            
            if (notificationsDropdown.style.display === 'block') {
                loadNotifications();
            }
        });

        // Cerrar dropdown al hacer click fuera
        document.addEventListener('click', function(e) {
            if (!notificationBtn.contains(e.target) && !notificationsDropdown.contains(e.target)) {
                notificationsDropdown.style.display = 'none';
            }
        });

        // Marcar todas como leídas
        if (markAllRead) {
            markAllRead.addEventListener('click', function() {
                markAllNotificationsAsRead();
            });
        }

        // Cargar notificaciones iniciales
        loadNotificationsCount();
        
        // Actualizar cada 30 segundos
        setInterval(loadNotificationsCount, 30000);
    }

    // === CARGAR NOTIFICACIONES ===
    async function loadNotifications() {
        try {
            const response = await fetch('/admin/notifications/unread');
            const data = await response.json();

            if (data.notifications && data.notifications.length > 0) {
                displayNotifications(data.notifications);
            } else {
                displayNoNotifications();
            }

            updateNotificationBadge(data.unread_count);
        } catch (error) {
            console.error('Error cargando notificaciones:', error);
            notificationsList.innerHTML = '<div class="text-center py-3 text-danger">Error al cargar notificaciones</div>';
        }
    }

    // === CARGAR SOLO EL CONTADOR ===
    async function loadNotificationsCount() {
        try {
            const response = await fetch('/admin/notifications/unread');
            const data = await response.json();
            updateNotificationBadge(data.unread_count);
        } catch (error) {
            console.error('Error cargando contador:', error);
        }
    }

    // === MOSTRAR NOTIFICACIONES ===
    function displayNotifications(notifications) {
        const html = notifications.map(notif => `
            <div class="notification-item ${notif.read ? '' : 'unread'}" data-id="${notif.id}">
                <div class="notification-icon ${notif.color}">
                    <i class="fas ${notif.icon}"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-title">${notif.title}</div>
                    <div class="notification-message">${notif.message}</div>
                    <div class="notification-time">${notif.created_at}</div>
                </div>
                <div class="notification-actions">
                    <button class="notification-action-btn mark-read" data-id="${notif.id}" title="Marcar como leída">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="notification-action-btn delete-notif" data-id="${notif.id}" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');

        notificationsList.innerHTML = html;

        // Agregar event listeners
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function(e) {
                if (!e.target.closest('.notification-actions')) {
                    const url = notifications.find(n => n.id === this.dataset.id)?.url;
                    if (url && url !== '#') {
                        markNotificationAsRead(this.dataset.id);
                        window.location.href = url;
                    }
                }
            });
        });

        document.querySelectorAll('.mark-read').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                markNotificationAsRead(this.dataset.id);
            });
        });

        document.querySelectorAll('.delete-notif').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                deleteNotification(this.dataset.id);
            });
        });
    }

    // === SIN NOTIFICACIONES ===
    function displayNoNotifications() {
        notificationsList.innerHTML = `
            <div class="notifications-empty">
                <i class="fas fa-bell-slash"></i>
                <div>No tienes notificaciones</div>
            </div>
        `;
    }

    // === ACTUALIZAR BADGE ===
    function updateNotificationBadge(count) {
        if (notificationBadge) {
            notificationBadge.textContent = count;
            if (count > 0) {
                notificationBadge.classList.add('has-notifications');
            } else {
                notificationBadge.classList.remove('has-notifications');
            }
        }
    }

    // === MARCAR COMO LEÍDA ===
    async function markNotificationAsRead(id) {
        try {
            const response = await fetch(`/admin/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            if (data.success) {
                loadNotifications();
            }
        } catch (error) {
            console.error('Error marcando notificación:', error);
        }
    }

    // === MARCAR TODAS COMO LEÍDAS ===
    async function markAllNotificationsAsRead() {
        try {
            const response = await fetch('/admin/notifications/read-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            if (data.success) {
                loadNotifications();
            }
        } catch (error) {
            console.error('Error marcando todas:', error);
        }
    }

    // === ELIMINAR NOTIFICACIÓN ===
    async function deleteNotification(id) {
        try {
            const response = await fetch(`/admin/notifications/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            if (data.success) {
                loadNotifications();
            }
        } catch (error) {
            console.error('Error eliminando notificación:', error);
        }
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

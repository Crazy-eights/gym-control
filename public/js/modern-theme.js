/* ===================================
   ⚡ MODERN THEME JAVASCRIPT
   Funcionalidad interactiva para el tema moderno
   =================================== */

document.addEventListener('DOMContentLoaded', function() {
    
    // === SIDEBAR TOGGLE === //
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            
            // Guardar estado en localStorage
            if (sidebar.classList.contains('collapsed')) {
                localStorage.setItem('sidebarCollapsed', 'true');
            } else {
                localStorage.setItem('sidebarCollapsed', 'false');
            }
        });
    }
    
    // === RESTAURAR ESTADO DEL SIDEBAR === //
    const sidebarState = localStorage.getItem('sidebarCollapsed');
    if (sidebarState === 'true' && sidebar) {
        sidebar.classList.add('collapsed');
    }
    
    // === MOBILE SIDEBAR OVERLAY === //
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('show-mobile');
            sidebarOverlay.classList.remove('show');
        });
    }
    
    // === MOBILE SIDEBAR TOGGLE === //
    if (window.innerWidth <= 768) {
        if (sidebarToggle && sidebar && sidebarOverlay) {
            sidebarToggle.addEventListener('click', function(e) {
                e.preventDefault();
                sidebar.classList.toggle('show-mobile');
                sidebarOverlay.classList.toggle('show');
            });
        }
    }
    
    // === TOOLTIPS === //
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    if (typeof bootstrap !== 'undefined') {
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // === DROPDOWN AUTO-CLOSE === //
    document.addEventListener('click', function(e) {
        const dropdowns = document.querySelectorAll('.dropdown-menu.show');
        dropdowns.forEach(function(dropdown) {
            if (!dropdown.contains(e.target) && !dropdown.previousElementSibling.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });
    });
    
    // === SMOOTH SCROLL PARA ANCLAS === //
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // === CARDS HOVER EFFECT === //
    const cards = document.querySelectorAll('.card');
    cards.forEach(function(card) {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // === FORM VALIDATION FEEDBACK === //
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
    
    // === AUTO-HIDE ALERTS === //
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert && alert.parentNode) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000); // 5 segundos
    });
    
    // === LOADING STATES === //
    const loadingButtons = document.querySelectorAll('[data-loading]');
    loadingButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Cargando...';
            this.disabled = true;
            
            // Restaurar después de 3 segundos (ajustar según necesidad)
            setTimeout(() => {
                this.innerHTML = originalText;
                this.disabled = false;
            }, 3000);
        });
    });
    
    // === RESPONSIVE SIDEBAR === //
    function handleResize() {
        if (window.innerWidth <= 768) {
            if (sidebar) {
                sidebar.classList.remove('collapsed');
                sidebar.classList.remove('show-mobile');
            }
            if (sidebarOverlay) {
                sidebarOverlay.classList.remove('show');
            }
        }
    }
    
    window.addEventListener('resize', handleResize);
    
    // === DARK MODE TOGGLE (para futuro) === //
    const darkModeToggle = document.getElementById('darkModeToggle');
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
            
            // Cambiar icono
            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-moon');
                icon.classList.toggle('fa-sun');
            }
        });
        
        // Restaurar estado del dark mode
        const darkMode = localStorage.getItem('darkMode');
        if (darkMode === 'enabled') {
            document.body.classList.add('dark-mode');
        }
    }
    
    // === INITIALIZE COMPONENTS === //
    initializeComponents();
});

function initializeComponents() {
    // Inicializar componentes de Bootstrap si están disponibles
    if (typeof bootstrap !== 'undefined') {
        // Inicializar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Inicializar popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    }
}
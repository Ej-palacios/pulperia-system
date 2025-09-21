/**
 * Fluent UI JavaScript - Pulpería System
 * Modern JavaScript with Fluent UI interactions
 */

class FluentApp {
    constructor() {
        this.init();
        this.setupEventListeners();
        this.setupNotifications();
        this.setupSidebar();
        this.setupLoadingStates();
        this.setupFormValidation();
        this.setupKeyboardShortcuts();
    }

    init() {
        console.log('🚀 Fluent App initialized');
        this.showWelcomeMessage();
        this.setupTooltips();
        this.setupDropdowns();
        this.setupAlerts();
    }

    showWelcomeMessage() {
        const user = this.getCurrentUser();
        if (user) {
            this.showToast(`¡Bienvenido de nuevo, ${user.name}!`, 'success');
        }
    }

    setupEventListeners() {
        // Global click handler for dynamic content
        document.addEventListener('click', (e) => {
            this.handleGlobalClick(e);
        });

        // Form submission handler
        document.addEventListener('submit', (e) => {
            this.handleFormSubmit(e);
        });

        // Window resize handler
        window.addEventListener('resize', this.debounce(() => {
            this.handleResize();
        }, 250));

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            this.handleKeyboardShortcuts(e);
        });
    }

    setupSidebar() {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarToggleMobile = document.getElementById('sidebarToggleMobile');
        const mainContent = document.getElementById('mainContent');

        // Desktop sidebar toggle
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                this.toggleSidebar();
            });
        }

        // Mobile sidebar toggle
        if (sidebarToggleMobile) {
            sidebarToggleMobile.addEventListener('click', () => {
                this.toggleSidebarMobile();
            });
        }

        // Close mobile sidebar when clicking outside
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 1024 && sidebar.classList.contains('show')) {
                if (!sidebar.contains(e.target) && !sidebarToggleMobile.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });

        // Handle sidebar navigation
        const navItems = document.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            item.addEventListener('click', (e) => {
                this.handleNavigation(e, item);
            });
        });
    }

    setupNotifications() {
        const notificationBtn = document.querySelector('.notification-btn');
        const notificationBadge = document.querySelector('.notification-badge');

        if (notificationBtn) {
            notificationBtn.addEventListener('click', () => {
                this.markNotificationsAsRead();
            });
        }

        // Simulate real-time notifications
        this.setupNotificationSimulation();
    }

    setupNotificationSimulation() {
        // Simulate notifications every 30 seconds
        setInterval(() => {
            if (Math.random() > 0.7) {
                this.addNotification({
                    type: 'info',
                    title: 'Nueva actualización',
                    message: 'El sistema se ha actualizado correctamente',
                    time: new Date()
                });
            }
        }, 30000);
    }

    setupLoadingStates() {
        // Global loading overlay
        this.loadingOverlay = document.getElementById('loadingOverlay');

        // Intercept form submissions to show loading
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', () => {
                this.showLoading();
            });
        });

        // Intercept link clicks for loading
        const links = document.querySelectorAll('a[href^="/"], a[href^="http"]');
        links.forEach(link => {
            link.addEventListener('click', (e) => {
                if (link.href && !link.target) {
                    this.showLoading();
                }
            });
        });
    }

    setupFormValidation() {
        const forms = document.querySelectorAll('form[data-validate]');
        forms.forEach(form => {
            this.setupFormValidationForForm(form);
        });
    }

    setupFormValidationForForm(form) {
        const inputs = form.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('blur', () => {
                this.validateField(input);
            });

            input.addEventListener('input', this.debounce(() => {
                this.validateField(input);
            }, 300));
        });

        form.addEventListener('submit', (e) => {
            if (!this.validateForm(form)) {
                e.preventDefault();
            }
        });
    }

    setupKeyboardShortcuts() {
        this.shortcuts = {
            'ctrl+/': () => this.showKeyboardShortcuts(),
            'ctrl+k': () => this.openCommandPalette(),
            'ctrl+n': () => this.openNewItem(),
            'escape': () => this.closeModals(),
            'ctrl+s': (e) => this.saveForm(e)
        };
    }

    // Sidebar Methods
    toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('sidebar-collapsed');
        
        // Save preference
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    }

    toggleSidebarMobile() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('show');
    }

    // Navigation Methods
    handleNavigation(e, item) {
        // Add loading state
        this.showLoading();
        
        // Add active state
        const navItems = document.querySelectorAll('.nav-item');
        navItems.forEach(nav => nav.classList.remove('active'));
        item.classList.add('active');
        
        // Store current page
        localStorage.setItem('currentPage', item.href);
    }

    // Notification Methods
    addNotification(notification) {
        const notificationList = document.querySelector('.notification-list');
        const notificationBadge = document.querySelector('.notification-badge');
        
        if (notificationList && notificationBadge) {
            const notificationElement = this.createNotificationElement(notification);
            notificationList.insertBefore(notificationElement, notificationList.firstChild);
            
            // Update badge count
            const currentCount = parseInt(notificationBadge.textContent) || 0;
            notificationBadge.textContent = currentCount + 1;
            
            // Show toast
            this.showToast(notification.title, notification.type);
        }
    }

    createNotificationElement(notification) {
        const div = document.createElement('div');
        div.className = 'notification-item';
        div.innerHTML = `
            <i class="fas fa-${this.getNotificationIcon(notification.type)} text-${notification.type}"></i>
            <div class="notification-content">
                <div class="notification-title">${notification.title}</div>
                <div class="notification-text">${notification.message}</div>
                <div class="notification-time">${this.formatTime(notification.time)}</div>
            </div>
        `;
        return div;
    }

    getNotificationIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || 'bell';
    }

    markNotificationsAsRead() {
        const notificationBadge = document.querySelector('.notification-badge');
        if (notificationBadge) {
            notificationBadge.textContent = '0';
        }
    }

    // Loading Methods
    showLoading(message = 'Cargando...') {
        if (this.loadingOverlay) {
            const loadingText = this.loadingOverlay.querySelector('.loading-text');
            if (loadingText) {
                loadingText.textContent = message;
            }
            this.loadingOverlay.classList.add('show');
        }
    }

    hideLoading() {
        if (this.loadingOverlay) {
            this.loadingOverlay.classList.remove('show');
        }
    }

    // Toast Methods
    showToast(message, type = 'info', duration = 5000) {
        const toast = this.createToast(message, type);
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);
        
        // Auto remove
        setTimeout(() => {
            this.removeToast(toast);
        }, duration);
    }

    createToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `fluent-toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas fa-${this.getNotificationIcon(type)}"></i>
                <span>${message}</span>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        return toast;
    }

    removeToast(toast) {
        toast.classList.add('hide');
        setTimeout(() => {
            if (toast.parentElement) {
                toast.parentElement.removeChild(toast);
            }
        }, 300);
    }

    // Form Validation Methods
    validateField(field) {
        const value = field.value.trim();
        const type = field.type;
        const required = field.hasAttribute('required');
        let isValid = true;
        let message = '';

        // Required validation
        if (required && !value) {
            isValid = false;
            message = 'Este campo es requerido';
        }

        // Type-specific validation
        if (value && isValid) {
            switch (type) {
                case 'email':
                    if (!this.isValidEmail(value)) {
                        isValid = false;
                        message = 'Ingrese un email válido';
                    }
                    break;
                case 'tel':
                    if (!this.isValidPhone(value)) {
                        isValid = false;
                        message = 'Ingrese un teléfono válido';
                    }
                    break;
                case 'number':
                    if (isNaN(value) || parseFloat(value) < 0) {
                        isValid = false;
                        message = 'Ingrese un número válido';
                    }
                    break;
            }
        }

        // Custom validation
        const customValidator = field.dataset.validate;
        if (customValidator && value) {
            const validator = this.validators[customValidator];
            if (validator && !validator(value)) {
                isValid = false;
                message = field.dataset.validateMessage || 'Valor inválido';
            }
        }

        this.showFieldValidation(field, isValid, message);
        return isValid;
    }

    validateForm(form) {
        const fields = form.querySelectorAll('input, select, textarea');
        let isValid = true;

        fields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    }

    showFieldValidation(field, isValid, message) {
        const group = field.closest('.form-group') || field.parentElement;
        
        // Remove existing validation
        group.classList.remove('is-valid', 'is-invalid');
        const existingFeedback = group.querySelector('.invalid-feedback, .valid-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }

        // Add new validation
        if (message) {
            group.classList.add(isValid ? 'is-valid' : 'is-invalid');
            
            const feedback = document.createElement('div');
            feedback.className = isValid ? 'valid-feedback' : 'invalid-feedback';
            feedback.textContent = message;
            group.appendChild(feedback);
        }
    }

    // Utility Methods
    isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    isValidPhone(phone) {
        const re = /^[+]?[0-9]{8,15}$/;
        return re.test(phone);
    }

    formatTime(date) {
        const now = new Date();
        const diff = now - date;
        const minutes = Math.floor(diff / 60000);
        
        if (minutes < 1) return 'Ahora';
        if (minutes < 60) return `Hace ${minutes} min`;
        
        const hours = Math.floor(minutes / 60);
        if (hours < 24) return `Hace ${hours}h`;
        
        const days = Math.floor(hours / 24);
        return `Hace ${days}d`;
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat('es-NI', {
            style: 'currency',
            currency: 'NIO'
        }).format(amount);
    }

    formatDate(date) {
        return new Date(date).toLocaleDateString('es-NI', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        });
    }

    formatDateTime(date) {
        return new Date(date).toLocaleDateString('es-NI', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    debounce(func, wait) {
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

    // Event Handlers
    handleGlobalClick(e) {
        // Handle dropdown closes
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                menu.classList.remove('show');
            });
        }

        // Handle modal closes
        if (e.target.classList.contains('modal-backdrop')) {
            this.closeModals();
        }
    }

    handleFormSubmit(e) {
        const form = e.target;
        
        // Show loading
        this.showLoading('Procesando...');
        
        // Hide loading after form submission
        setTimeout(() => {
            this.hideLoading();
        }, 1000);
    }

    handleResize() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        
        if (window.innerWidth <= 1024) {
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('sidebar-collapsed');
        }
    }

    handleKeyboardShortcuts(e) {
        const key = e.ctrlKey ? `ctrl+${e.key.toLowerCase()}` : e.key.toLowerCase();
        
        if (this.shortcuts[key]) {
            e.preventDefault();
            this.shortcuts[key](e);
        }
    }

    // Setup Methods
    setupTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(tooltipTriggerEl => {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    setupDropdowns() {
        const dropdownTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
        dropdownTriggerList.map(dropdownTriggerEl => {
            return new bootstrap.Dropdown(dropdownTriggerEl);
        });
    }

    setupAlerts() {
        const autoDismissAlerts = document.querySelectorAll('.alert.auto-dismiss');
        autoDismissAlerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    }

    // Command Palette
    openCommandPalette() {
        this.showToast('Paleta de comandos (próximamente)', 'info');
    }

    openNewItem() {
        this.showToast('Nuevo elemento (próximamente)', 'info');
    }

    closeModals() {
        document.querySelectorAll('.modal.show').forEach(modal => {
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) {
                bsModal.hide();
            }
        });
    }

    saveForm(e) {
        const form = document.querySelector('form');
        if (form) {
            e.preventDefault();
            form.dispatchEvent(new Event('submit'));
        }
    }

    showKeyboardShortcuts() {
        this.showToast('Atajos de teclado: Ctrl+K (comandos), Ctrl+N (nuevo), Ctrl+S (guardar)', 'info', 8000);
    }

    // User Methods
    getCurrentUser() {
        // This would typically come from a global variable or API call
        return {
            name: 'Usuario',
            role: 'Administrador'
        };
    }

    // Validators
    validators = {
        cedula: (value) => {
            const regex = /^[0-9]{3}-[0-9]{6}-[0-9]{4}[A-Z]$|^[0-9]{14}$/;
            return regex.test(value);
        },
        phone: (value) => {
            const regex = /^[+]?[0-9]{8,15}$/;
            return regex.test(value);
        },
        positive: (value) => {
            return parseFloat(value) > 0;
        }
    };
}

// Initialize the app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.fluentApp = new FluentApp();
});

// Global utilities
window.PulperiaUtils = {
    formatCurrency: (amount) => window.fluentApp.formatCurrency(amount),
    formatDate: (date) => window.fluentApp.formatDate(date),
    formatDateTime: (date) => window.fluentApp.formatDateTime(date),
    showToast: (message, type) => window.fluentApp.showToast(message, type),
    showLoading: (message) => window.fluentApp.showLoading(message),
    hideLoading: () => window.fluentApp.hideLoading()
};

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = FluentApp;
}


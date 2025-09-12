// app.js - Scripts globales para toda la aplicación
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips de Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Inicializar popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Confirmación para acciones destructivas
    const confirmButtons = document.querySelectorAll('[data-confirm]');
    confirmButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm') || '¿Está seguro de realizar esta acción?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // Auto-ocultar alerts después de 5 segundos
    const autoDismissAlerts = document.querySelectorAll('.alert.auto-dismiss');
    autoDismissAlerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Formularios con confirmación de navegación
    const forms = document.querySelectorAll('form[data-unsaved-changes]');
    let formChanged = false;

    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('change', () => {
                formChanged = true;
            });
        });

        form.addEventListener('submit', () => {
            formChanged = false;
        });
    });

    window.addEventListener('beforeunload', (e) => {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Función global para formatear moneda
    window.formatCurrency = function(amount) {
        return new Intl.NumberFormat('es-NI', {
            style: 'currency',
            currency: 'NIO'
        }).format(amount);
    };

    // Función global para formatear fecha
    window.formatDate = function(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('es-NI', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        });
    };

    // Función global para formatear fecha y hora
    window.formatDateTime = function(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('es-NI', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    };
});

// Utilidades globales
const PulperiaUtils = {
    // Calcular impuestos
    calculateTax: function(subtotal, taxRate = 0.15) {
        return subtotal * taxRate;
    },

    // Calcular total
    calculateTotal: function(subtotal, taxRate = 0.15) {
        return subtotal + this.calculateTax(subtotal, taxRate);
    },

    // Validar cédula nicaragüense
    validateCedula: function(cedula) {
        const regex = /^[0-9]{3}-[0-9]{6}-[0-9]{4}[A-Z]$|^[0-9]{14}$/;
        return regex.test(cedula);
    },

    // Validar teléfono
    validatePhone: function(phone) {
        const regex = /^[+]?[0-9]{8,15}$/;
        return regex.test(phone);
    },

    // Debounce para búsquedas
    debounce: function(func, wait) {
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
};
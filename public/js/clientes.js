// clientes.js - Scripts para gestión de clientes
document.addEventListener('DOMContentLoaded', function() {
    // Validación de cédula
    const cedulaInput = document.getElementById('cedula');
    if (cedulaInput) {
        cedulaInput.addEventListener('blur', function() {
            if (this.value && !PulperiaUtils.validateCedula(this.value)) {
                alert('El formato de la cédula no es válido. Formato esperado: 001-030599-1000A o 14 dígitos numéricos.');
                this.focus();
            }
        });
    }

    // Validación de teléfono
    const telefonoInput = document.getElementById('telefono');
    if (telefonoInput) {
        telefonoInput.addEventListener('blur', function() {
            if (this.value && !PulperiaUtils.validatePhone(this.value)) {
                alert('Por favor ingrese un número de teléfono válido.');
                this.focus();
            }
        });
    }

    // Búsqueda de clientes
    const clientSearch = document.getElementById('clientSearch');
    if (clientSearch) {
        const searchHandler = PulperiaUtils.debounce(function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#clientsTable tbody tr');
            
            rows.forEach(row => {
                const clientName = row.cells[0].textContent.toLowerCase();
                const clientCedula = row.cells[1].textContent.toLowerCase();
                const clientPhone = row.cells[2].textContent.toLowerCase();
                
                if (clientName.includes(searchTerm) || clientCedula.includes(searchTerm) || clientPhone.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }, 300);

        clientSearch.addEventListener('input', searchHandler);
    }

    // Filtro por saldo
    const balanceFilter = document.getElementById('balanceFilter');
    if (balanceFilter) {
        balanceFilter.addEventListener('change', function() {
            const filterValue = this.value;
            const rows = document.querySelectorAll('#clientsTable tbody tr');
            
            rows.forEach(row => {
                const balance = parseFloat(row.cells[3].textContent.replace('C$ ', '').replace(',', ''));
                let showRow = true;
                
                switch(filterValue) {
                    case 'con-saldo':
                        showRow = balance > 0;
                        break;
                    case 'sin-saldo':
                        showRow = balance === 0;
                        break;
                    case 'saldo-alto':
                        showRow = balance > 1000;
                        break;
                }
                
                row.style.display = showRow ? '' : 'none';
            });
        });
    }

    // Cálculo automático de saldo al agregar crédito/abono
    const calculateClientBalance = () => {
        const creditos = Array.from(document.querySelectorAll('.credito-monto')).reduce((sum, el) => {
            return sum + parseFloat(el.textContent.replace('C$ ', '').replace(',', ''));
        }, 0);
        
        const abonos = Array.from(document.querySelectorAll('.abono-monto')).reduce((sum, el) => {
            return sum + parseFloat(el.textContent.replace('C$ ', '').replace(',', ''));
        }, 0);
        
        const saldo = creditos - abonos;
        document.getElementById('totalSaldo').textContent = formatCurrency(saldo);
        
        const saldoBadge = document.getElementById('saldoBadge');
        if (saldoBadge) {
            saldoBadge.className = `badge bg-${saldo > 0 ? 'warning' : 'success'}`;
            saldoBadge.textContent = formatCurrency(saldo);
        }
    };

    // Inicializar cálculos si estamos en la página de detalle
    if (document.getElementById('totalSaldo')) {
        calculateClientBalance();
    }
});
// JavaScript para la gestión de créditos
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const clientFilter = document.getElementById('clientFilter');
    const tableBody = document.getElementById('creditsTableBody');
    
    function filterCredits() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedStatus = statusFilter.value;
        const selectedClient = clientFilter.value;
        
        const rows = tableBody.querySelectorAll('tr');
        
        rows.forEach(row => {
            if (row.querySelector('.empty-state')) return;
            
            const creditId = row.cells[0].textContent.toLowerCase();
            const clientName = row.cells[1].textContent.toLowerCase();
            const clientPhone = row.cells[1].textContent.toLowerCase();
            
            const statusBadge = row.cells[5].querySelector('.status-badge');
            const statusClass = statusBadge ? statusBadge.className : '';
            
            let showRow = true;
            
            // Filtro de búsqueda
            if (searchTerm) {
                const matchesSearch = creditId.includes(searchTerm) || 
                                    clientName.includes(searchTerm) || 
                                    clientPhone.includes(searchTerm);
                if (!matchesSearch) {
                    showRow = false;
                }
            }
            
            // Filtro de estado
            if (selectedStatus) {
                const hasStatusClass = statusClass.includes(`status-${selectedStatus}`);
                if (!hasStatusClass) {
                    showRow = false;
                }
            }
            
            // Filtro de cliente
            if (selectedClient) {
                const clientId = row.cells[1].querySelector('[data-client-id]');
                if (!clientId || clientId.dataset.clientId !== selectedClient) {
                    showRow = false;
                }
            }
            
            row.style.display = showRow ? '' : 'none';
        });
    }
    
    // Event listeners
    if (searchInput) {
        searchInput.addEventListener('input', filterCredits);
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', filterCredits);
    }
    
    if (clientFilter) {
        clientFilter.addEventListener('change', filterCredits);
    }
    
    // Función para actualizar resumen en tiempo real
    function updateSummary() {
        const visibleRows = Array.from(tableBody.querySelectorAll('tr')).filter(row => 
            !row.querySelector('.empty-state') && row.style.display !== 'none'
        );
        
        // Actualizar contadores si es necesario
        // Esta función se puede expandir para cálculos en tiempo real
    }
    
    // Llamar a updateSummary cuando cambien los filtros
    [searchInput, statusFilter, clientFilter].forEach(element => {
        if (element) {
            element.addEventListener('input', updateSummary);
            element.addEventListener('change', updateSummary);
        }
    });
});


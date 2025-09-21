// JavaScript unificado para todas las vistas

// Función para filtrar tablas
function filterTable(searchInputId, tableBodyId, searchColumns = [1]) {
    const searchInput = document.getElementById(searchInputId);
    const tableBody = document.getElementById(tableBodyId);
    
    if (!searchInput || !tableBody) return;
    
    function filterRows() {
        const searchTerm = searchInput.value.toLowerCase();
        const rows = tableBody.querySelectorAll('tr');
        
        rows.forEach(row => {
            if (row.querySelector('.empty-state')) return;
            
            let matches = false;
            
            if (searchTerm === '') {
                matches = true;
            } else {
                searchColumns.forEach(columnIndex => {
                    const cellText = row.cells[columnIndex]?.textContent.toLowerCase() || '';
                    if (cellText.includes(searchTerm)) {
                        matches = true;
                    }
                });
            }
            
            row.style.display = matches ? '' : 'none';
        });
    }
    
    searchInput.addEventListener('input', filterRows);
}

// Función para filtrar con múltiples criterios
function filterTableAdvanced(config) {
    const { searchInputId, tableBodyId, filters = {} } = config;
    
    const searchInput = document.getElementById(searchInputId);
    const tableBody = document.getElementById(tableBodyId);
    
    if (!searchInput || !tableBody) return;
    
    function filterRows() {
        const searchTerm = searchInput.value.toLowerCase();
        const rows = tableBody.querySelectorAll('tr');
        
        rows.forEach(row => {
            if (row.querySelector('.empty-state')) return;
            
            let showRow = true;
            
            // Filtro de búsqueda
            if (searchTerm) {
                const searchColumns = filters.searchColumns || [1];
                let matchesSearch = false;
                
                searchColumns.forEach(columnIndex => {
                    const cellText = row.cells[columnIndex]?.textContent.toLowerCase() || '';
                    if (cellText.includes(searchTerm)) {
                        matchesSearch = true;
                    }
                });
                
                if (!matchesSearch) {
                    showRow = false;
                }
            }
            
            // Filtros adicionales
            Object.keys(filters).forEach(filterKey => {
                if (filterKey === 'searchColumns') return;
                
                const filterElement = document.getElementById(filters[filterKey].elementId);
                if (!filterElement) return;
                
                const selectedValue = filterElement.value;
                if (!selectedValue) return;
                
                const { columnIndex, className } = filters[filterKey];
                const cell = row.cells[columnIndex];
                
                if (cell) {
                    const element = cell.querySelector(`.${className}`);
                    if (element) {
                        const hasClass = element.className.includes(selectedValue);
                        if (!hasClass) {
                            showRow = false;
                        }
                    }
                }
            });
            
            row.style.display = showRow ? '' : 'none';
        });
    }
    
    // Event listeners
    searchInput.addEventListener('input', filterRows);
    
    Object.keys(filters).forEach(filterKey => {
        if (filterKey === 'searchColumns') return;
        
        const filterElement = document.getElementById(filters[filterKey].elementId);
        if (filterElement) {
            filterElement.addEventListener('change', filterRows);
        }
    });
}

// Función para filtrar grids de tarjetas
function filterGrid(searchInputId, gridId) {
    const searchInput = document.getElementById(searchInputId);
    const grid = document.getElementById(gridId);
    
    if (!searchInput || !grid) return;
    
    function filterCards() {
        const searchTerm = searchInput.value.toLowerCase();
        const cards = grid.querySelectorAll('.category-card, .product-card, .supplier-card');
        
        cards.forEach(card => {
            if (card.querySelector('.empty-state')) return;
            
            const cardText = card.textContent.toLowerCase();
            const matches = cardText.includes(searchTerm);
            card.style.display = matches ? '' : 'none';
        });
    }
    
    searchInput.addEventListener('input', filterCards);
}

// Inicialización automática para vistas específicas
document.addEventListener('DOMContentLoaded', function() {
    // Productos
    if (document.getElementById('productsTableBody')) {
        filterTableAdvanced({
            searchInputId: 'searchInput',
            tableBodyId: 'productsTableBody',
            searchColumns: [1],
            category: {
                elementId: 'categoryFilter',
                columnIndex: 2,
                className: 'category-name'
            },
            stock: {
                elementId: 'stockFilter',
                columnIndex: 5,
                className: 'stock-badge'
            }
        });
    }
    
    // Categorías
    if (document.getElementById('categoriesGrid')) {
        filterGrid('searchInput', 'categoriesGrid');
    }
    
    // Proveedores
    if (document.getElementById('suppliersTableBody')) {
        filterTableAdvanced({
            searchInputId: 'searchInput',
            tableBodyId: 'suppliersTableBody',
            searchColumns: [0, 1, 2, 3, 4],
            status: {
                elementId: 'statusFilter',
                columnIndex: 5,
                className: 'status-badge'
            }
        });
    }
    
    // Ventas
    if (document.getElementById('salesTableBody')) {
        filterTableAdvanced({
            searchInputId: 'searchInput',
            tableBodyId: 'salesTableBody',
            searchColumns: [0, 2, 3],
            status: {
                elementId: 'statusFilter',
                columnIndex: 8,
                className: 'status-badge'
            }
        });
    }
    
    // Compras
    if (document.getElementById('purchasesTableBody')) {
        filterTableAdvanced({
            searchInputId: 'searchInput',
            tableBodyId: 'purchasesTableBody',
            searchColumns: [0, 2],
            status: {
                elementId: 'statusFilter',
                columnIndex: 7,
                className: 'status-badge'
            }
        });
    }
    
    // Créditos
    if (document.getElementById('creditsTableBody')) {
        filterTableAdvanced({
            searchInputId: 'searchInput',
            tableBodyId: 'creditsTableBody',
            searchColumns: [0, 1],
            status: {
                elementId: 'statusFilter',
                columnIndex: 5,
                className: 'status-badge'
            },
            client: {
                elementId: 'clientFilter',
                columnIndex: 1,
                className: 'client-name'
            }
        });
    }
});










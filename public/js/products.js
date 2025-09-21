// JavaScript para la gestión de productos
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const stockFilter = document.getElementById('stockFilter');
    const tableBody = document.getElementById('productsTableBody');
    
    function filterProducts() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value;
        const selectedStock = stockFilter.value;
        
        const rows = tableBody.querySelectorAll('tr');
        
        rows.forEach(row => {
            if (row.querySelector('.empty-state')) return;
            
            const productName = row.cells[1].textContent.toLowerCase();
            const category = row.cells[2].textContent;
            const stockBadge = row.cells[5].querySelector('.stock-badge');
            const stockClass = stockBadge ? stockBadge.className : '';
            
            let showRow = true;
            
            // Filtro de búsqueda
            if (searchTerm && !productName.includes(searchTerm)) {
                showRow = false;
            }
            
            // Filtro de categoría
            if (selectedCategory && !category.includes(selectedCategory)) {
                showRow = false;
            }
            
            // Filtro de stock
            if (selectedStock) {
                const hasStockClass = stockClass.includes(`stock-${selectedStock}`);
                if (!hasStockClass) {
                    showRow = false;
                }
            }
            
            row.style.display = showRow ? '' : 'none';
        });
    }
    
    // Event listeners
    if (searchInput) {
        searchInput.addEventListener('input', filterProducts);
    }
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterProducts);
    }
    
    if (stockFilter) {
        stockFilter.addEventListener('change', filterProducts);
    }
});


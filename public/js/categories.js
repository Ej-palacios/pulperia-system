// JavaScript para la gestión de categorías
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const categoriesGrid = document.getElementById('categoriesGrid');
    
    function filterCategories() {
        const searchTerm = searchInput.value.toLowerCase();
        const cards = categoriesGrid.querySelectorAll('.category-card');
        
        cards.forEach(card => {
            if (card.querySelector('.empty-state')) return;
            
            const categoryName = card.querySelector('.category-name').textContent.toLowerCase();
            const categoryDescription = card.querySelector('.category-description').textContent.toLowerCase();
            
            const matches = categoryName.includes(searchTerm) || categoryDescription.includes(searchTerm);
            card.style.display = matches ? '' : 'none';
        });
    }
    
    if (searchInput) {
        searchInput.addEventListener('input', filterCategories);
    }
});


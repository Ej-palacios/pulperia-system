@props([
    'headers' => [],
    'rows' => [],
    'striped' => false,
    'hover' => true,
    'bordered' => false,
    'small' => false,
    'responsive' => true,
    'sortable' => false,
    'searchable' => false,
    'pagination' => false,
    'perPage' => 10,
    'currentPage' => 1,
    'total' => 0,
    'emptyMessage' => 'No hay datos disponibles',
    'loading' => false
])

@php
    $tableClass = 'fluent-table';
    
    if ($striped) $tableClass .= ' table-striped';
    if ($hover) $tableClass .= ' table-hover';
    if ($bordered) $tableClass .= ' table-bordered';
    if ($small) $tableClass .= ' table-sm';
    if ($sortable) $tableClass .= ' table-sortable';
@endphp

<div class="fluent-table-container">
    @if($searchable)
        <div class="table-toolbar">
            <div class="table-search">
                <x-fluent-input 
                    type="search" 
                    placeholder="Buscar..." 
                    class="table-search-input"
                    data-table-search
                />
            </div>
            <div class="table-actions">
                @if($pagination)
                    <div class="table-pagination-info">
                        Mostrando {{ (($currentPage - 1) * $perPage) + 1 }} - {{ min($currentPage * $perPage, $total) }} de {{ $total }} registros
                    </div>
                @endif
            </div>
        </div>
    @endif
    
    <div class="table-wrapper {{ $responsive ? 'table-responsive' : '' }}">
        @if($loading)
            <div class="table-loading">
                <div class="loading-spinner">
                    <div class="spinner"></div>
                    <div class="loading-text">Cargando datos...</div>
                </div>
            </div>
        @else
            <table class="{{ $tableClass }}" data-table-id="{{ uniqid() }}">
                @if(!empty($headers))
                    <thead>
                        <tr>
                            @foreach($headers as $header)
                                <th class="table-header {{ $header['sortable'] ?? false ? 'sortable' : '' }}" 
                                    data-sort="{{ $header['key'] ?? '' }}">
                                    <div class="header-content">
                                        <span class="header-text">{{ $header['label'] ?? $header }}</span>
                                        @if(($header['sortable'] ?? false) || $sortable)
                                            <div class="sort-indicators">
                                                <i class="fas fa-sort sort-icon"></i>
                                                <i class="fas fa-sort-up sort-up" style="display: none;"></i>
                                                <i class="fas fa-sort-down sort-down" style="display: none;"></i>
                                            </div>
                                        @endif
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                @endif
                
                <tbody>
                    @if(!empty($rows))
                        @foreach($rows as $index => $row)
                            <tr class="table-row" data-row-index="{{ $index }}">
                                @if(is_array($row))
                                    @foreach($row as $cell)
                                        <td class="table-cell">
                                            @if(is_array($cell))
                                                {!! $cell['content'] ?? '' !!}
                                            @else
                                                {{ $cell }}
                                            @endif
                                        </td>
                                    @endforeach
                                @else
                                    {{ $row }}
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr class="empty-row">
                            <td colspan="{{ count($headers) }}" class="empty-cell">
                                <div class="empty-state">
                                    <i class="fas fa-inbox empty-icon"></i>
                                    <div class="empty-message">{{ $emptyMessage }}</div>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        @endif
    </div>
    
    @if($pagination && $total > $perPage)
        <div class="table-pagination">
            <div class="pagination-info">
                <span>Página {{ $currentPage }} de {{ ceil($total / $perPage) }}</span>
            </div>
            <div class="pagination-controls">
                <button class="pagination-btn {{ $currentPage <= 1 ? 'disabled' : '' }}" 
                        data-page="{{ $currentPage - 1 }}" 
                        {{ $currentPage <= 1 ? 'disabled' : '' }}>
                    <i class="fas fa-chevron-left"></i>
                </button>
                
                @for($i = max(1, $currentPage - 2); $i <= min(ceil($total / $perPage), $currentPage + 2); $i++)
                    <button class="pagination-btn {{ $i === $currentPage ? 'active' : '' }}" 
                            data-page="{{ $i }}">
                        {{ $i }}
                    </button>
                @endfor
                
                <button class="pagination-btn {{ $currentPage >= ceil($total / $perPage) ? 'disabled' : '' }}" 
                        data-page="{{ $currentPage + 1 }}" 
                        {{ $currentPage >= ceil($total / $perPage) ? 'disabled' : '' }}>
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    @endif
</div>

<style>
.fluent-table-container {
    background-color: var(--fluent-white);
    border-radius: var(--fluent-radius-lg);
    border: 1px solid var(--fluent-gray-30);
    overflow: hidden;
    box-shadow: var(--fluent-shadow-sm);
}

.table-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--fluent-space-md) var(--fluent-space-lg);
    border-bottom: 1px solid var(--fluent-gray-30);
    background-color: var(--fluent-gray-10);
}

.table-search {
    flex: 1;
    max-width: 300px;
}

.table-search-input {
    width: 100%;
}

.table-actions {
    display: flex;
    align-items: center;
    gap: var(--fluent-space-md);
}

.table-pagination-info {
    font-size: var(--fluent-font-size-sm);
    color: var(--fluent-gray-80);
}

.table-wrapper {
    overflow-x: auto;
}

.fluent-table {
    width: 100%;
    border-collapse: collapse;
    font-size: var(--fluent-font-size-sm);
}

.table-header {
    background-color: var(--fluent-gray-10);
    color: var(--fluent-gray-120);
    font-weight: 600;
    padding: var(--fluent-space-md) var(--fluent-space-lg);
    text-align: left;
    border-bottom: 1px solid var(--fluent-gray-30);
    position: relative;
}

.table-header.sortable {
    cursor: pointer;
    user-select: none;
}

.table-header.sortable:hover {
    background-color: var(--fluent-gray-20);
}

.header-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: var(--fluent-space-sm);
}

.header-text {
    flex: 1;
}

.sort-indicators {
    display: flex;
    flex-direction: column;
    gap: 2px;
    opacity: 0.5;
    transition: opacity var(--fluent-transition-fast);
}

.table-header.sortable:hover .sort-indicators {
    opacity: 1;
}

.table-header.sorted .sort-indicators {
    opacity: 1;
}

.sort-icon {
    font-size: var(--fluent-font-size-xs);
}

.sort-up,
.sort-down {
    font-size: var(--fluent-font-size-xs);
    color: var(--fluent-primary);
}

.table-row {
    transition: background-color var(--fluent-transition-fast);
}

.table-row:hover {
    background-color: var(--fluent-gray-10);
}

.table-row:nth-child(even) {
    background-color: var(--fluent-gray-5);
}

.table-cell {
    padding: var(--fluent-space-md) var(--fluent-space-lg);
    border-bottom: 1px solid var(--fluent-gray-20);
    vertical-align: middle;
}

.table-cell:first-child {
    border-left: 3px solid transparent;
}

.table-row:hover .table-cell:first-child {
    border-left-color: var(--fluent-primary);
}

/* Empty state */
.empty-row {
    background-color: var(--fluent-white);
}

.empty-cell {
    text-align: center;
    padding: var(--fluent-space-2xl);
}

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--fluent-space-md);
    color: var(--fluent-gray-80);
}

.empty-icon {
    font-size: var(--fluent-font-size-3xl);
    opacity: 0.5;
}

.empty-message {
    font-size: var(--fluent-font-size-base);
    font-weight: 500;
}

/* Loading state */
.table-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--fluent-space-2xl);
    background-color: var(--fluent-white);
}

.loading-spinner {
    text-align: center;
}

.loading-spinner .spinner {
    width: 32px;
    height: 32px;
    border: 3px solid var(--fluent-gray-30);
    border-top: 3px solid var(--fluent-primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto var(--fluent-space-md);
}

.loading-text {
    color: var(--fluent-gray-80);
    font-size: var(--fluent-font-size-sm);
    font-weight: 500;
}

/* Pagination */
.table-pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--fluent-space-md) var(--fluent-space-lg);
    border-top: 1px solid var(--fluent-gray-30);
    background-color: var(--fluent-gray-10);
}

.pagination-info {
    font-size: var(--fluent-font-size-sm);
    color: var(--fluent-gray-80);
}

.pagination-controls {
    display: flex;
    align-items: center;
    gap: var(--fluent-space-xs);
}

.pagination-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border: 1px solid var(--fluent-gray-40);
    background-color: var(--fluent-white);
    color: var(--fluent-gray-100);
    border-radius: var(--fluent-radius-sm);
    cursor: pointer;
    transition: all var(--fluent-transition-fast);
    font-size: var(--fluent-font-size-sm);
    font-weight: 500;
}

.pagination-btn:hover:not(.disabled) {
    background-color: var(--fluent-gray-20);
    border-color: var(--fluent-gray-50);
}

.pagination-btn.active {
    background-color: var(--fluent-primary);
    border-color: var(--fluent-primary);
    color: var(--fluent-white);
}

.pagination-btn.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}

/* Responsive */
@media (max-width: 768px) {
    .table-toolbar {
        flex-direction: column;
        gap: var(--fluent-space-md);
        align-items: stretch;
    }
    
    .table-search {
        max-width: none;
    }
    
    .table-pagination {
        flex-direction: column;
        gap: var(--fluent-space-md);
        align-items: center;
    }
    
    .pagination-controls {
        flex-wrap: wrap;
        justify-content: center;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .fluent-table-container {
        background-color: var(--fluent-gray-20);
        border-color: var(--fluent-gray-40);
    }
    
    .table-toolbar {
        background-color: var(--fluent-gray-30);
        border-color: var(--fluent-gray-40);
    }
    
    .table-header {
        background-color: var(--fluent-gray-30);
        border-color: var(--fluent-gray-40);
    }
    
    .table-row:hover {
        background-color: var(--fluent-gray-30);
    }
    
    .table-row:nth-child(even) {
        background-color: var(--fluent-gray-25);
    }
    
    .table-cell {
        border-color: var(--fluent-gray-40);
    }
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Table search functionality
    const searchInputs = document.querySelectorAll('[data-table-search]');
    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            const table = this.closest('.fluent-table-container').querySelector('.fluent-table');
            const searchTerm = this.value.toLowerCase();
            const rows = table.querySelectorAll('.table-row');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
    
    // Table sorting functionality
    const sortableHeaders = document.querySelectorAll('.table-header.sortable');
    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const table = this.closest('.fluent-table');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('.table-row'));
            const columnIndex = Array.from(this.parentNode.children).indexOf(this);
            const sortKey = this.dataset.sort;
            
            // Toggle sort direction
            const isAscending = !this.classList.contains('sorted-asc');
            
            // Reset all headers
            sortableHeaders.forEach(h => {
                h.classList.remove('sorted-asc', 'sorted-desc');
                h.querySelector('.sort-icon').style.display = '';
                h.querySelector('.sort-up').style.display = 'none';
                h.querySelector('.sort-down').style.display = 'none';
            });
            
            // Set current header state
            this.classList.add(isAscending ? 'sorted-asc' : 'sorted-desc');
            this.querySelector('.sort-icon').style.display = 'none';
            this.querySelector(isAscending ? '.sort-up' : '.sort-down').style.display = '';
            
            // Sort rows
            rows.sort((a, b) => {
                const aValue = a.children[columnIndex].textContent.trim();
                const bValue = b.children[columnIndex].textContent.trim();
                
                // Try to parse as numbers
                const aNum = parseFloat(aValue);
                const bNum = parseFloat(bValue);
                
                if (!isNaN(aNum) && !isNaN(bNum)) {
                    return isAscending ? aNum - bNum : bNum - aNum;
                }
                
                // Sort as strings
                return isAscending ? 
                    aValue.localeCompare(bValue) : 
                    bValue.localeCompare(aValue);
            });
            
            // Reorder rows in DOM
            rows.forEach(row => tbody.appendChild(row));
        });
    });
    
    // Pagination functionality
    const paginationBtns = document.querySelectorAll('.pagination-btn[data-page]');
    paginationBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const page = this.dataset.page;
            if (page && !this.classList.contains('disabled')) {
                // This would typically trigger a page reload or AJAX request
                console.log('Navigate to page:', page);
            }
        });
    });
});
</script>


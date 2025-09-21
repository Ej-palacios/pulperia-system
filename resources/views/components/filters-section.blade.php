@props(['action' => '', 'method' => 'GET'])

<div class="filters-section mb-4">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>
                Filtros de Búsqueda
            </h6>
        </div>
        <div class="card-body">
            <form method="{{ $method }}" action="{{ $action }}" class="row g-3">
                {{ $slot }}
            </form>
        </div>
    </div>
</div>

<style>
.filters-section .card {
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.filters-section .card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 1rem 1.25rem;
}

.filters-section .card-title {
    color: #495057;
    font-weight: 600;
    font-size: 0.95rem;
}

.filters-section .card-body {
    padding: 1.25rem;
}

.filters-section .form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.filters-section .form-control,
.filters-section .form-select {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.filters-section .form-control:focus,
.filters-section .form-select:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.filters-section .btn {
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    font-weight: 500;
}

.filters-section .btn-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.filters-section .btn-primary:hover {
    background-color: #0b5ed7;
    border-color: #0a58ca;
}

.filters-section .btn-outline-secondary {
    color: #6c757d;
    border-color: #6c757d;
}

.filters-section .btn-outline-secondary:hover {
    background-color: #6c757d;
    border-color: #6c757d;
    color: #fff;
}
</style>

@props([
    'loading' => false,
    'striped' => true
])

<div class="fluent-table-wrapper">
    <table {{ $attributes->merge(['class' => 'fluent-table' . ($striped ? ' table-striped' : '')]) }}>
        @if (isset($header))
            <thead class="fluent-table-header">
                <tr>
                    {{ $header }}
                </tr>
            </thead>
        @endif

        <tbody class="fluent-table-body">
            {{ $slot }}
        </tbody>

        @if (isset($footer))
            <tfoot class="fluent-table-footer">
                <tr>
                    {{ $footer }}
                </tr>
            </tfoot>
        @endif
    </table>

    @if ($loading)
        <div class="fluent-table-loading-overlay">
            <div class="spinner"></div>
            <p>Cargando datos...</p>
        </div>
    @endif
</div>

<style>
.fluent-table-wrapper {
    position: relative;
    overflow-x: auto;
    border: 1px solid var(--fluent-gray-30);
    border-radius: var(--fluent-radius-md);
}

.fluent-table {
    width: 100%;
    border-collapse: collapse;
    background-color: var(--fluent-white);
}

.fluent-table-header th {
    padding: var(--fluent-space-md) var(--fluent-space-lg);
    background-color: var(--fluent-gray-20);
    border-bottom: 2px solid var(--fluent-gray-40);
    font-weight: 600;
    text-align: left;
    font-size: var(--fluent-font-size-sm);
    color: var(--fluent-gray-100);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.fluent-table-body td {
    padding: var(--fluent-space-md) var(--fluent-space-lg);
    border-bottom: 1px solid var(--fluent-gray-20);
    font-size: var(--fluent-font-size-sm);
    color: var(--fluent-gray-110);
    vertical-align: middle;
}

.fluent-table.table-striped .fluent-table-body tr:nth-child(even) {
    background-color: var(--fluent-gray-10);
}

.fluent-table .fluent-table-body tr:hover {
    background-color: var(--fluent-primary-light);
}

.fluent-table-footer td {
    padding: var(--fluent-space-md) var(--fluent-space-lg);
    background-color: var(--fluent-gray-20);
    font-weight: 600;
    font-size: var(--fluent-font-size-sm);
}

.fluent-table-loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(255, 255, 255, 0.8);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: var(--fluent-space-md);
}

.fluent-table-loading-overlay .spinner {
    width: 24px;
    height: 24px;
    border: 3px solid var(--fluent-primary-light);
    border-top-color: var(--fluent-primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.fluent-table-loading-overlay p {
    font-size: var(--fluent-font-size-sm);
    color: var(--fluent-gray-100);
    margin: 0;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

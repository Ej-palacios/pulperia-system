@props([
    'type' => 'info',
    'message' => '',
    'dismissible' => true,
    'autoDismiss' => false,
    'icon' => null
])

@php
    $typeClasses = [
        'success' => 'fluent-alert-success',
        'error' => 'fluent-alert-error',
        'warning' => 'fluent-alert-warning',
        'info' => 'fluent-alert-info'
    ];
    
    $icons = [
        'success' => 'check-circle',
        'error' => 'exclamation-circle',
        'warning' => 'exclamation-triangle',
        'info' => 'info-circle'
    ];
    
    $alertClass = $typeClasses[$type] ?? $typeClasses['info'];
    $alertIcon = $icon ?? $icons[$type] ?? $icons['info'];
@endphp

<div class="fluent-alert {{ $alertClass }} {{ $dismissible ? 'dismissible' : '' }} {{ $autoDismiss ? 'auto-dismiss' : '' }}" 
     role="alert" 
     data-bs-dismiss="alert">
    <div class="alert-content">
        <div class="alert-icon">
            <i class="fas fa-{{ $alertIcon }}"></i>
        </div>
        <div class="alert-body">
            @if($message)
                <div class="alert-message">{{ $message }}</div>
            @else
                {{ $slot }}
            @endif
        </div>
        @if($dismissible)
            <button type="button" class="alert-close" data-bs-dismiss="alert" aria-label="Cerrar">
                <i class="fas fa-times"></i>
            </button>
        @endif
    </div>
</div>

<style>
.fluent-alert {
    display: flex;
    align-items: flex-start;
    gap: var(--fluent-space-sm);
    padding: var(--fluent-space-md);
    border-radius: var(--fluent-radius-lg);
    border: 1px solid;
    margin-bottom: var(--fluent-space-md);
    font-size: var(--fluent-font-size-sm);
    line-height: 1.5;
    position: relative;
    transition: all var(--fluent-transition-normal);
}

.fluent-alert-success {
    background-color: var(--fluent-success-light);
    border-color: var(--fluent-success);
    color: var(--fluent-success-hover);
}

.fluent-alert-error {
    background-color: var(--fluent-error-light);
    border-color: var(--fluent-error);
    color: var(--fluent-error-hover);
}

.fluent-alert-warning {
    background-color: var(--fluent-warning-light);
    border-color: var(--fluent-warning);
    color: var(--fluent-warning-hover);
}

.fluent-alert-info {
    background-color: var(--fluent-info-light);
    border-color: var(--fluent-info);
    color: var(--fluent-info-hover);
}

.alert-content {
    display: flex;
    align-items: flex-start;
    gap: var(--fluent-space-sm);
    width: 100%;
}

.alert-icon {
    flex-shrink: 0;
    font-size: var(--fluent-font-size-lg);
    margin-top: 2px;
}

.alert-body {
    flex: 1;
    min-width: 0;
}

.alert-message {
    font-weight: 500;
}

.alert-close {
    background: none;
    border: none;
    color: inherit;
    cursor: pointer;
    padding: var(--fluent-space-xs);
    border-radius: var(--fluent-radius-sm);
    transition: all var(--fluent-transition-fast);
    flex-shrink: 0;
    margin-top: -2px;
}

.alert-close:hover {
    background-color: rgba(0, 0, 0, 0.1);
}

.fluent-alert.dismissible {
    padding-right: var(--fluent-space-xl);
}

.fluent-alert ul {
    margin: var(--fluent-space-sm) 0 0 0;
    padding-left: var(--fluent-space-lg);
}

.fluent-alert li {
    margin-bottom: var(--fluent-space-xs);
}

.fluent-alert li:last-child {
    margin-bottom: 0;
}

/* Animation for dismissible alerts */
.fluent-alert.fade {
    opacity: 0;
    transition: opacity var(--fluent-transition-normal);
}

.fluent-alert.show {
    opacity: 1;
}

/* Auto-dismiss animation */
.fluent-alert.auto-dismiss {
    animation: slideInRight var(--fluent-transition-normal) ease-out;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>


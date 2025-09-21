@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'medium',
    'disabled' => false,
    'loading' => false,
    'icon' => null,
    'iconPosition' => 'left',
    'href' => null,
    'target' => null,
    'confirm' => null,
    'confirmMessage' => '¿Está seguro de realizar esta acción?'
])

@php
    $variants = [
        'primary' => 'fluent2-button-primary',
        'secondary' => 'fluent2-button-secondary',
        'success' => 'fluent2-button-success',
        'warning' => 'fluent2-button-warning',
        'error' => 'fluent2-button-error',
        'ghost' => 'fluent2-button-ghost',
        'link' => 'fluent2-button-link'
    ];
    
    $sizes = [
        'small' => 'fluent2-button-small',
        'medium' => '',
        'large' => 'fluent2-button-large'
    ];
    
    $buttonClass = 'fluent2-button ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['medium']);
    
    if ($disabled) {
        $buttonClass .= ' disabled';
    }
    
    if ($loading) {
        $buttonClass .= ' loading';
    }
    
    $tag = $href ? 'a' : 'button';
    $attributes = $attributes->merge([
        'class' => $buttonClass,
        'type' => $href ? null : $type,
        'href' => $href,
        'target' => $target,
        'disabled' => $disabled || $loading
    ]);
    
    if ($confirm) {
        $attributes = $attributes->merge([
            'data-confirm' => $confirmMessage,
            'onclick' => "return confirm('{$confirmMessage}')"
        ]);
    }
@endphp

<{{ $tag }} {{ $attributes }}>
    @if($loading)
        <div class="fluent2-spinner" style="width: 16px; height: 16px; border-width: 2px;"></div>
    @elseif($icon && $iconPosition === 'left')
        <x-fluent2-icon :name="$icon" size="small" />
    @endif
    
    <span class="btn-text">{{ $slot }}</span>
    
    @if($icon && $iconPosition === 'right')
        <x-fluent2-icon :name="$icon" size="small" />
    @endif
</{{ $tag }}>

<style>
.fluent2-button {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--fluent2-space-s);
    padding: var(--fluent2-space-s) var(--fluent2-space-l);
    font-family: var(--fluent2-font-family);
    font-size: var(--fluent2-font-size-body);
    font-weight: var(--fluent2-font-weight-medium);
    line-height: var(--fluent2-line-height-body);
    text-align: center;
    text-decoration: none;
    white-space: nowrap;
    vertical-align: middle;
    cursor: pointer;
    user-select: none;
    border: 1px solid transparent;
    border-radius: var(--fluent2-radius-medium);
    transition: all var(--fluent2-duration-fast) var(--fluent2-easing-standard);
    box-shadow: var(--fluent2-shadow-2);
    overflow: hidden;
    min-height: 32px;
    position: relative;
    overflow: hidden;
    white-space: nowrap;
    user-select: none;
    outline: none;
}

.fluent-btn:focus {
    outline: 2px solid var(--fluent-primary);
    outline-offset: 2px;
}

.fluent-btn:focus:not(:focus-visible) {
    outline: none;
}

/* Sizes */
.fluent-btn-sm {
    padding: var(--fluent-space-sm) var(--fluent-space-md);
    font-size: var(--fluent-font-size-sm);
    min-height: 32px;
}

.fluent-btn-md {
    padding: var(--fluent-space-sm) var(--fluent-space-lg);
    font-size: var(--fluent-font-size-base);
    min-height: 40px;
}

.fluent-btn-lg {
    padding: var(--fluent-space-md) var(--fluent-space-xl);
    font-size: var(--fluent-font-size-lg);
    min-height: 48px;
}

/* Variants */
.fluent-btn-primary {
    background-color: var(--fluent-primary);
    color: var(--fluent-white);
    border-color: var(--fluent-primary);
}

.fluent-btn-primary:hover:not(.disabled) {
    background-color: var(--fluent-primary-hover);
    border-color: var(--fluent-primary-hover);
    transform: translateY(-1px);
    box-shadow: var(--fluent-shadow-md);
}

.fluent-btn-primary:active:not(.disabled) {
    background-color: var(--fluent-primary-pressed);
    border-color: var(--fluent-primary-pressed);
    transform: translateY(0);
}

.fluent-btn-secondary {
    background-color: var(--fluent-white);
    color: var(--fluent-gray-120);
    border-color: var(--fluent-gray-40);
}

.fluent-btn-secondary:hover:not(.disabled) {
    background-color: var(--fluent-gray-20);
    border-color: var(--fluent-gray-50);
    transform: translateY(-1px);
    box-shadow: var(--fluent-shadow-md);
}

.fluent-btn-secondary:active:not(.disabled) {
    background-color: var(--fluent-gray-30);
    border-color: var(--fluent-gray-60);
    transform: translateY(0);
}

.fluent-btn-success {
    background-color: var(--fluent-success);
    color: var(--fluent-white);
    border-color: var(--fluent-success);
}

.fluent-btn-success:hover:not(.disabled) {
    background-color: var(--fluent-success-hover);
    border-color: var(--fluent-success-hover);
    transform: translateY(-1px);
    box-shadow: var(--fluent-shadow-md);
}

.fluent-btn-warning {
    background-color: var(--fluent-warning);
    color: var(--fluent-white);
    border-color: var(--fluent-warning);
}

.fluent-btn-warning:hover:not(.disabled) {
    background-color: var(--fluent-warning-hover);
    border-color: var(--fluent-warning-hover);
    transform: translateY(-1px);
    box-shadow: var(--fluent-shadow-md);
}

.fluent-btn-error {
    background-color: var(--fluent-error);
    color: var(--fluent-white);
    border-color: var(--fluent-error);
}

.fluent-btn-error:hover:not(.disabled) {
    background-color: var(--fluent-error-hover);
    border-color: var(--fluent-error-hover);
    transform: translateY(-1px);
    box-shadow: var(--fluent-shadow-md);
}

.fluent-btn-ghost {
    background-color: transparent;
    color: var(--fluent-primary);
    border-color: transparent;
}

.fluent-btn-ghost:hover:not(.disabled) {
    background-color: var(--fluent-primary-light);
    color: var(--fluent-primary-hover);
}

.fluent-btn-link {
    background-color: transparent;
    color: var(--fluent-primary);
    border-color: transparent;
    text-decoration: underline;
    padding: var(--fluent-space-xs) var(--fluent-space-sm);
    min-height: auto;
}

.fluent-btn-link:hover:not(.disabled) {
    color: var(--fluent-primary-hover);
    text-decoration: none;
}

/* Disabled state */
.fluent-btn.disabled {
    opacity: 0.6;
    cursor: not-allowed;
    pointer-events: none;
}

/* Loading state */
.fluent-btn.loading {
    pointer-events: none;
}

.btn-spinner {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
}

.btn-spinner .spinner {
    width: 16px;
    height: 16px;
    border: 2px solid transparent;
    border-top: 2px solid currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.fluent-btn.loading .btn-text {
    opacity: 0;
}

.fluent-btn.loading .btn-icon {
    opacity: 0;
}

/* Icon styles */
.btn-icon {
    font-size: var(--fluent-font-size-sm);
    line-height: 1;
}

.fluent-btn-sm .btn-icon {
    font-size: var(--fluent-font-size-xs);
}

.fluent-btn-lg .btn-icon {
    font-size: var(--fluent-font-size-base);
}

/* Button groups */
.fluent-btn-group {
    display: inline-flex;
    border-radius: var(--fluent-radius-md);
    overflow: hidden;
}

.fluent-btn-group .fluent-btn {
    border-radius: 0;
    border-right-width: 0;
}

.fluent-btn-group .fluent-btn:first-child {
    border-top-left-radius: var(--fluent-radius-md);
    border-bottom-left-radius: var(--fluent-radius-md);
}

.fluent-btn-group .fluent-btn:last-child {
    border-top-right-radius: var(--fluent-radius-md);
    border-bottom-right-radius: var(--fluent-radius-md);
    border-right-width: 1px;
}

/* Responsive */
@media (max-width: 768px) {
    .fluent-btn {
        width: 100%;
    }
    
    .fluent-btn-group {
        width: 100%;
    }
    
    .fluent-btn-group .fluent-btn {
        flex: 1;
    }
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>


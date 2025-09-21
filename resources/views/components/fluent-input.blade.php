@props([
    'type' => 'text',
    'label' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'value' => '',
    'error' => '',
    'help' => '',
    'icon' => null,
    'iconPosition' => 'left',
    'size' => 'medium',
    'autocomplete' => null,
    'pattern' => null,
    'min' => null,
    'max' => null,
    'step' => null,
    'maxlength' => null,
    'minlength' => null
])

@php
    $sizes = [
        'small' => 'fluent-input-sm',
        'medium' => 'fluent-input-md',
        'large' => 'fluent-input-lg'
    ];
    
    $inputClass = 'fluent-input ' . ($sizes[$size] ?? $sizes['medium']);
    
    if ($error) {
        $inputClass .= ' is-invalid';
    }
    
    if ($icon) {
        $inputClass .= ' has-icon has-icon-' . $iconPosition;
    }
    
    $attributes = $attributes->merge([
        'class' => $inputClass,
        'type' => $type,
        'placeholder' => $placeholder,
        'required' => $required,
        'disabled' => $disabled,
        'readonly' => $readonly,
        'value' => $value,
        'autocomplete' => $autocomplete,
        'pattern' => $pattern,
        'min' => $min,
        'max' => $max,
        'step' => $step,
        'maxlength' => $maxlength,
        'minlength' => $minlength
    ]);
@endphp

<div class="fluent-form-group">
    @if($label)
        <label class="fluent-label {{ $required ? 'required' : '' }}" for="{{ $attributes->get('id', $attributes->get('name')) }}">
            {{ $label }}
            @if($required)
                <span class="required-asterisk">*</span>
            @endif
        </label>
    @endif
    
    <div class="fluent-input-container">
        @if($icon && $iconPosition === 'left')
            <div class="input-icon input-icon-left">
                <i class="fas fa-{{ $icon }}"></i>
            </div>
        @endif
        
        <input {{ $attributes }}>
        
        @if($icon && $iconPosition === 'right')
            <div class="input-icon input-icon-right">
                <i class="fas fa-{{ $icon }}"></i>
            </div>
        @endif
        
        @if($type === 'password')
            <button type="button" class="password-toggle" onclick="togglePassword(this)">
                <i class="fas fa-eye"></i>
            </button>
        @endif
    </div>
    
    @if($help && !$error)
        <div class="fluent-help-text">{{ $help }}</div>
    @endif
    
    @if($error)
        <div class="fluent-error-text">{{ $error }}</div>
    @endif
</div>

<style>
.fluent-form-group {
    margin-bottom: var(--fluent-space-lg);
}

.fluent-label {
    display: block;
    font-weight: 500;
    color: var(--fluent-gray-120);
    margin-bottom: var(--fluent-space-sm);
    font-size: var(--fluent-font-size-sm);
}

.fluent-label.required .required-asterisk {
    color: var(--fluent-error);
    margin-left: var(--fluent-space-xs);
}

.fluent-input-container {
    position: relative;
    display: flex;
    align-items: center;
}

.fluent-input {
    width: 100%;
    font-family: var(--fluent-font-family);
    font-size: var(--fluent-font-size-base);
    color: var(--fluent-gray-120);
    background-color: var(--fluent-white);
    border: 1px solid var(--fluent-gray-40);
    border-radius: var(--fluent-radius-md);
    padding: var(--fluent-space-sm) var(--fluent-space-md);
    transition: all var(--fluent-transition-fast);
    outline: none;
}

.fluent-input:focus {
    border-color: var(--fluent-primary);
    box-shadow: 0 0 0 2px rgba(0, 120, 212, 0.2);
}

.fluent-input:disabled {
    background-color: var(--fluent-gray-20);
    color: var(--fluent-gray-70);
    cursor: not-allowed;
}

.fluent-input:read-only {
    background-color: var(--fluent-gray-10);
    color: var(--fluent-gray-90);
}

.fluent-input::placeholder {
    color: var(--fluent-gray-70);
}

/* Sizes */
.fluent-input-sm {
    padding: var(--fluent-space-xs) var(--fluent-space-sm);
    font-size: var(--fluent-font-size-sm);
    min-height: 32px;
}

.fluent-input-md {
    padding: var(--fluent-space-sm) var(--fluent-space-md);
    font-size: var(--fluent-font-size-base);
    min-height: 40px;
}

.fluent-input-lg {
    padding: var(--fluent-space-md) var(--fluent-space-lg);
    font-size: var(--fluent-font-size-lg);
    min-height: 48px;
}

/* Icon styles */
.fluent-input.has-icon-left {
    padding-left: var(--fluent-space-xl);
}

.fluent-input.has-icon-right {
    padding-right: var(--fluent-space-xl);
}

.input-icon {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    color: var(--fluent-gray-70);
    font-size: var(--fluent-font-size-sm);
    pointer-events: none;
    z-index: 1;
}

.input-icon-left {
    left: var(--fluent-space-md);
}

.input-icon-right {
    right: var(--fluent-space-md);
}

.fluent-input:focus + .input-icon,
.fluent-input:focus ~ .input-icon {
    color: var(--fluent-primary);
}

/* Password toggle */
.password-toggle {
    position: absolute;
    right: var(--fluent-space-md);
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--fluent-gray-70);
    cursor: pointer;
    padding: var(--fluent-space-xs);
    border-radius: var(--fluent-radius-sm);
    transition: all var(--fluent-transition-fast);
    z-index: 1;
}

.password-toggle:hover {
    color: var(--fluent-gray-100);
    background-color: var(--fluent-gray-20);
}

/* Validation states */
.fluent-input.is-invalid {
    border-color: var(--fluent-error);
}

.fluent-input.is-invalid:focus {
    border-color: var(--fluent-error);
    box-shadow: 0 0 0 2px rgba(209, 52, 56, 0.2);
}

.fluent-input.is-valid {
    border-color: var(--fluent-success);
}

.fluent-input.is-valid:focus {
    border-color: var(--fluent-success);
    box-shadow: 0 0 0 2px rgba(16, 124, 16, 0.2);
}

/* Help and error text */
.fluent-help-text {
    margin-top: var(--fluent-space-xs);
    font-size: var(--fluent-font-size-xs);
    color: var(--fluent-gray-80);
    line-height: 1.4;
}

.fluent-error-text {
    margin-top: var(--fluent-space-xs);
    font-size: var(--fluent-font-size-xs);
    color: var(--fluent-error);
    line-height: 1.4;
    display: flex;
    align-items: center;
    gap: var(--fluent-space-xs);
}

.fluent-error-text::before {
    content: '⚠';
    font-size: var(--fluent-font-size-xs);
}

/* Special input types */
.fluent-input[type="search"] {
    padding-left: var(--fluent-space-xl);
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23605e5c' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: var(--fluent-space-md) center;
    background-size: 16px;
}

.fluent-input[type="search"]:focus {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%230078d4' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
}

/* Number input spinners */
.fluent-input[type="number"]::-webkit-outer-spin-button,
.fluent-input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.fluent-input[type="number"] {
    -moz-appearance: textfield;
}

/* Responsive */
@media (max-width: 768px) {
    .fluent-input {
        font-size: 16px; /* Prevent zoom on iOS */
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .fluent-input {
        background-color: var(--fluent-gray-20);
        border-color: var(--fluent-gray-50);
        color: var(--fluent-gray-140);
    }
    
    .fluent-input:focus {
        background-color: var(--fluent-gray-10);
    }
    
    .fluent-input:disabled {
        background-color: var(--fluent-gray-30);
        color: var(--fluent-gray-80);
    }
    
    .fluent-input::placeholder {
        color: var(--fluent-gray-80);
    }
}
</style>

<script>
function togglePassword(button) {
    const input = button.previousElementSibling;
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>


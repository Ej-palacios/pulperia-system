@props([
    'label' => '',
    'name' => '',
    'required' => false,
    'disabled' => false,
    'error' => '',
    'help' => '',
    'value' => ''
])

<div class="fluent-form-group">
    @if($label)
        <label class="fluent-label {{ $required ? 'required' : '' }}" for="{{ $name }}">
            {{ $label }}
            @if($required)
                <span class="required-asterisk">*</span>
            @endif
        </label>
    @endif

    <div class="fluent-select-container">
        <select 
            id="{{ $name }}" 
            name="{{ $name }}" 
            class="fluent-select @if($error) is-invalid @endif"
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{ $attributes }}
        >
            {{ $slot }}
        </select>
        <div class="select-chevron">
            <i class="fas fa-chevron-down"></i>
        </div>
    </div>

    @if($help && !$error)
        <div class="fluent-help-text">{{ $help }}</div>
    @endif

    @if($error)
        <div class="fluent-error-text">{{ $error }}</div>
    @endif
</div>

<style>
.fluent-select-container {
    position: relative;
    display: flex;
    align-items: center;
}

.fluent-select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    width: 100%;
    font-family: var(--fluent-font-family);
    font-size: var(--fluent-font-size-base);
    color: var(--fluent-gray-120);
    background-color: var(--fluent-white);
    border: 1px solid var(--fluent-gray-40);
    border-radius: var(--fluent-radius-md);
    padding: var(--fluent-space-sm) var(--fluent-space-lg) var(--fluent-space-sm) var(--fluent-space-md);
    transition: all var(--fluent-transition-fast);
    cursor: pointer;
    line-height: 1.5;
    min-height: 40px;
}

.fluent-select:focus {
    outline: none;
    border-color: var(--fluent-primary);
    box-shadow: 0 0 0 2px rgba(0, 120, 212, 0.2);
}

.fluent-select.is-invalid {
    border-color: var(--fluent-error);
}

.fluent-select:disabled {
    background-color: var(--fluent-gray-20);
    color: var(--fluent-gray-70);
    cursor: not-allowed;
}

.select-chevron {
    position: absolute;
    right: var(--fluent-space-md);
    top: 50%;
    transform: translateY(-50%);
    color: var(--fluent-gray-80);
    pointer-events: none;
    font-size: var(--fluent-font-size-xs);
}
</style>

@props(['label', 'name', 'type' => 'text', 'value' => '', 'required' => false, 'help' => null])

<div class="mb-3">
    @if($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if($required) <span class="text-danger">*</span> @endif
        </label>
    @endif
    
    <input 
        type="{{ $type }}" 
        id="{{ $name }}" 
        name="{{ $name }}" 
        value="{{ old($name, $value) }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->class(['form-control']) }}
    >
    
    @if($help)
        <div class="form-text">{{ $help }}</div>
    @endif
</div>
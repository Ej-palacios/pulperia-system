@props(['type' => 'button', 'color' => 'primary', 'size' => '', 'icon' => null])

<button type="{{ $type }}" {{ $attributes->class([
    'btn',
    "btn-$color",
    $size ? "btn-$size" : '',
]) }}>
    @if($icon)
        <i class="{{ $icon }} me-1"></i>
    @endif
    {{ $slot }}
</button>
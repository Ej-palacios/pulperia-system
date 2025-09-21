@props(['type' => 'button', 'color' => 'primary', 'size' => '', 'disabled' => false, 'icon' => '', 'href' => ''])

@if($href)
<a 
    href="{{ $href }}" 
    class="btn btn-{{ $color }} {{ $size ? 'btn-' . $size : '' }}"
    @if($disabled) disabled @endif
    {{ $attributes->merge() }}
>
    @if($icon)<i class="{{ $icon }}"></i>@endif
    {{ $slot }}
</a>
@else
<button 
    type="{{ $type }}" 
    class="btn btn-{{ $color }} {{ $size ? 'btn-' . $size : '' }}"
    @if($disabled) disabled @endif
    {{ $attributes->merge() }}
>
    @if($icon)<i class="{{ $icon }}"></i>@endif
    {{ $slot }}
</button>
@endif
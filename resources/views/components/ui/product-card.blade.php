@props([
    'nombre',
    'precio' => 0,
    'categoria' => null,
    'imagen' => null, // path o URL
    'href' => '#',
    'badge' => null,
])

@php
    $imgSrc = $imagen;
    if ($imagen) {
        $isExternal = str_starts_with($imagen, 'http://') || str_starts_with($imagen, 'https://');
        $imgSrc = $isExternal ? $imagen : asset('storage/' . ltrim($imagen, '/'));
    }
@endphp

<a href="{{ $href }}" class="text-decoration-none" style="color: inherit;">
    <div class="card h-100" style="border-radius:12px;overflow:hidden;border:1px solid #e9ecef;">
        <div class="ratio ratio-4x3 bg-light" style="background-position:center;background-size:cover;{{ $imgSrc ? 'background-image:url(' . e($imgSrc) . ');' : '' }}">
            @unless($imgSrc)
                <div class="d-flex align-items-center justify-content-center text-muted">
                    <i class="fas fa-image fa-2x"></i>
                </div>
            @endunless
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-1">
                <h6 class="mb-0" style="line-height:1.2;">{{ $nombre }}</h6>
                @if($badge)
                    <span class="badge text-bg-warning">{{ $badge }}</span>
                @endif
            </div>
            @if($categoria)
                <div class="text-muted small mb-2">{{ $categoria }}</div>
            @endif
            <div class="fw-bold" style="color:#198754;">C$ {{ number_format((float) $precio, 2) }}</div>
            {{ $slot }}
        </div>
    </div>
 </a>





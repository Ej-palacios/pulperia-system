@props([
    'title',
    'subtitle' => null,
    'icon' => 'fas fa-box',
    'href' => '#',
    'badge' => null,
    'color' => 'primary', // primary | success | warning | danger | info | secondary
])

@php
    $colorMap = [
        'primary' => '#0d6efd',
        'success' => '#198754',
        'warning' => '#ffc107',
        'danger' => '#dc3545',
        'info' => '#0dcaf0',
        'secondary' => '#6c757d',
    ];
    $accent = $colorMap[$color] ?? $colorMap['primary'];
@endphp

<a href="{{ $href }}" class="text-decoration-none" style="color: inherit;">
    <div class="card" style="border:1px solid #e9ecef;border-radius:12px;overflow:hidden;transition:transform .15s ease, box-shadow .15s ease;">
        <div style="height:4px;background: {{ $accent }}"></div>
        <div class="card-body d-flex align-items-start gap-3">
            <div class="d-flex align-items-center justify-content-center" style="width:48px;height:48px;border-radius:10px;background: rgba(0,0,0,.04);color: {{ $accent }};flex:0 0 48px;">
                <i class="{{ $icon }}"></i>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2">
                    <h6 class="mb-0">{{ $title }}</h6>
                    @if($badge)
                        <span class="badge" style="background: {{ $accent }};">{{ $badge }}</span>
                    @endif
                </div>
                @if($subtitle)
                    <div class="text-muted small mt-1">{{ $subtitle }}</div>
                @endif
                {{ $slot }}
            </div>
            <i class="fas fa-chevron-right text-muted"></i>
        </div>
    </div>
 </a>





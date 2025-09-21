@props([
    'variant' => 'default', // default, primary, success, warning, error
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'href' => null
])

@php
    $cardClass = 'fluent-card variant-' . $variant;
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => $cardClass, 'href' => $href]) }}>
    @if ($title || isset($header)) 
        <div class="card-header">
            <div class="header-content">
                @if ($icon)
                    <div class="card-icon">
                        <i class="fas fa-{{ $icon }}"></i>
                    </div>
                @endif
                <div class="title-wrapper">
                    @if ($title)
                        <h3 class="card-title">{{ $title }}</h3>
                    @endif
                    @if ($subtitle)
                        <p class="card-subtitle">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
            @if (isset($actions))
                <div class="card-actions">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    <div class="card-content">
        {{ $slot }}
    </div>

    @if (isset($footer))
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endif
</{{ $tag }}>

<style>
.fluent-card {
    background-color: var(--fluent-white);
    border-radius: var(--fluent-radius-lg);
    border: 1px solid var(--fluent-gray-30);
    box-shadow: var(--fluent-shadow-sm);
    transition: all var(--fluent-transition-normal);
    display: flex;
    flex-direction: column;
}

a.fluent-card {
    text-decoration: none;
    color: inherit;
}

a.fluent-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--fluent-shadow-lg);
}

.fluent-card.variant-default {
    border-left: 4px solid var(--fluent-gray-40);
}

.fluent-card.variant-primary {
    border-left: 4px solid var(--fluent-primary);
}

.fluent-card.variant-success {
    border-left: 4px solid var(--fluent-success);
}

.fluent-card.variant-warning {
    border-left: 4px solid var(--fluent-warning);
}

.fluent-card.variant-error {
    border-left: 4px solid var(--fluent-error);
}

.card-header {
    padding: var(--fluent-space-lg);
    border-bottom: 1px solid var(--fluent-gray-20);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: var(--fluent-space-md);
}

.header-content {
    display: flex;
    align-items: center;
    gap: var(--fluent-space-md);
}

.card-icon {
    font-size: var(--fluent-font-size-xl);
    color: var(--fluent-gray-80);
}

.title-wrapper {
    display: flex;
    flex-direction: column;
}

.card-title {
    font-size: var(--fluent-font-size-lg);
    font-weight: 600;
    color: var(--fluent-gray-120);
    margin: 0;
}

.card-subtitle {
    font-size: var(--fluent-font-size-sm);
    color: var(--fluent-gray-80);
    margin: 0;
}

.card-actions {
    display: flex;
    align-items: center;
    gap: var(--fluent-space-sm);
}

.card-content {
    padding: var(--fluent-space-lg);
    flex: 1;
}

.card-footer {
    padding: var(--fluent-space-md) var(--fluent-space-lg);
    border-top: 1px solid var(--fluent-gray-20);
    background-color: var(--fluent-gray-10);
    font-size: var(--fluent-font-size-sm);
    color: var(--fluent-gray-80);
}
</style>

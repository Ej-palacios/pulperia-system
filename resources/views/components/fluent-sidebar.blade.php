@props([
    'logoUrl' => '#',
    'appName' => config('app.name', 'Pulpería')
])

<aside class="fluent-sidebar">
    <div class="sidebar-header">
        <a href="{{ $logoUrl }}" class="logo-container">
            <i class="fas fa-store logo-icon"></i>
            <span class="logo-text">{{ $appName }}</span>
        </a>
        <x-fluent-button variant="ghost" icon="bars" class="sidebar-toggle" />
    </div>

    <nav class="sidebar-nav">
        {{ $slot }}
    </nav>

    @if (isset($footer))
        <div class="sidebar-footer">
            {{ $footer }}
        </div>
    @endif
</aside>

<style>
.fluent-sidebar {
    width: 280px;
    background-color: var(--fluent-background-layer-1);
    border-right: 1px solid var(--fluent-border-color);
    display: flex;
    flex-direction: column;
    transition: width 0.3s ease;
}

.sidebar-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 var(--fluent-space-lg);
    height: 60px;
    border-bottom: 1px solid var(--fluent-border-color);
}

.logo-container {
    display: flex;
    align-items: center;
    gap: var(--fluent-space-md);
    text-decoration: none;
    color: var(--fluent-text-primary);
}

.logo-icon {
    font-size: var(--fluent-font-size-xl);
    color: var(--fluent-primary);
}

.logo-text {
    font-size: var(--fluent-font-size-lg);
    font-weight: 600;
    white-space: nowrap;
}

.sidebar-nav {
    flex-grow: 1;
    overflow-y: auto;
    padding: var(--fluent-space-md) 0;
}

.sidebar-footer {
    padding: var(--fluent-space-lg);
    border-top: 1px solid var(--fluent-border-color);
}
</style>

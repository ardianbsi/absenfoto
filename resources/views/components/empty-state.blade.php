@props([
    'icon' => 'ti-database-off',
    'title' => 'No data found',
    'description' => '',
    'buttonLabel' => '',
    'buttonLink' => '',
    'buttonIcon' => 'ti-plus',
])

<div class="empty">
    <div class="empty-icon">
        <i class="ti {{ $icon }}" style="font-size: 3rem;"></i>
    </div>
    <p class="empty-title h3">{{ $title }}</p>
    @if($description)
        <p class="empty-subtitle text-muted">{{ $description }}</p>
    @endif
    @if($buttonLabel && $buttonLink)
        <div class="empty-action">
            <a href="{{ $buttonLink }}" class="btn btn-primary">
                <i class="ti {{ $buttonIcon }} me-1"></i>
                {{ $buttonLabel }}
            </a>
        </div>
    @endif
    @if(isset($slot) && $slot)
        <div class="empty-action">
            {{ $slot }}
        </div>
    @endif
</div>

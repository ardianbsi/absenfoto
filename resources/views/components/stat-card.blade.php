@props(['title', 'value', 'icon' => 'ti-star', 'color' => 'blue', 'link' => null])

@php
    $colors = ['blue', 'azure', 'indigo', 'purple', 'pink', 'red', 'orange', 'yellow', 'lime', 'green', 'teal', 'cyan'];
    $color = in_array($color, $colors) ? $color : 'blue';
@endphp

<div class="card card-sm">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-auto">
                <span class="bg-{{ $color }} text-white avatar">
                    <i class="ti {{ $icon }}"></i>
                </span>
            </div>
            <div class="col">
                <div class="font-weight-medium">
                    {{ $value }}
                </div>
                <div class="text-muted">
                    {{ $title }}
                </div>
            </div>
        </div>
        @if($link)
            <div class="mt-2 text-end">
                <a href="{{ $link }}" class="btn btn-sm btn-ghost-{{ $color }}">
                    View details &raquo;
                </a>
            </div>
        @endif
    </div>
</div>

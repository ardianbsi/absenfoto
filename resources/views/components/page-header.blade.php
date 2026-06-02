@props(['title' => '', 'subtitle' => '', 'breadcrumbs' => []])

<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col">
            @if(!empty($breadcrumbs))
                <x-breadcrumb :items="$breadcrumbs" />
            @endif

            <h2 class="page-title">
                {{ $title }}
            </h2>

            @if($subtitle)
                <div class="text-muted mt-1">{{ $subtitle }}</div>
            @endif
        </div>

        @if(isset($actions))
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    {{ $actions }}
                </div>
            </div>
        @endif
    </div>
</div>

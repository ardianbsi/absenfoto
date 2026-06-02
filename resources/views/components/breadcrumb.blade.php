@props(['items' => []])

<ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}"><i class="ti ti-home"></i></a>
    </li>
    @foreach($items as $item)
        @if(isset($item['url']) && !$loop->last)
            <li class="breadcrumb-item">
                <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
            </li>
        @else
            <li class="breadcrumb-item active" aria-current="page">
                {{ $item['label'] }}
            </li>
        @endif
    @endforeach
</ol>

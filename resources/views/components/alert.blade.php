@props(['type' => 'info', 'message' => ''])

@if($message)
    <div class="alert alert-{{ $type }} alert-dismissible" role="alert">
        <div class="d-flex">
            <div>
                @switch($type)
                    @case('success')
                        <i class="ti ti-check"></i>
                        @break
                    @case('danger')
                        <i class="ti ti-alert-triangle"></i>
                        @break
                    @case('warning')
                        <i class="ti ti-alert-circle"></i>
                        @break
                    @default
                        <i class="ti ti-info-circle"></i>
                @endswitch
            </div>
            <div class="ms-2">{{ $message }}</div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="Close"></a>
    </div>
@endif

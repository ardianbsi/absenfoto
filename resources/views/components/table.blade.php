@props(['responsive' => true])

<div class="table-responsive">
    <table class="table table-vcenter card-table table-striped">
        <thead>
            {{ $header }}
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>

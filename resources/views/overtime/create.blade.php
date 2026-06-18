@extends('layouts.app')
@section('title', 'Ajukan Lembur')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Ajukan Lembur</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('overtime.my') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="alert alert-info d-flex align-items-center" role="alert">
            <i class="ti ti-info-circle me-2 fs-2"></i>
            <div>Lembur harus dilakukan di luar jam kerja reguler. Pastikan Anda telah menyelesaikan jam kerja normal sebelum mengajukan lembur.</div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Form Lembur</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('overtime.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label required">Tanggal Lembur</label>
                        <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', now()->toDateString()) }}" id="overtime_date" required>
                        @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label required">Jam Mulai</label>
                            <input type="time" name="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time') }}" id="start_time" required>
                            @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Jam Selesai</label>
                            <input type="time" name="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time') }}" id="end_time" required>
                            @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="card card-sm border">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-clock-hour me-2 fs-2 text-primary"></i>
                                    <div>
                                        <span class="text-secondary me-2">Total Jam Lembur:</span>
                                        <span class="fs-3 fw-bold" id="total_hours">0</span>
                                        <span class="text-secondary">jam</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Deskripsi Pekerjaan</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Jelaskan pekerjaan yang dilakukan selama lembur" required>{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lampiran (opsional)</label>
                        <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                        @error('attachment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="form-hint">Format: PDF, JPG, PNG. Maks 2MB</small>
                    </div>

                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-send me-2"></i>Ajukan Lembur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const startTime = document.getElementById('start_time');
    const endTime = document.getElementById('end_time');
    const totalHours = document.getElementById('total_hours');

    function calcHours() {
        if (startTime.value && endTime.value) {
            const [sh, sm] = startTime.value.split(':').map(Number);
            const [eh, em] = endTime.value.split(':').map(Number);
            let diff = (eh * 60 + em) - (sh * 60 + sm);
            if (diff < 0) diff += 24 * 60;
            const hours = Math.floor(diff / 60);
            const mins = diff % 60;
            totalHours.textContent = hours + '.' + String(Math.round(mins / 60 * 10));
        }
    }

    if (startTime) startTime.addEventListener('change', calcHours);
    if (endTime) endTime.addEventListener('change', calcHours);
})();
</script>
@endpush

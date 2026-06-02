@extends('layouts.app')
@section('title', 'Ajukan Cuti / Izin')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Ajukan Cuti / Izin</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('leaves.my') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Form Pengajuan</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('leaves.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label required">Tipe Cuti / Izin</label>
                        <select name="leave_type_id" class="form-select @error('leave_type_id') is-invalid @enderror" id="leave_type_id" required>
                            <option value="">Pilih Tipe</option>
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }} data-quota="{{ $balances[$type->id]['remaining'] ?? 0 }}" data-max="{{ $type->max_days ?? 30 }}">
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('leave_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label required">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}" id="start_date" required>
                            @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Tanggal Selesai</label>
                            <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}" id="end_date" required>
                            @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="card card-sm border">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-calculator me-2 fs-2 text-primary"></i>
                                    <div>
                                        <span class="text-secondary me-2">Total Hari:</span>
                                        <span class="fs-3 fw-bold" id="total_days">0</span>
                                        <span class="text-secondary">hari</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Alasan</label>
                        <textarea name="reason" class="form-control @error('reason') is-invalid @enderror" rows="4" id="reason" maxlength="500" required>{{ old('reason') }}</textarea>
                        @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="form-hint"><span id="char_count">0</span>/500 karakter</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lampiran (opsional)</label>
                        <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        @error('attachment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="form-hint">Format: PDF, JPG, PNG, DOC. Maks 2MB</small>
                    </div>

                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-send me-2"></i>Ajukan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Sisa Kuota Cuti</h3>
            </div>
            <div class="card-body" id="balanceContainer">
                @foreach($balances as $balance)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>{{ $balance['type']->name }}</span>
                            <span class="text-secondary">{{ $balance['used'] }}/{{ $balance['quota'] }}</span>
                        </div>
                        <div class="progress progress-xs">
                            <div class="progress-bar {{ $balance['remaining'] > 0 ? 'bg-success' : 'bg-danger' }}" style="width: {{ $balance['quota'] > 0 ? ($balance['used'] / $balance['quota'] * 100) : 0 }}%"></div>
                        </div>
                        <small class="text-secondary">Sisa: {{ $balance['remaining'] }} hari</small>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const totalDays = document.getElementById('total_days');
    const reason = document.getElementById('reason');
    const charCount = document.getElementById('char_count');

    function calcDays() {
        if (startDate.value && endDate.value) {
            const start = new Date(startDate.value);
            const end = new Date(endDate.value);
            if (end >= start) {
                const diff = Math.floor((end - start) / (1000 * 60 * 60 * 24)) + 1;
                totalDays.textContent = diff;
            }
        }
    }

    if (startDate) startDate.addEventListener('change', calcDays);
    if (endDate) endDate.addEventListener('change', calcDays);

    if (reason && charCount) {
        charCount.textContent = reason.value.length;
        reason.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    }

    const leaveTypeSelect = document.getElementById('leave_type_id');
    if (leaveTypeSelect) {
        leaveTypeSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            if (selected && selected.dataset.quota !== undefined) {
                const remaining = parseInt(selected.dataset.quota);
                if (remaining <= 0) {
                    alert('Kuota untuk tipe cuti ini sudah habis.');
                }
            }
        });
    }
})();
</script>
@endpush

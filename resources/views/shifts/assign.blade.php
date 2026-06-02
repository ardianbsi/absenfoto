@extends('layouts.app')
@section('title', 'Assign Shift')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Master Data</div>
                <h2 class="page-title">Assign Shift</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('shifts.index') }}" class="btn btn-secondary">
                    <span class="ti ti-arrow-left me-2"></span>Data Shift
                </a>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <form action="{{ route('shifts.assign.store') }}" method="POST">
                    @csrf
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Form Assign Shift</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Pilih Karyawan <span class="text-danger">*</span></label>
                                <div class="card">
                                    <div class="card-body p-2" style="max-height: 300px; overflow-y: auto;">
                                        @if(isset($employees) && $employees->count() > 0)
                                            @foreach($employees as $employee)
                                            <label class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="employees[]" value="{{ $employee->id }}" {{ in_array($employee->id, old('employees', [])) ? 'checked' : '' }}>
                                                <span class="form-check-label">
                                                    <div class="d-flex align-items-center">
                                                        @if($employee->photo)
                                                        <span class="avatar avatar-sm me-2 rounded" style="background-image: url({{ asset('storage/'.$employee->photo) }})"></span>
                                                        @else
                                                        <span class="avatar avatar-sm me-2 rounded-circle bg-primary text-white">{{ substr($employee->name, 0, 1) }}</span>
                                                        @endif
                                                        <div>
                                                            <div class="fw-medium">{{ $employee->name }}</div>
                                                            <div class="text-secondary small">{{ $employee->department->name ?? '-' }} | {{ $employee->position->name ?? '-' }}</div>
                                                        </div>
                                                    </div>
                                                </span>
                                            </label>
                                            @endforeach
                                        @else
                                        <div class="text-center text-secondary py-3">
                                            Belum ada data karyawan
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleAll(true)">Pilih Semua</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleAll(false)">Batal Semua</button>
                                </div>
                                @error('employees')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Pilih Department (Filter)</label>
                                <select class="form-select" id="filter-department">
                                    <option value="">Semua Department</option>
                                    @if(isset($departments))
                                        @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pilih Shift <span class="text-danger">*</span></label>
                                    <select class="form-select @error('shift_id') is-invalid @enderror" name="shift_id">
                                        <option value="">Pilih Shift</option>
                                        @if(isset($shifts))
                                            @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                                {{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})
                                            </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('shift_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}">
                                    @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" name="end_date" value="{{ old('end_date') }}">
                                    @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Hari Kerja</label>
                                    <div class="form-selectgroup">
                                        <label class="form-selectgroup-item">
                                            <input type="checkbox" name="days[]" value="1" class="form-selectgroup-input" {{ in_array('1', old('days', ['1','2','3','4','5'])) ? 'checked' : '' }}>
                                            <span class="form-selectgroup-label">Senin</span>
                                        </label>
                                        <label class="form-selectgroup-item">
                                            <input type="checkbox" name="days[]" value="2" class="form-selectgroup-input" {{ in_array('2', old('days', ['1','2','3','4','5'])) ? 'checked' : '' }}>
                                            <span class="form-selectgroup-label">Sel</span>
                                        </label>
                                        <label class="form-selectgroup-item">
                                            <input type="checkbox" name="days[]" value="3" class="form-selectgroup-input" {{ in_array('3', old('days', ['1','2','3','4','5'])) ? 'checked' : '' }}>
                                            <span class="form-selectgroup-label">Rab</span>
                                        </label>
                                        <label class="form-selectgroup-item">
                                            <input type="checkbox" name="days[]" value="4" class="form-selectgroup-input" {{ in_array('4', old('days', ['1','2','3','4','5'])) ? 'checked' : '' }}>
                                            <span class="form-selectgroup-label">Kam</span>
                                        </label>
                                        <label class="form-selectgroup-item">
                                            <input type="checkbox" name="days[]" value="5" class="form-selectgroup-input" {{ in_array('5', old('days', ['1','2','3','4','5'])) ? 'checked' : '' }}>
                                            <span class="form-selectgroup-label">Jum</span>
                                        </label>
                                        <label class="form-selectgroup-item">
                                            <input type="checkbox" name="days[]" value="6" class="form-selectgroup-input" {{ in_array('6', old('days', [])) ? 'checked' : '' }}>
                                            <span class="form-selectgroup-label">Sab</span>
                                        </label>
                                        <label class="form-selectgroup-item">
                                            <input type="checkbox" name="days[]" value="0" class="form-selectgroup-input" {{ in_array('0', old('days', [])) ? 'checked' : '' }}>
                                            <span class="form-selectgroup-label">Min</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('shifts.index') }}" class="btn btn-secondary">
                                    <span class="ti ti-arrow-left me-2"></span>Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <span class="ti ti-device-floppy me-2"></span>Assign Shift
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
function toggleAll(check) {
    const checkboxes = document.querySelectorAll('input[name="employees[]"]');
    checkboxes.forEach(cb => cb.checked = check);
}
</script>
@endpush

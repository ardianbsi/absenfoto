@extends('layouts.app')
@section('title', 'Data Hari Libur')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Master Data</div>
                <h2 class="page-title">Data Hari Libur</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <button type="button" class="btn btn-secondary" id="toggle-view">
                        <span class="ti ti-calendar me-2"></span>Kalender
                    </button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-create">
                        <span class="ti ti-plus me-2"></span>Tambah Hari Libur
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="card mb-4" id="card-calendar" style="display: none;">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <a href="?month={{ $prevMonth }}&year={{ $prevYear }}" class="btn btn-ghost">
                        <span class="ti ti-arrow-left"></span>
                    </a>
                    <h3 class="card-title m-0">{{ $currentMonthName ?? 'Bulan Ini' }} {{ $currentYear ?? date('Y') }}</h3>
                    <a href="?month={{ $nextMonth }}&year={{ $nextYear }}" class="btn btn-ghost">
                        <span class="ti ti-arrow-right"></span>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-1">
                    @foreach(['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $dayName)
                    <div class="col text-center fw-bold py-2 text-secondary">
                        {{ $dayName }}
                    </div>
                    @endforeach
                    @for($i = 0; $i < $firstDayOfMonth; $i++)
                    <div class="col"></div>
                    @endfor
                    @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $date = \Carbon\Carbon::create($currentYear ?? date('Y'), $currentMonth ?? date('n'), $day);
                        $isWeekend = $date->isWeekend();
                        $holiday = $holidays->where('date', $date->toDateString())->first();
                    @endphp
                    <div class="col">
                        <div class="card card-sm {{ $isWeekend ? 'bg-red-lt' : '' }} {{ $holiday ? 'border border-yellow' : '' }}">
                            <div class="card-body p-2 text-center">
                                <div class="fw-bold">{{ $day }}</div>
                                @if($holiday)
                                <div class="small text-yellow">
                                    <span class="ti ti-holiday-village"></span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>
        <div class="card" id="card-table">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th class="w-1">No</th>
                            <th>Tanggal</th>
                            <th>Nama Hari Libur</th>
                            <th>Tipe</th>
                            <th>Tahun</th>
                            <th>Berulang</th>
                            <th>Status</th>
                            <th class="w-1">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($holidays) && $holidays->count() > 0)
                            @foreach($holidays as $holiday)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <span class="badge bg-red text-red-fg">
                                        {{ optional($holiday->date)->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td class="font-weight-medium">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm rounded-circle bg-yellow text-white me-2">
                                            <span class="ti ti-holiday-village"></span>
                                        </span>
                                        {{ $holiday->name }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $holiday->type == 'national' ? 'red' : ($holiday->type == 'religious' ? 'purple' : 'blue') }}">
                                        {{ ucfirst($holiday->type) }}
                                    </span>
                                </td>
                                <td>{{ $holiday->year ?? '-' }}</td>
                                <td>
                                    @if($holiday->is_recurring)
                                    <span class="badge bg-green">Ya</span>
                                    @else
                                    <span class="badge bg-gray">Tidak</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $holiday->status == 'active' ? 'green' : 'red' }}">
                                        {{ $holiday->status == 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <button type="button" class="btn btn-icon btn-outline-primary edit-btn" data-id="{{ $holiday->id }}" data-name="{{ $holiday->name }}" data-date="{{ optional($holiday->date)->format('Y-m-d') }}" data-type="{{ $holiday->type }}" data-year="{{ $holiday->year }}" data-is-recurring="{{ $holiday->is_recurring ? '1' : '0' }}" data-description="{{ $holiday->description }}" data-status="{{ $holiday->status }}" data-bs-toggle="modal" data-bs-target="#modal-edit">
                                            <span class="ti ti-edit"></span>
                                        </button>
                                        <button type="button" class="btn btn-icon btn-outline-danger delete-btn" data-id="{{ $holiday->id }}" data-name="{{ $holiday->name }}" data-bs-toggle="modal" data-bs-target="#modal-delete">
                                            <span class="ti ti-trash"></span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                        <tr>
                            <td colspan="8" class="text-center text-secondary py-6">
                                <div class="mb-2"><span class="ti ti-holiday-village fs-2"></span></div>
                                <h3>Belum ada data hari libur</h3>
                                <p class="mb-3">Klik tombol "Tambah Hari Libur" untuk menambahkan data baru.</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-create">
                                    <span class="ti ti-plus me-2"></span>Tambah Hari Libur
                                </button>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if(isset($holidays) && $holidays->hasPages())
            <div class="card-footer d-flex align-items-center">
                <p class="m-0 text-secondary">
                    Menampilkan {{ $holidays->firstItem() ?? 0 }} sampai {{ $holidays->lastItem() ?? 0 }} dari {{ $holidays->total() }} data
                </p>
                <ul class="pagination m-0 ms-auto">
                    {{ $holidays->links() }}
                </ul>
            </div>
            @endif
        </div>
    </div>
</div>
<div class="modal fade" id="modal-create" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('holidays.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Hari Libur Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('date') is-invalid @enderror" name="date" value="{{ old('date') }}">
                    @error('date')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Hari Libur <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Contoh: Tahun Baru, Idul Fitri">
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipe</label>
                        <select class="form-select" name="type">
                            <option value="national" {{ old('type') == 'national' ? 'selected' : '' }}>Nasional</option>
                            <option value="religious" {{ old('type') == 'religious' ? 'selected' : '' }}>Agama</option>
                            <option value="company" {{ old('type') == 'company' ? 'selected' : '' }}>Perusahaan</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tahun</label>
                        <input type="number" class="form-control" name="year" value="{{ old('year', date('Y')) }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_recurring" value="1" {{ old('is_recurring') ? 'checked' : '' }}>
                        <span class="form-check-label">Berulang setiap tahun</span>
                    </label>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" name="description" rows="3">{{ old('description') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="modal-edit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="" method="POST" class="modal-content" id="form-edit">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Hari Libur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="date" id="edit-date">
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Hari Libur <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="edit-name">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipe</label>
                        <select class="form-select" name="type" id="edit-type">
                            <option value="national">Nasional</option>
                            <option value="religious">Agama</option>
                            <option value="company">Perusahaan</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tahun</label>
                        <input type="number" class="form-control" name="year" id="edit-year">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_recurring" value="1" id="edit-is-recurring">
                        <span class="form-check-label">Berulang setiap tahun</span>
                    </label>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" name="description" rows="3" id="edit-description"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status" id="edit-status">
                        <option value="active">Aktif</option>
                        <option value="inactive">Tidak Aktif</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="modal-delete" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="" method="POST" class="modal-content" id="form-delete">
            @csrf
            @method('DELETE')
            <div class="modal-body text-center py-4">
                <span class="ti ti-alert-circle text-red mb-3" style="font-size: 3rem;"></span>
                <h3>Hapus Hari Libur</h3>
                <p class="text-secondary">Anda yakin ingin menghapus hari libur <strong id="delete-name"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Hapus</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
let isCalendarView = false;
document.getElementById('toggle-view').addEventListener('click', function() {
    isCalendarView = !isCalendarView;
    document.getElementById('card-calendar').style.display = isCalendarView ? 'block' : 'none';
    document.getElementById('card-table').style.display = isCalendarView ? 'none' : 'block';
    this.innerHTML = isCalendarView ? '<span class="ti ti-list me-2"></span>Tabel' : '<span class="ti ti-calendar me-2"></span>Kalender';
});
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        document.getElementById('form-edit').action = '{{ url('holidays') }}/' + id;
        document.getElementById('edit-date').value = this.dataset.date;
        document.getElementById('edit-name').value = this.dataset.name;
        document.getElementById('edit-type').value = this.dataset.type;
        document.getElementById('edit-year').value = this.dataset.year;
        document.getElementById('edit-is-recurring').checked = this.dataset.isRecurring == '1';
        document.getElementById('edit-description').value = this.dataset.description || '';
        document.getElementById('edit-status').value = this.dataset.status;
    });
});
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        document.getElementById('form-delete').action = '{{ url('holidays') }}/' + id;
        document.getElementById('delete-name').textContent = this.dataset.name;
    });
});
</script>
@endpush

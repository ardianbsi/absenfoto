@extends('layouts.app')
@section('title', 'Laporan')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Laporan</h2>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" role="tablist" id="reportTabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab-daily">
                    <i class="ti ti-calendar me-1"></i>Harian
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-weekly">
                    <i class="ti ti-calendar-week me-1"></i>Mingguan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-monthly">
                    <i class="ti ti-calendar-month me-1"></i>Bulanan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-late">
                    <i class="ti ti-clock-alert me-1"></i>Keterlambatan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-overtime">
                    <i class="ti ti-clock-hour me-1"></i>Lembur
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-leave">
                    <i class="ti ti-calendar-off me-1"></i>Cuti
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">
            <div class="tab-pane active show" id="tab-daily">
                <form method="GET" action="{{ route('reports.daily') }}" class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="date" class="form-control" value="{{ request('date', now()->toDateString()) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Departemen</label>
                        <select name="department_id" class="form-select">
                            <option value="">Semua Departemen</option>
                            @foreach($departments ?? [] as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary"><i class="ti ti-eye me-1"></i>Tampilkan</button>
                        <a href="{{ route('reports.export-csv', ['type' => 'daily', 'date' => request('date', now()->toDateString())]) }}" class="btn btn-outline-success"><i class="ti ti-file-spreadsheet me-1"></i>CSV</a>
                    </div>
                </form>
                <div class="row row-cards mb-3">
                    <div class="col-sm-3">
                        <div class="card card-sm"><div class="card-body"><div class="text-secondary">Total Karyawan</div><div class="fs-2 fw-bold">{{ $attendances?->count() ?? 0 }}</div></div></div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card card-sm"><div class="card-body"><div class="text-secondary">Hadir</div><div class="fs-2 fw-bold text-success">{{ $attendances?->where('status', 'hadir')->count() ?? 0 }}</div></div></div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card card-sm"><div class="card-body"><div class="text-secondary">Telat</div><div class="fs-2 fw-bold text-warning">{{ $attendances?->where('status', 'telat')->count() ?? 0 }}</div></div></div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card card-sm"><div class="card-body"><div class="text-secondary">Alpha</div><div class="fs-2 fw-bold text-danger">{{ $attendances?->where('status', 'alpha')->count() ?? 0 }}</div></div></div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter">
                        <thead><tr><th>No</th><th>Nama</th><th>NIK</th><th>Departemen</th><th>Check In</th><th>Check Out</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse(($attendances ?? []) as $attendance)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $attendance->employee?->full_name ?? '-' }}</td>
                                    <td>{{ $attendance->employee?->nik ?? '-' }}</td>
                                    <td>{{ $attendance->employee?->department?->name ?? '-' }}</td>
                                    <td>{{ $attendance->check_in?->format('H:i:s') ?? '-' }}</td>
                                    <td>{{ $attendance->check_out?->format('H:i:s') ?? '-' }}</td>
                                    <td>
                                        @php $sc = ['hadir'=>'bg-success','telat'=>'bg-orange','alpha'=>'bg-danger','izin'=>'bg-info','sakit'=>'bg-warning','cuti'=>'bg-purple']; @endphp
                                        <span class="badge {{ $sc[$attendance->status] ?? 'bg-secondary' }}">{{ ucfirst($attendance->status) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center py-4"><div class="empty"><p class="empty-title">Tidak ada data</p></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane" id="tab-weekly">
                <form method="GET" action="{{ route('reports.weekly') }}" class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date', now()->startOfWeek()->toDateString()) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date', now()->endOfWeek()->toDateString()) }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary"><i class="ti ti-eye me-1"></i>Tampilkan</button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-vcenter">
                        <thead><tr><th>Karyawan</th><th>Hadir</th><th>Telat</th><th>Alpha</th><th>Total</th></tr></thead>
                        <tbody>
                            @forelse(($report ?? []) as $row)
                                <tr><td>{{ $row['name'] ?? '-' }}</td><td>{{ $row['hadir'] ?? 0 }}</td><td>{{ $row['telat'] ?? 0 }}</td><td>{{ $row['alpha'] ?? 0 }}</td><td>{{ $row['total'] ?? 0 }}</td></tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-4"><div class="empty"><p class="empty-title">Pilih periode untuk menampilkan data</p></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane" id="tab-monthly">
                <form method="GET" action="{{ route('reports.monthly') }}" class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Bulan</label>
                        <select name="month" class="form-select">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ (request('month', now()->month) == $m) ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tahun</label>
                        <select name="year" class="form-select">
                            @foreach(range(now()->year - 2, now()->year + 1) as $y)
                                <option value="{{ $y }}" {{ (request('year', now()->year) == $y) ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary"><i class="ti ti-eye me-1"></i>Tampilkan</button>
                        <a href="{{ route('reports.export-csv', ['type' => 'monthly', 'month' => request('month', now()->month), 'year' => request('year', now()->year)]) }}" class="btn btn-outline-success"><i class="ti ti-file-spreadsheet me-1"></i>CSV</a>
                    </div>
                </form>
                <div class="row row-cards mb-3">
                    <div class="col-sm-4">
                        <div class="card card-sm"><div class="card-body"><div class="text-secondary">Total Kehadiran</div><div class="fs-2 fw-bold text-success">{{ $totalHadir ?? 0 }}</div></div></div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card card-sm"><div class="card-body"><div class="text-secondary">Total Keterlambatan</div><div class="fs-2 fw-bold text-warning">{{ $totalTelat ?? 0 }}</div></div></div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card card-sm"><div class="card-body"><div class="text-secondary">Total Alpha</div><div class="fs-2 fw-bold text-danger">{{ $totalAlpha ?? 0 }}</div></div></div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter">
                        <thead><tr><th>Karyawan</th><th>NIK</th><th>Departemen</th><th>Hadir</th><th>Telat</th><th>Alpha</th><th>Persentase</th></tr></thead>
                        <tbody>
                            @forelse(($attendances ?? []) as $attendance)
                                <tr><td>{{ $attendance->employee?->full_name ?? '-' }}</td><td>{{ $attendance->employee?->nik ?? '-' }}</td><td>{{ $attendance->employee?->department?->name ?? '-' }}</td><td>{{ $attendance->status === 'hadir' ? 1 : 0 }}</td><td>{{ $attendance->status === 'telat' ? 1 : 0 }}</td><td>{{ $attendance->status === 'alpha' ? 1 : 0 }}</td><td>-</td></tr>
                            @empty
                                <tr><td colspan="7" class="text-center py-4"><div class="empty"><p class="empty-title">Pilih periode untuk menampilkan data</p></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane" id="tab-late">
                <form method="GET" action="{{ route('reports.late') }}" class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date', now()->startOfMonth()->toDateString()) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date', now()->toDateString()) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Karyawan</label>
                        <select name="employee_id" class="form-select">
                            <option value="">Semua</option>
                            @foreach($employees ?? [] as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary"><i class="ti ti-eye me-1"></i>Tampilkan</button>
                        <a href="{{ route('reports.export-csv', ['type' => 'daily', 'date' => request('date', now()->toDateString())]) }}" class="btn btn-outline-success"><i class="ti ti-file-spreadsheet me-1"></i>CSV</a>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-vcenter">
                        <thead><tr><th>Tanggal</th><th>Karyawan</th><th>NIK</th><th>Check In</th><th>Telat (menit)</th></tr></thead>
                        <tbody>
                            @forelse(($lateAttendances ?? []) as $attendance)
                                <tr><td>{{ $attendance->date?->format('d/m/Y') ?? '-' }}</td><td>{{ $attendance->employee?->full_name ?? '-' }}</td><td>{{ $attendance->employee?->nik ?? '-' }}</td><td>{{ $attendance->check_in?->format('H:i:s') ?? '-' }}</td><td><span class="text-danger">{{ $attendance->late_minutes ?? 0 }} menit</span></td></tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-4"><div class="empty"><p>Tidak ada data keterlambatan</p></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane" id="tab-overtime">
                <form method="GET" action="{{ route('reports.overtime') }}" class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date', now()->startOfMonth()->toDateString()) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date', now()->toDateString()) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Karyawan</label>
                        <select name="employee_id" class="form-select">
                            <option value="">Semua</option>
                            @foreach($employees ?? [] as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100"><i class="ti ti-eye me-1"></i>Tampilkan</button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-vcenter">
                        <thead><tr><th>Tanggal</th><th>Karyawan</th><th>Mulai</th><th>Selesai</th><th>Total Jam</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse(($overtimeReports ?? []) as $overtime)
                                <tr><td>{{ $overtime->date?->format('d/m/Y') ?? '-' }}</td><td>{{ $overtime->employee?->full_name ?? '-' }}</td><td>{{ $overtime->start_time?->format('H:i') ?? '-' }}</td><td>{{ $overtime->end_time?->format('H:i') ?? '-' }}</td><td>{{ $overtime->total_minutes ? intdiv($overtime->total_minutes, 60).'j '.($overtime->total_minutes % 60).'m' : '-' }}</td><td><span class="badge bg-success">{{ ucfirst($overtime->status) }}</span></td></tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-4"><div class="empty"><p>Tidak ada data lembur</p></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane" id="tab-leave">
                <form method="GET" action="{{ route('reports.leave') }}" class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date', now()->startOfMonth()->toDateString()) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date', now()->toDateString()) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Karyawan</label>
                        <select name="employee_id" class="form-select">
                            <option value="">Semua</option>
                            @foreach($employees ?? [] as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100"><i class="ti ti-eye me-1"></i>Tampilkan</button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-vcenter">
                        <thead><tr><th>Karyawan</th><th>Tipe Cuti</th><th>Tanggal</th><th>Hari</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse(($leaveReports ?? []) as $leave)
                                <tr><td>{{ $leave->employee?->full_name ?? '-' }}</td><td>{{ $leave->leaveType?->name ?? '-' }}</td><td>{{ $leave->start_date?->format('d/m/Y') ?? '-' }} - {{ $leave->end_date?->format('d/m/Y') ?? '-' }}</td><td>{{ $leave->total_days }}</td><td><span class="badge bg-success">{{ ucfirst($leave->status) }}</span></td></tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-4"><div class="empty"><p>Tidak ada data cuti</p></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const hash = window.location.hash;
    if (hash) {
        const tab = document.querySelector('[data-bs-toggle="tab"][href="' + hash + '"]');
        if (tab) tab.click();
    }
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function(el) {
        el.addEventListener('shown.bs.tab', function(e) {
            history.replaceState(null, null, e.target.getAttribute('href'));
        });
    });
})();
</script>
@endpush

@extends('layouts.app')
@section('title', 'Rekap Absensi')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Rekap Absensi</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a href="{{ route('reports.export-csv', ['type' => 'excel', 'date' => request('date', now()->toDateString())]) }}" class="btn btn-outline-success">
                    <i class="ti ti-file-spreadsheet me-2"></i>Excel
                </a>
                <a href="{{ route('reports.export-csv', ['type' => 'pdf', 'date' => request('date', now()->toDateString())]) }}" class="btn btn-outline-danger">
                    <i class="ti ti-file-pdf me-2"></i>PDF
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row row-deck row-cards mb-3">
    <div class="col-sm-4">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-success text-white avatar">
                            <i class="ti ti-user-check"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">{{ $attendances->where('status', 'hadir')->count() }}</div>
                        <div class="text-secondary">Hadir Hari Ini</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-warning text-white avatar">
                            <i class="ti ti-clock-alert"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">{{ $attendances->where('status', 'telat')->count() }}</div>
                        <div class="text-secondary">Telat Hari Ini</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-danger text-white avatar">
                            <i class="ti ti-user-x"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">{{ $attendances->where('status', 'alpha')->count() }}</div>
                        <div class="text-secondary">Alpha Hari Ini</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body border-bottom">
        <form method="GET" action="{{ route('attendance.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Tanggal</label>
                <input type="date" name="date" class="form-control" value="{{ request('date', now()->toDateString()) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Karyawan</label>
                <select name="employee_id" class="form-select">
                    <option value="">Semua Karyawan</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }} - {{ $emp->nik }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="telat" {{ request('status') == 'telat' ? 'selected' : '' }}>Telat</option>
                    <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                    <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                    <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="cuti" {{ request('status') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Departemen</label>
                <select name="department_id" class="form-select">
                    <option value="">Semua</option>
                    @foreach($employees->unique('department_id') as $emp)
                        @if($emp->department)
                            <option value="{{ $emp->department->id }}" {{ request('department_id') == $emp->department->id ? 'selected' : '' }}>{{ $emp->department->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="ti ti-filter me-2"></i>Filter
                </button>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Karyawan</th>
                    <th>NIK</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Status</th>
                    <th>Tipe</th>
                    <th>Durasi</th>
                    <th>Telat</th>
                    <th class="w-1">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->date->format('d/m/Y') }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs me-2" style="background-image: url({{ $attendance->employee?->photo ? asset('storage/'.$attendance->employee->photo) : '' }})"></span>
                                {{ $attendance->employee?->full_name ?? '-' }}
                            </div>
                        </td>
                        <td>{{ $attendance->employee?->nik ?? '-' }}</td>
                        <td>{{ $attendance->check_in?->format('H:i:s') ?? '-' }}</td>
                        <td>{{ $attendance->check_out?->format('H:i:s') ?? '-' }}</td>
                        <td>
                            @php
                                $statusClasses = ['hadir' => 'bg-success', 'telat' => 'bg-orange', 'alpha' => 'bg-danger', 'izin' => 'bg-info', 'sakit' => 'bg-warning', 'cuti' => 'bg-purple'];
                                $statusLabels = ['hadir' => 'Hadir', 'telat' => 'Telat', 'alpha' => 'Alpha', 'izin' => 'Izin', 'sakit' => 'Sakit', 'cuti' => 'Cuti'];
                            @endphp
                            <span class="badge {{ $statusClasses[$attendance->status] ?? 'bg-secondary' }}">
                                {{ $statusLabels[$attendance->status] ?? $attendance->status }}
                            </span>
                        </td>
                        <td>
                            @php
                                $typeClasses = ['wfo' => 'bg-blue', 'waf' => 'bg-green', 'wfh' => 'bg-indigo'];
                                $typeLabels = ['wfo' => 'WFO', 'waf' => 'WAF', 'wfh' => 'WFH'];
                            @endphp
                            <span class="badge {{ $typeClasses[$attendance->attendance_type] ?? 'bg-secondary' }}">
                                {{ $typeLabels[$attendance->attendance_type] ?? $attendance->attendance_type }}
                            </span>
                        </td>
                        <td>{{ $attendance->duration_formatted ?? '-' }}</td>
                        <td>
                            @if($attendance->late_minutes > 0)
                                <span class="text-danger">{{ $attendance->late_minutes }} menit</span>
                            @else
                                <span class="text-secondary">-</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('attendance.show', $attendance) }}" class="btn btn-sm btn-outline-primary">
                                <i class="ti ti-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-4">
                            <div class="empty">
                                <div class="empty-icon"><i class="ti ti-clipboard-list fs-1"></i></div>
                                <p class="empty-title">Tidak ada data absensi</p>
                                <p class="empty-subtitle text-secondary">Belum ada catatan absensi untuk periode ini.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($attendances->hasPages())
        <div class="card-footer d-flex align-items-center justify-content-center">
            {{ $attendances->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

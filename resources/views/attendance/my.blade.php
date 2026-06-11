@extends('layouts.app')
@section('title', 'Absensi Saya')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Absensi Saya</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            @if(!$todayAttendance || !$todayAttendance->check_in)
                <a href="{{ route('attendance.check-in') }}" class="btn btn-success">
                    <i class="ti ti-login me-2"></i>Check In
                </a>
            @elseif(!$todayAttendance->check_out)
                <a href="{{ route('attendance.check-out', $todayAttendance) }}" class="btn btn-warning">
                    <i class="ti ti-logout me-2"></i>Check Out
                </a>
            @endif
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
                        <div class="text-secondary">Hadir</div>
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
                        <div class="text-secondary">Telat</div>
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
                        <div class="text-secondary">Alpha</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body border-bottom">
        <form method="GET" action="{{ route('attendance.my') }}" class="row g-3">
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
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="ti ti-filter me-2"></i>Tampilkan
                </button>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
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
                        <td colspan="8" class="text-center py-4">
                            <div class="empty">
                                <div class="empty-icon"><i class="ti ti-clipboard-list fs-1"></i></div>
                                <p class="empty-title">Belum ada absensi</p>
                                <p class="empty-subtitle text-secondary">Belum ada catatan absensi bulan ini.</p>
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

@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Home</div>
                <h2 class="page-title">Dashboard</h2>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        @if(auth()->user()->role && in_array(auth()->user()->role->name, ['super_admin', 'hr']))
        <div class="row row-deck row-cards mb-4">
            <div class="col-sm-6 col-lg-2">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Hadir</div>
                            <div class="ms-auto lh-1">
                                <span class="badge bg-green text-green-fg">Hari Ini</span>
                            </div>
                        </div>
                        <div class="h1 mb-3 text-green">{{ $totalHadir ?? 0 }}</div>
                        <div class="d-flex align-items-center">
                            <div class="me-2 text-green">
                                <span class="ti ti-user-check"></span>
                            </div>
                            <div class="text-secondary">Karyawan hadir hari ini</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-2">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Telat</div>
                            <div class="ms-auto lh-1">
                                <span class="badge bg-orange text-orange-fg">Hari Ini</span>
                            </div>
                        </div>
                        <div class="h1 mb-3 text-orange">{{ $totalTelat ?? 0 }}</div>
                        <div class="d-flex align-items-center">
                            <div class="me-2 text-orange">
                                <span class="ti ti-clock-exclamation"></span>
                            </div>
                            <div class="text-secondary">Karyawan telat hari ini</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-2">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Tidak Hadir</div>
                            <div class="ms-auto lh-1">
                                <span class="badge bg-red text-red-fg">Hari Ini</span>
                            </div>
                        </div>
                        <div class="h1 mb-3 text-red">{{ $totalTidakHadir ?? 0 }}</div>
                        <div class="d-flex align-items-center">
                            <div class="me-2 text-red">
                                <span class="ti ti-user-x"></span>
                            </div>
                            <div class="text-secondary">Alpha/tidak masuk</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-2">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Cuti</div>
                            <div class="ms-auto lh-1">
                                <span class="badge bg-blue text-blue-fg">Hari Ini</span>
                            </div>
                        </div>
                        <div class="h1 mb-3 text-blue">{{ $totalCuti ?? 0 }}</div>
                        <div class="d-flex align-items-center">
                            <div class="me-2 text-blue">
                                <span class="ti ti-calendar-off"></span>
                            </div>
                            <div class="text-secondary">Karyawan cuti hari ini</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-2">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total WFH</div>
                            <div class="ms-auto lh-1">
                                <span class="badge bg-purple text-purple-fg">Hari Ini</span>
                            </div>
                        </div>
                        <div class="h1 mb-3 text-purple">{{ $totalWFH ?? 0 }}</div>
                        <div class="d-flex align-items-center">
                            <div class="me-2 text-purple">
                                <span class="ti ti-home"></span>
                            </div>
                            <div class="text-secondary">Work From Home</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-2">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Lembur</div>
                            <div class="ms-auto lh-1">
                                <span class="badge bg-teal text-teal-fg">Hari Ini</span>
                            </div>
                        </div>
                        <div class="h1 mb-3 text-teal">{{ $totalLembur ?? 0 }}</div>
                        <div class="d-flex align-items-center">
                            <div class="me-2 text-teal">
                                <span class="ti ti-clock-plus"></span>
                            </div>
                            <div class="text-secondary">Pengajuan lembur</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row row-deck row-cards mb-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Grafik Kehadiran 30 Hari Terakhir</h3>
                    </div>
                    <div class="card-body">
                        <div id="chart-attendance" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Distribusi Kehadiran</h3>
                    </div>
                    <div class="card-body">
                        <div id="chart-donut" style="height: 250px;"></div>
                        <div class="mt-3">
                            <div class="row">
                                <div class="col-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-green me-2"></span>
                                        <span>Hadir</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-orange me-2"></span>
                                        <span>Telat</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-red me-2"></span>
                                        <span>Alpha</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-blue me-2"></span>
                                        <span>Cuti</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-yellow me-2"></span>
                                        <span>Sakit</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-purple me-2"></span>
                                        <span>Izin</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row row-deck row-cards mb-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Absensi Terbaru</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Tanggal</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($recentAttendances) && (is_object($recentAttendances) ? $recentAttendances->count() > 0 : count($recentAttendances) > 0))
                                    @foreach($recentAttendances as $attendance)
                                    <tr>
                                        <td>
                                            <div class="d-flex py-1 align-items-center">
                                                <span class="avatar avatar-sm me-2 rounded-circle bg-primary text-white">{{ substr($attendance->employee->name ?? '-', 0, 1) }}</span>
                                                <div>
                                                    <div class="font-weight-medium">{{ $attendance->employee->name ?? '-' }}</div>
                                                    <div class="text-secondary">{{ $attendance->employee->position->name ?? '-' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $attendance->date->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $attendance->check_in_status == 'on_time' ? 'green' : 'orange' }}">{{ $attendance->check_in ?? '-' }}</span>
                                        </td>
                                        <td>{{ $attendance->check_out ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $attendance->status == 'present' ? 'green' : ($attendance->status == 'late' ? 'orange' : 'red') }}">
                                                {{ ucfirst($attendance->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                <tr>
                                    <td colspan="5" class="text-center text-secondary py-4">
                                        <span class="ti ti-inbox fs-2"></span>
                                        <div class="mt-2">Belum ada data absensi</div>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Menunggu Persetujuan</h3>
                    </div>
                    <div class="card-body">
                        @if((isset($pendingLeaves) && $pendingLeaves->count() > 0) || (isset($pendingOvertimes) && $pendingOvertimes->count() > 0))
                            @if(isset($pendingLeaves) && $pendingLeaves->count() > 0)
                            <h4 class="mb-3">Cuti</h4>
                            <div class="list-group list-group-flush">
                                @foreach($pendingLeaves as $leave)
                                <div class="list-group-item px-0">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="avatar rounded-circle bg-blue text-white"><span class="ti ti-calendar-off"></span></span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">{{ $leave->employee->name ?? '-' }}</div>
                                            <div class="text-secondary small">{{ $leave->start_date->format('d/m') }} - {{ $leave->end_date->format('d/m/Y') }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <span class="badge bg-yellow">Pending</span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                            @if(isset($pendingOvertimes) && $pendingOvertimes->count() > 0)
                            <h4 class="mb-3 mt-4">Lembur</h4>
                            <div class="list-group list-group-flush">
                                @foreach($pendingOvertimes as $overtime)
                                <div class="list-group-item px-0">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="avatar rounded-circle bg-teal text-white"><span class="ti ti-clock-plus"></span></span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">{{ $overtime->employee->name ?? '-' }}</div>
                                            <div class="text-secondary small">{{ $overtime->date->format('d/m/Y') }} | {{ $overtime->duration }} jam</div>
                                        </div>
                                        <div class="col-auto">
                                            <span class="badge bg-yellow">Pending</span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        @else
                        <div class="text-center text-secondary py-4">
                            <span class="ti ti-check-circle fs-2"></span>
                            <div class="mt-2">Tidak ada yang menunggu persetujuan</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row row-deck row-cards">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Karyawan Cuti Hari Ini</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Department</th>
                                    <th>Tipe Cuti</th>
                                    <th>Sampai Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($employeeOnLeave) && count($employeeOnLeave) > 0)
                                    @foreach($employeeOnLeave as $leave)
                                    <tr>
                                        <td>
                                            <div class="d-flex py-1 align-items-center">
                                                <span class="avatar avatar-sm me-2 rounded-circle bg-blue text-white">{{ substr($leave->employee->name ?? '-', 0, 1) }}</span>
                                                <div>
                                                    <div class="font-weight-medium">{{ $leave->employee->name ?? '-' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $leave->employee->department->name ?? '-' }}</td>
                                        <td>                                        <span class="badge bg-blue">{{ $leave->leaveType->name ?? 'Cuti' }}</span></td>
                                        <td>{{ $leave->end_date->format('d/m/Y') }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                <tr>
                                    <td colspan="4" class="text-center text-secondary py-4">
                                        Tidak ada karyawan cuti hari ini
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Karyawan Telat Hari Ini</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Department</th>
                                    <th>Check In</th>
                                    <th>Keterlambatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($lateAlerts) && count($lateAlerts) > 0)
                                    @foreach($lateAlerts as $attendance)
                                    <tr>
                                        <td>
                                            <div class="d-flex py-1 align-items-center">
                                                <span class="avatar avatar-sm me-2 rounded-circle bg-orange text-white">{{ substr($attendance->employee->name ?? '-', 0, 1) }}</span>
                                                <div>
                                                    <div class="font-weight-medium">{{ $attendance->employee->name ?? '-' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $attendance->employee->department->name ?? '-' }}</td>
                                        <td><span class="badge bg-orange">{{ $attendance->check_in }}</span></td>
                                        <td class="text-orange">{{ $attendance->late_minutes ?? 0 }} menit</td>
                                    </tr>
                                    @endforeach
                                @else
                                <tr>
                                    <td colspan="4" class="text-center text-secondary py-4">
                                        Tidak ada karyawan telat hari ini
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="row row-deck row-cards mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Hadir</div>
                            <div class="ms-auto"><span class="badge bg-green text-green-fg">Hari Ini</span></div>
                        </div>
                        <div class="h1 mb-3 text-green">{{ $totalHadir ?? 0 }}</div>
                        <div class="d-flex"><span class="ti ti-user-check me-2 text-green"></span><span class="text-secondary">Status kehadiran</span></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Telat</div>
                            <div class="ms-auto"><span class="badge bg-orange text-orange-fg">Hari Ini</span></div>
                        </div>
                        <div class="h1 mb-3 text-orange">{{ $totalTelat ?? 0 }}</div>
                        <div class="d-flex"><span class="ti ti-clock-exclamation me-2 text-orange"></span><span class="text-secondary">Keterlambatan</span></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Sisa Cuti</div>
                            <div class="ms-auto"><span class="badge bg-blue text-blue-fg">Tahunan</span></div>
                        </div>
                        <div class="h1 mb-3 text-blue">12</div>
                        <div class="d-flex"><span class="ti ti-calendar me-2 text-blue"></span><span class="text-secondary">Hari tersisa</span></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Lembur</div>
                            <div class="ms-auto"><span class="badge bg-teal text-teal-fg">Bulan Ini</span></div>
                        </div>
                        <div class="h1 mb-3 text-teal">{{ $totalLembur ?? 0 }}</div>
                        <div class="d-flex"><span class="ti ti-clock-plus me-2 text-teal"></span><span class="text-secondary">Total jam lembur</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row row-deck row-cards">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Absensi 30 Hari Terakhir</h3>
                    </div>
                    <div class="card-body">
                        <div id="chart-attendance" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Aksi Cepat</h3>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="row align-items-center">
                                    <div class="col-auto"><span class="avatar rounded-circle bg-green text-white"><span class="ti ti-fingerprint"></span></span></div>
                                    <div class="col"><div class="font-weight-medium">Check In</div><div class="text-secondary small">Catat kedatangan</div></div>
                                </div>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="row align-items-center">
                                    <div class="col-auto"><span class="avatar rounded-circle bg-red text-white"><span class="ti ti-logout"></span></span></div>
                                    <div class="col"><div class="font-weight-medium">Check Out</div><div class="text-secondary small">Catat kepulangan</div></div>
                                </div>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="row align-items-center">
                                    <div class="col-auto"><span class="avatar rounded-circle bg-blue text-white"><span class="ti ti-calendar-off"></span></span></div>
                                    <div class="col"><div class="font-weight-medium">Ajukan Cuti</div><div class="text-secondary small">Pengajuan cuti</div></div>
                                </div>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="row align-items-center">
                                    <div class="col-auto"><span class="avatar rounded-circle bg-teal text-white"><span class="ti ti-clock-plus"></span></span></div>
                                    <div class="col"><div class="font-weight-medium">Ajukan Lembur</div><div class="text-secondary small">Pengajuan lembur</div></div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    const textColor = isDark ? '#9aa4af' : '#626976';
    const gridColor = isDark ? 'rgba(154, 164, 175, 0.1)' : 'rgba(98, 105, 118, 0.1)';
    
    const ctxAttendance = document.getElementById('chart-attendance').getContext('2d');
    const chartLabels = @json($attendanceChartLabels ?? []);
    const chartData = @json($attendanceChartData ?? []);
    
    new Chart(ctxAttendance, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Kehadiran (%)',
                data: chartData,
                backgroundColor: 'rgba(32, 201, 151, 0.8)',
                borderColor: 'rgba(32, 201, 151, 1)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: { color: textColor },
                    grid: { color: gridColor }
                },
                x: {
                    ticks: { color: textColor },
                    grid: { color: gridColor }
                }
            }
        }
    });

    const donutLabels = @json($donutLabels ?? []);
    const donutData = @json($donutData ?? []);
    const donutColors = ['#20c997', '#fd7e14', '#d63939', '#4299e1', '#f59e0b', '#805ad5'];
    
    const ctxDonut = document.getElementById('chart-donut').getContext('2d');
    new Chart(ctxDonut, {
        type: 'doughnut',
        data: {
            labels: donutLabels,
            datasets: [{
                data: donutData,
                backgroundColor: donutColors,
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>
@endpush

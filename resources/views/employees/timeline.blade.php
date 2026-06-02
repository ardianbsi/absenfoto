@extends('layouts.app')
@section('title', 'Timeline Karyawan')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Timeline {{ $employee->full_name }}</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('employees.show', $employee) }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="mb-3">
                    <span class="avatar avatar-xl" style="background-image: url({{ $employee->photo ? asset('storage/'.$employee->photo) : '' }})"></span>
                </div>
                <h3>{{ $employee->full_name }}</h3>
                <p class="text-secondary mb-1">{{ $employee->position?->name ?? '-' }}</p>
                <p class="text-secondary mb-1">{{ $employee->department?->name ?? '-' }}</p>
                <p class="text-secondary mb-0">NIK: {{ $employee->nik }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Aktivitas</h3>
                <div class="card-actions">
                    <form method="GET" action="{{ request()->url() }}" class="row g-2">
                        <div class="col-auto">
                            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date', now()->startOfMonth()->toDateString()) }}">
                        </div>
                        <div class="col-auto">
                            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date', now()->toDateString()) }}">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body">
                @php
                    $timeline = collect();

                    $joinDate = $employee->join_date;
                    if($joinDate) {
                        $timeline->push([
                            'date' => $joinDate,
                            'type' => 'join',
                            'title' => 'Bergabung',
                            'description' => $employee->full_name . ' bergabung sebagai ' . ($employee->position?->name ?? 'Karyawan'),
                            'icon' => 'ti-user-plus',
                            'color' => 'bg-primary',
                        ]);
                    }

                    $attendances = $employee->attendances()
                        ->whereBetween('date', [request('start_date', now()->startOfMonth()->toDateString()), request('end_date', now()->toDateString())])
                        ->orderBy('date', 'desc')
                        ->get();

                    foreach($attendances as $attendance) {
                        $statusLabels = ['hadir' => 'Hadir', 'telat' => 'Telat', 'alpha' => 'Alpha', 'izin' => 'Izin', 'sakit' => 'Sakit', 'cuti' => 'Cuti'];
                        $statusColors = ['hadir' => 'bg-success', 'telat' => 'bg-warning', 'alpha' => 'bg-danger', 'izin' => 'bg-info', 'sakit' => 'bg-warning', 'cuti' => 'bg-purple'];
                        $timeline->push([
                            'date' => $attendance->date,
                            'type' => 'attendance',
                            'title' => 'Absensi: ' . ($statusLabels[$attendance->status] ?? $attendance->status),
                            'description' => 'Check In: ' . ($attendance->check_in?->format('H:i:s') ?? '-') . ' | Check Out: ' . ($attendance->check_out?->format('H:i:s') ?? '-') . ' | Durasi: ' . ($attendance->duration_formatted ?? '-'),
                            'icon' => 'ti-clipboard-check',
                            'color' => $statusColors[$attendance->status] ?? 'bg-secondary',
                        ]);
                    }

                    $leaves = $employee->leaveRequests()
                        ->whereBetween('start_date', [request('start_date', now()->startOfMonth()->toDateString()), request('end_date', now()->toDateString())])
                        ->orderBy('created_at', 'desc')
                        ->get();

                    foreach($leaves as $leave) {
                        $statusLabels = ['pending' => 'Pending', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];
                        $statusColors = ['pending' => 'bg-warning', 'approved' => 'bg-success', 'rejected' => 'bg-danger'];
                        $timeline->push([
                            'date' => $leave->created_at,
                            'type' => 'leave',
                            'title' => 'Cuti: ' . ($leave->leaveType?->name ?? '-') . ' - ' . ($statusLabels[$leave->status] ?? $leave->status),
                            'description' => $leave->start_date->format('d/m/Y') . ' - ' . $leave->end_date->format('d/m/Y') . ' (' . $leave->total_days . ' hari)',
                            'icon' => 'ti-calendar-off',
                            'color' => $statusColors[$leave->status] ?? 'bg-secondary',
                        ]);
                    }

                    $overtimes = $employee->overtimeRequests()
                        ->whereBetween('date', [request('start_date', now()->startOfMonth()->toDateString()), request('end_date', now()->toDateString())])
                        ->orderBy('created_at', 'desc')
                        ->get();

                    foreach($overtimes as $overtime) {
                        $statusLabels = ['pending' => 'Pending', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];
                        $statusColors = ['pending' => 'bg-warning', 'approved' => 'bg-success', 'rejected' => 'bg-danger'];
                        $timeline->push([
                            'date' => $overtime->created_at,
                            'type' => 'overtime',
                            'title' => 'Lembur: ' . ($statusLabels[$overtime->status] ?? $overtime->status),
                            'description' => $overtime->date->format('d/m/Y') . ' | ' . ($overtime->start_time?->format('H:i') ?? '-') . ' - ' . ($overtime->end_time?->format('H:i') ?? '-'),
                            'icon' => 'ti-clock-hour',
                            'color' => $statusColors[$overtime->status] ?? 'bg-secondary',
                        ]);
                    }

                    $timeline = $timeline->sortByDesc('date')->values();
                @endphp

                @if($timeline->count() > 0)
                    <ul class="steps steps-vertical" id="timeline-list">
                        @foreach($timeline as $item)
                            <li class="step-item">
                                <div class="d-flex align-items-start">
                                    <div class="me-3">
                                        <span class="avatar avatar-sm {{ $item['color'] }} text-white">
                                            <i class="ti {{ $item['icon'] }}"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between">
                                            <h4 class="mb-0">{{ $item['title'] }}</h4>
                                            <small class="text-secondary">{{ $item['date'] instanceof \Carbon\Carbon ? $item['date']->format('d/m/Y H:i') : $item['date']->format('d/m/Y') }}</small>
                                        </div>
                                        <p class="text-secondary mb-0">{{ $item['description'] }}</p>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="empty">
                        <div class="empty-icon"><i class="ti ti-timeline fs-1"></i></div>
                        <p class="empty-title">Tidak ada aktivitas</p>
                        <p class="empty-subtitle text-secondary">Tidak ada aktivitas untuk periode ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    #timeline-list .step-item {
        position: relative;
        padding-left: 3rem;
        padding-bottom: 1.5rem;
        border-left: 2px solid var(--tblr-border-color);
        margin-left: 1.25rem;
    }
    #timeline-list .step-item:last-child {
        border-left: 2px solid transparent;
        padding-bottom: 0;
    }
    #timeline-list .step-item .avatar {
        position: absolute;
        left: -1.25rem;
        top: 0;
        z-index: 1;
    }
    [data-bs-theme="dark"] #timeline-list .step-item {
        border-left-color: rgba(255,255,255,0.1);
    }
</style>
@endpush

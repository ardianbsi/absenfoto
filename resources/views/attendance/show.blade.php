@extends('layouts.app')
@section('title', 'Detail Absensi')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Detail Absensi</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary">
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
                    <span class="avatar avatar-xl" style="background-image: url({{ $attendance->employee?->photo ? asset('storage/'.$attendance->employee->photo) : '' }})"></span>
                </div>
                <h3 class="mb-1">{{ $attendance->employee?->full_name ?? '-' }}</h3>
                <p class="text-secondary mb-1">NIK: {{ $attendance->employee?->nik ?? '-' }}</p>
                <p class="text-secondary mb-1">{{ $attendance->employee?->department?->name ?? '-' }} - {{ $attendance->employee?->position?->name ?? '-' }}</p>
                <p class="text-secondary mb-0">{{ $attendance->date->format('l, d F Y') }}</p>
                @php
                    $statusClasses = ['hadir' => 'bg-success', 'telat' => 'bg-orange', 'alpha' => 'bg-danger', 'izin' => 'bg-info', 'sakit' => 'bg-warning', 'cuti' => 'bg-purple'];
                    $statusLabels = ['hadir' => 'Hadir', 'telat' => 'Telat', 'alpha' => 'Alpha', 'izin' => 'Izin', 'sakit' => 'Sakit', 'cuti' => 'Cuti'];
                @endphp
                <div class="mt-2">
                    <span class="badge {{ $statusClasses[$attendance->status] ?? 'bg-secondary' }} fs-6">
                        {{ $statusLabels[$attendance->status] ?? $attendance->status }}
                    </span>
                    @php
                        $typeClasses = ['wfo' => 'bg-blue', 'waf' => 'bg-green', 'wfh' => 'bg-indigo'];
                        $typeLabels = ['wfo' => 'WFO', 'waf' => 'WAF', 'wfh' => 'WFH'];
                    @endphp
                    <span class="badge {{ $typeClasses[$attendance->attendance_type] ?? 'bg-secondary' }} fs-6 ms-1">
                        {{ $typeLabels[$attendance->attendance_type] ?? $attendance->attendance_type }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="row">
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Check In</h4>
                    </div>
                    <div class="card-body">
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">Waktu</div>
                                <div class="datagrid-content">{{ $attendance->check_in?->format('H:i:s') ?? '-' }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Lokasi</div>
                                <div class="datagrid-content">
                                    @if($attendance->check_in_latitude && $attendance->check_in_longitude)
                                        <a href="https://www.google.com/maps?q={{ $attendance->check_in_latitude }},{{ $attendance->check_in_longitude }}" target="_blank" class="text-primary">
                                            {{ $attendance->check_in_latitude }}, {{ $attendance->check_in_longitude }}
                                            <i class="ti ti-external-link"></i>
                                        </a>
                                    @else
                                        <span class="text-secondary">-</span>
                                    @endif
                                </div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">IP Address</div>
                                <div class="datagrid-content">{{ $attendance->check_in_ip ?? '-' }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Device</div>
                                <div class="datagrid-content">{{ $attendance->check_in_device ?? '-' }}</div>
                            </div>
                            @if($attendance->check_in_note)
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Catatan</div>
                                    <div class="datagrid-content">{{ $attendance->check_in_note }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @if($attendance->check_in_photo)
                        <div class="card-footer text-center">
                            <a href="{{ asset('storage/'.$attendance->check_in_photo) }}" target="_blank">
                                <img src="{{ asset('storage/'.$attendance->check_in_photo) }}" class="img-fluid rounded" style="max-height:200px;" alt="Check In Photo">
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Check Out</h4>
                    </div>
                    <div class="card-body">
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">Waktu</div>
                                <div class="datagrid-content">{{ $attendance->check_out?->format('H:i:s') ?? '-' }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Lokasi</div>
                                <div class="datagrid-content">
                                    @if($attendance->check_out_latitude && $attendance->check_out_longitude)
                                        <a href="https://www.google.com/maps?q={{ $attendance->check_out_latitude }},{{ $attendance->check_out_longitude }}" target="_blank" class="text-primary">
                                            {{ $attendance->check_out_latitude }}, {{ $attendance->check_out_longitude }}
                                            <i class="ti ti-external-link"></i>
                                        </a>
                                    @else
                                        <span class="text-secondary">-</span>
                                    @endif
                                </div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">IP Address</div>
                                <div class="datagrid-content">{{ $attendance->check_out_ip ?? '-' }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Device</div>
                                <div class="datagrid-content">{{ $attendance->check_out_device ?? '-' }}</div>
                            </div>
                            @if($attendance->check_out_note)
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Catatan</div>
                                    <div class="datagrid-content">{{ $attendance->check_out_note }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @if($attendance->check_out_photo)
                        <div class="card-footer text-center">
                            <a href="{{ asset('storage/'.$attendance->check_out_photo) }}" target="_blank">
                                <img src="{{ asset('storage/'.$attendance->check_out_photo) }}" class="img-fluid rounded" style="max-height:200px;" alt="Check Out Photo">
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h4 class="card-title">Ringkasan</h4>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-4 text-center">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="text-secondary mb-1">Durasi</div>
                                <div class="fs-2 fw-bold">{{ $attendance->duration_formatted ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="text-secondary mb-1">Telat</div>
                                <div class="fs-2 fw-bold {{ $attendance->late_minutes > 0 ? 'text-danger' : '' }}">{{ $attendance->late_minutes > 0 ? $attendance->late_minutes.' menit' : '-' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="text-secondary mb-1">Lembur</div>
                                <div class="fs-2 fw-bold">{{ $attendance->overtime_minutes > 0 ? $attendance->overtime_minutes.' menit' : '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h4 class="card-title">Aktivitas</h4>
            </div>
            <div class="card-body">
                @forelse($attendance->logs as $log)
                    <div class="d-flex mb-2">
                        <span class="me-2">
                            <i class="ti ti-circle-filled fs-6 {{ $log->action === 'check_in' ? 'text-success' : 'text-warning' }}"></i>
                        </span>
                        <div>
                            <p class="mb-0">{{ $log->description }}</p>
                            <small class="text-secondary">{{ $log->created_at->format('d/m/Y H:i:s') }} - {{ $log->ip_address ?? '-' }}</small>
                        </div>
                    </div>
                @empty
                    <p class="text-secondary text-center mb-0">Tidak ada aktivitas</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

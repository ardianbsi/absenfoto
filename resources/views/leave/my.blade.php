@extends('layouts.app')
@section('title', 'Pengajuan Saya')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Pengajuan Cuti / Izin Saya</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('leaves.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-2"></i>Ajukan Baru
            </a>
        </div>
    </div>
</div>

<div class="row row-deck row-cards mb-3">
    @php
        $usedQuota = 0;
        $totalQuota = 0;
    @endphp
    @foreach($employee->leaveRequests()->whereYear('start_date', now()->year)->whereIn('status', ['approved', 'pending'])->get()->groupBy('leave_type_id') as $typeId => $leavesByType)
        @php
            $type = \App\Models\LeaveType::find($typeId);
        @endphp
        @if($type)
            <div class="col-sm-4">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="font-weight-medium">{{ $type->name }}</div>
                                <div class="text-secondary small">{{ $leavesByType->sum('total_days') }}/{{ $type->quota }} hari terpakai</div>
                            </div>
                            <div class="text-end">
                                <div class="fs-2 fw-bold text-primary">{{ max(0, $type->quota - $leavesByType->sum('total_days')) }}</div>
                                <div class="text-secondary small">sisa</div>
                            </div>
                        </div>
                        <div class="progress progress-xs mt-2">
                            <div class="progress-bar bg-primary" style="width: {{ $type->quota > 0 ? min(100, $leavesByType->sum('total_days') / $type->quota * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>

<div class="card">
    <div class="card-body border-bottom">
        <form method="GET" action="{{ route('leaves.my') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
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
                    <i class="ti ti-filter me-2"></i>Filter
                </button>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Tipe</th>
                    <th>Rentang Tanggal</th>
                    <th>Hari</th>
                    <th>Status</th>
                    <th>Tanggal Pengajuan</th>
                    <th class="w-1">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaves as $leave)
                    <tr>
                        <td>{{ $leave->leaveType?->name ?? '-' }}</td>
                        <td>{{ $leave->start_date->format('d/m/Y') }} - {{ $leave->end_date->format('d/m/Y') }}</td>
                        <td>{{ $leave->total_days }} hari</td>
                        <td>
                            @php
                                $statusClasses = ['pending' => 'bg-warning', 'approved' => 'bg-success', 'rejected' => 'bg-danger'];
                                $statusLabels = ['pending' => 'Pending', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];
                            @endphp
                            <span class="badge {{ $statusClasses[$leave->status] ?? 'bg-secondary' }}">
                                {{ $statusLabels[$leave->status] ?? $leave->status }}
                            </span>
                        </td>
                        <td>{{ $leave->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('leaves.show', $leave) }}" class="btn btn-sm btn-outline-primary">
                                <i class="ti ti-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="empty">
                                <div class="empty-icon"><i class="ti ti-calendar-off fs-1"></i></div>
                                <p class="empty-title">Belum ada pengajuan</p>
                                <p class="empty-subtitle text-secondary">Anda belum mengajukan cuti atau izin.</p>
                                <a href="{{ route('leaves.create') }}" class="btn btn-primary">
                                    <i class="ti ti-plus me-2"></i>Ajukan Sekarang
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($leaves->hasPages())
        <div class="card-footer d-flex align-items-center justify-content-center">
            {{ $leaves->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

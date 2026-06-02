@extends('layouts.app')
@section('title', 'Pengajuan Cuti / Izin')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Pengajuan Cuti / Izin</h2>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link {{ !request('status') ? 'active' : '' }}" href="{{ route('leaves.index') }}">
                    <i class="ti ti-list me-1"></i>Semua
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('status') === 'pending' ? 'active' : '' }}" href="{{ route('leaves.index', ['status' => 'pending']) }}">
                    <span class="badge bg-warning me-1"></span>Pending
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('status') === 'approved' ? 'active' : '' }}" href="{{ route('leaves.index', ['status' => 'approved']) }}">
                    <span class="badge bg-success me-1"></span>Disetujui
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('status') === 'rejected' ? 'active' : '' }}" href="{{ route('leaves.index', ['status' => 'rejected']) }}">
                    <span class="badge bg-danger me-1"></span>Ditolak
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body border-bottom">
        <form method="GET" action="{{ route('leaves.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Karyawan</label>
                <select name="employee_id" class="form-select">
                    <option value="">Semua Karyawan</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }} - {{ $emp->nik }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tipe Cuti</label>
                <select name="leave_type_id" class="form-select">
                    <option value="">Semua Tipe</option>
                    @foreach($leaveTypes as $type)
                        <option value="{{ $type->id }}" {{ request('leave_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="ti ti-filter me-2"></i>Filter
                </button>
            </div>
            @if(request()->anyFilled(['employee_id', 'leave_type_id', 'start_date', 'end_date', 'status']))
                <div class="col-12">
                    <a href="{{ route('leaves.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="ti ti-x me-1"></i>Hapus Filter
                    </a>
                </div>
            @endif
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Karyawan</th>
                    <th>Tipe Cuti</th>
                    <th>Rentang Tanggal</th>
                    <th>Hari</th>
                    <th>Status</th>
                    <th>Diajukan</th>
                    <th class="w-1">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaves as $leave)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs me-2" style="background-image: url({{ $leave->employee?->photo ? asset('storage/'.$leave->employee->photo) : '' }})"></span>
                                {{ $leave->employee?->full_name ?? '-' }}
                            </div>
                        </td>
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
                            <div class="btn-group">
                                <a href="{{ route('leaves.show', $leave) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="ti ti-eye"></i>
                                </a>
                                @if($leave->status === 'pending')
                                    <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#approveModal-{{ $leave->id }}">
                                        <i class="ti ti-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $leave->id }}">
                                        <i class="ti ti-x"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="empty">
                                <div class="empty-icon"><i class="ti ti-calendar-off fs-1"></i></div>
                                <p class="empty-title">Tidak ada pengajuan cuti/izin</p>
                                <p class="empty-subtitle text-secondary">Belum ada pengajuan cuti atau izin.</p>
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

@foreach($leaves->where('status', 'pending') as $leave)
    <div class="modal fade" id="approveModal-{{ $leave->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('leaves.approve', $leave) }}">
                    @csrf
                    <input type="hidden" name="status" value="approved">
                    <div class="modal-header">
                        <h5 class="modal-title">Setujui Pengajuan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Setujui pengajuan cuti/izin dari <strong>{{ $leave->employee?->full_name }}</strong>?</p>
                        <div class="mb-3">
                            <label class="form-label">Catatan (opsional)</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Setujui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="rejectModal-{{ $leave->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('leaves.approve', $leave) }}">
                    @csrf
                    <input type="hidden" name="status" value="rejected">
                    <div class="modal-header">
                        <h5 class="modal-title">Tolak Pengajuan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Tolak pengajuan cuti/izin dari <strong>{{ $leave->employee?->full_name }}</strong>?</p>
                        <div class="mb-3">
                            <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea name="rejected_reason" class="form-control" rows="3" required></textarea>
                            @error('rejected_reason')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endsection

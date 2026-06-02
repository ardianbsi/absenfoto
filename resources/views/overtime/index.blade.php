@extends('layouts.app')
@section('title', 'Lembur')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Manajemen Lembur</h2>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body border-bottom">
        <form method="GET" action="{{ route('overtimes.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Karyawan</label>
                <select name="employee_id" class="form-select">
                    <option value="">Semua</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
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
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="ti ti-filter me-2"></i>Filter
                </button>
            </div>
            @if(request()->anyFilled(['employee_id', 'status', 'start_date', 'end_date']))
                <div class="col-12">
                    <a href="{{ route('overtimes.index') }}" class="btn btn-sm btn-outline-secondary">
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
                    <th>Tanggal</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Total Jam</th>
                    <th>Status</th>
                    <th>Deskripsi</th>
                    <th class="w-1">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($overtimes as $overtime)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs me-2" style="background-image: url({{ $overtime->employee?->photo ? asset('storage/'.$overtime->employee->photo) : '' }})"></span>
                                {{ $overtime->employee?->full_name ?? '-' }}
                            </div>
                        </td>
                        <td>{{ $overtime->date->format('d/m/Y') }}</td>
                        <td>{{ $overtime->start_time?->format('H:i') ?? '-' }}</td>
                        <td>{{ $overtime->end_time?->format('H:i') ?? '-' }}</td>
                        <td>
                            @if($overtime->total_minutes)
                                {{ intdiv($overtime->total_minutes, 60) }}j {{ $overtime->total_minutes % 60 }}m
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @php
                                $sc = ['pending' => 'bg-warning', 'approved' => 'bg-success', 'rejected' => 'bg-danger'];
                                $sl = ['pending' => 'Pending', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];
                            @endphp
                            <span class="badge {{ $sc[$overtime->status] ?? 'bg-secondary' }}">{{ $sl[$overtime->status] ?? $overtime->status }}</span>
                        </td>
                        <td>{{ Str::limit($overtime->description, 30) }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('overtimes.show', $overtime) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="ti ti-eye"></i>
                                </a>
                                @if($overtime->status === 'pending')
                                    <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#approveModal-{{ $overtime->id }}">
                                        <i class="ti ti-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $overtime->id }}">
                                        <i class="ti ti-x"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="empty">
                                <div class="empty-icon"><i class="ti ti-clock-off fs-1"></i></div>
                                <p class="empty-title">Tidak ada data lembur</p>
                                <p class="empty-subtitle text-secondary">Belum ada pengajuan lembur.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($overtimes->hasPages())
        <div class="card-footer d-flex align-items-center justify-content-center">
            {{ $overtimes->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@foreach($overtimes->where('status', 'pending') as $overtime)
    <div class="modal fade" id="approveModal-{{ $overtime->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('overtimes.approve', $overtime) }}">
                    @csrf
                    <input type="hidden" name="status" value="approved">
                    <div class="modal-header">
                        <h5 class="modal-title">Setujui Lembur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Setujui pengajuan lembur dari <strong>{{ $overtime->employee?->full_name }}</strong>?</p>
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
    <div class="modal fade" id="rejectModal-{{ $overtime->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('overtimes.approve', $overtime) }}">
                    @csrf
                    <input type="hidden" name="status" value="rejected">
                    <div class="modal-header">
                        <h5 class="modal-title">Tolak Lembur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Tolak pengajuan lembur dari <strong>{{ $overtime->employee?->full_name }}</strong>?</p>
                        <div class="mb-3">
                            <label class="form-label required">Alasan Penolakan</label>
                            <textarea name="rejected_reason" class="form-control" rows="3" required></textarea>
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

@extends('layouts.app')
@section('title', 'Detail Pengajuan Cuti')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Detail Pengajuan Cuti / Izin</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('leaves.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informasi Pengajuan</h3>
                @php
                    $statusClasses = ['pending' => 'bg-warning', 'approved' => 'bg-success', 'rejected' => 'bg-danger'];
                    $statusLabels = ['pending' => 'Pending', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];
                @endphp
                <div class="card-actions">
                    <span class="badge {{ $statusClasses[$leave->status] ?? 'bg-secondary' }} fs-6">
                        {{ $statusLabels[$leave->status] ?? $leave->status }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="datagrid">
                    <div class="datagrid-item">
                        <div class="datagrid-title">Tipe Cuti</div>
                        <div class="datagrid-content">{{ $leave->leaveType?->name ?? '-' }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Tanggal Mulai</div>
                        <div class="datagrid-content">{{ $leave->start_date->format('d F Y') }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Tanggal Selesai</div>
                        <div class="datagrid-content">{{ $leave->end_date->format('d F Y') }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Total Hari</div>
                        <div class="datagrid-content">{{ $leave->total_days }} hari</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Diajukan Pada</div>
                        <div class="datagrid-content">{{ $leave->created_at->format('d F Y H:i') }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Status</div>
                        <div class="datagrid-content">
                            <span class="badge {{ $statusClasses[$leave->status] ?? 'bg-secondary' }}">
                                {{ $statusLabels[$leave->status] ?? $leave->status }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h4>Alasan</h4>
                    <p class="text-secondary">{{ $leave->reason ?? '-' }}</p>
                </div>

                @if($leave->notes)
                    <div class="mt-3">
                        <h4>Catatan</h4>
                        <p class="text-secondary">{{ $leave->notes }}</p>
                    </div>
                @endif

                @if($leave->rejected_reason)
                    <div class="mt-3">
                        <h4>Alasan Penolakan</h4>
                        <div class="alert alert-danger">{{ $leave->rejected_reason }}</div>
                    </div>
                @endif

                @if($leave->attachment)
                    <div class="mt-3">
                        <a href="{{ asset('storage/'.$leave->attachment) }}" class="btn btn-outline-primary" target="_blank">
                            <i class="ti ti-download me-2"></i>Download Lampiran
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Timeline</h3>
            </div>
            <div class="card-body">
                <ul class="steps steps-vertical">
                    <li class="step-item">
                        <div class="h4 m-0">Pengajuan Dibuat</div>
                        <div class="text-secondary">{{ $leave->created_at->format('d F Y H:i') }}</div>
                    </li>
                    @if($leave->status === 'approved')
                        <li class="step-item step-item-active">
                            <div class="h4 m-0">Disetujui</div>
                            <div class="text-secondary">
                                {{ $leave->approved_at?->format('d F Y H:i') ?? '-' }}
                                @if($leave->approver)
                                    oleh {{ $leave->approver->name }}
                                @endif
                            </div>
                        </li>
                    @elseif($leave->status === 'rejected')
                        <li class="step-item step-item-active">
                            <div class="h4 m-0">Ditolak</div>
                            <div class="text-secondary">
                                @if($leave->approver)
                                    oleh {{ $leave->approver->name }}
                                @endif
                            </div>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        @if($leave->status === 'pending')
            <div class="card mt-3">
                <div class="card-body">
                    <div class="btn-group w-100">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                            <i class="ti ti-check me-2"></i>Setujui
                        </button>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="ti ti-x me-2"></i>Tolak
                        </button>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="approveModal" tabindex="-1">
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
                                <p>Setujui pengajuan cuti/izin ini?</p>
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
            <div class="modal fade" id="rejectModal" tabindex="-1">
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
                                <p>Tolak pengajuan cuti/izin ini?</p>
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
        @endif
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informasi Karyawan</h3>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <span class="avatar avatar-xl" style="background-image: url({{ $leave->employee?->photo ? asset('storage/'.$leave->employee->photo) : '' }})"></span>
                </div>
                <h4>{{ $leave->employee?->full_name ?? '-' }}</h4>
                <p class="text-secondary mb-1">NIK: {{ $leave->employee?->nik ?? '-' }}</p>
                <p class="text-secondary mb-1">{{ $leave->employee?->department?->name ?? '-' }}</p>
                <p class="text-secondary mb-0">{{ $leave->employee?->position?->name ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')
@section('title', 'Detail Lembur')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Detail Lembur</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('overtimes.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informasi Lembur</h3>
                <div class="card-actions">
                    @php
                        $sc = ['pending' => 'bg-warning', 'approved' => 'bg-success', 'rejected' => 'bg-danger'];
                        $sl = ['pending' => 'Pending', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];
                    @endphp
                    <span class="badge {{ $sc[$overtime->status] ?? 'bg-secondary' }} fs-6">{{ $sl[$overtime->status] ?? $overtime->status }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="datagrid">
                    <div class="datagrid-item">
                        <div class="datagrid-title">Tanggal</div>
                        <div class="datagrid-content">{{ $overtime->date->format('d F Y') }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Jam Mulai</div>
                        <div class="datagrid-content">{{ $overtime->start_time?->format('H:i') ?? '-' }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Jam Selesai</div>
                        <div class="datagrid-content">{{ $overtime->end_time?->format('H:i') ?? '-' }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Total Jam</div>
                        <div class="datagrid-content">
                            @if($overtime->total_minutes)
                                {{ intdiv($overtime->total_minutes, 60) }} jam {{ $overtime->total_minutes % 60 }} menit
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Diajukan Pada</div>
                        <div class="datagrid-content">{{ $overtime->created_at->format('d F Y H:i') }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Status</div>
                        <div class="datagrid-content">
                            <span class="badge {{ $sc[$overtime->status] ?? 'bg-secondary' }}">{{ $sl[$overtime->status] ?? $overtime->status }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h4>Deskripsi Pekerjaan</h4>
                    <p class="text-secondary">{{ $overtime->description ?? '-' }}</p>
                </div>

                @if($overtime->rejected_reason)
                    <div class="mt-3">
                        <h4>Alasan Penolakan</h4>
                        <div class="alert alert-danger">{{ $overtime->rejected_reason }}</div>
                    </div>
                @endif

                @if($overtime->attachment)
                    <div class="mt-3">
                        <a href="{{ asset('storage/'.$overtime->attachment) }}" class="btn btn-outline-primary" target="_blank">
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
                        <div class="text-secondary">{{ $overtime->created_at->format('d F Y H:i') }}</div>
                    </li>
                    @if($overtime->status === 'approved')
                        <li class="step-item step-item-active">
                            <div class="h4 m-0">Disetujui</div>
                            <div class="text-secondary">
                                {{ $overtime->approved_at?->format('d F Y H:i') ?? '-' }}
                                @if($overtime->approver)
                                    oleh {{ $overtime->approver->name }}
                                @endif
                            </div>
                        </li>
                    @elseif($overtime->status === 'rejected')
                        <li class="step-item step-item-active">
                            <div class="h4 m-0">Ditolak</div>
                            <div class="text-secondary">
                                @if($overtime->approver)
                                    oleh {{ $overtime->approver->name }}
                                @endif
                            </div>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        @if($overtime->status === 'pending')
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
                        <form method="POST" action="{{ route('overtimes.approve', $overtime) }}">
                            @csrf
                            <input type="hidden" name="status" value="approved">
                            <div class="modal-header">
                                <h5 class="modal-title">Setujui Lembur</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>Setujui pengajuan lembur ini?</p>
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
                        <form method="POST" action="{{ route('overtimes.approve', $overtime) }}">
                            @csrf
                            <input type="hidden" name="status" value="rejected">
                            <div class="modal-header">
                                <h5 class="modal-title">Tolak Lembur</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>Tolak pengajuan lembur ini?</p>
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
                    <span class="avatar avatar-xl" style="background-image: url({{ $overtime->employee?->photo ? asset('storage/'.$overtime->employee->photo) : '' }})"></span>
                </div>
                <h4>{{ $overtime->employee?->full_name ?? '-' }}</h4>
                <p class="text-secondary mb-1">NIK: {{ $overtime->employee?->nik ?? '-' }}</p>
                <p class="text-secondary mb-1">{{ $overtime->employee?->department?->name ?? '-' }}</p>
                <p class="text-secondary mb-0">{{ $overtime->employee?->position?->name ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

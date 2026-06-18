@extends('layouts.app')
@section('title', 'Lembur Saya')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Lembur Saya</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('overtime.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-2"></i>Ajukan Lembur
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body border-bottom">
        <form method="GET" action="{{ route('overtime.my') }}" class="row g-3">
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
                <label class="form-label">Bulan</label>
                <select name="month" class="form-select">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ (request('month', now()->month) == $m) ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
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
                        <td>{{ Str::limit($overtime->description, 40) }}</td>
                        <td>
                            <a href="{{ route('overtime.show', $overtime) }}" class="btn btn-sm btn-outline-primary">
                                <i class="ti ti-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="empty">
                                <div class="empty-icon"><i class="ti ti-clock-off fs-1"></i></div>
                                <p class="empty-title">Belum ada pengajuan lembur</p>
                                <p class="empty-subtitle text-secondary">Anda belum mengajukan lembur.</p>
                                <a href="{{ route('overtime.create') }}" class="btn btn-primary">
                                    <i class="ti ti-plus me-2"></i>Ajukan Lembur
                                </a>
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
@endsection

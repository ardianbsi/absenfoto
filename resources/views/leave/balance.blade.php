@extends('layouts.app')
@section('title', 'Saldo Cuti')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Saldo Cuti & Izin</h2>
        </div>
    </div>
</div>

<div class="row row-cards mb-3">
    @forelse($balances as $balance)
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <h3 class="card-title mb-0">{{ $balance['type']->name }}</h3>
                            @if($balance['type']->is_paid)
                                <span class="badge bg-green-lt">Dibayar</span>
                            @endif
                            @if(!$balance['type']->is_deduct_quota)
                                <span class="badge bg-blue-lt">Tidak Potong Kuota</span>
                            @endif
                        </div>
                        <div class="text-end">
                            <div class="fs-1 fw-bold {{ $balance['remaining'] > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $balance['remaining'] }}
                            </div>
                            <div class="text-secondary small">sisa</div>
                        </div>
                    </div>
                    <div class="progress progress-lg mb-2">
                        @php
                            $pct = $balance['quota'] > 0 ? ($balance['used'] / $balance['quota'] * 100) : 0;
                            $barClass = $pct >= 100 ? 'bg-danger' : ($pct >= 75 ? 'bg-warning' : 'bg-success');
                        @endphp
                        <div class="progress-bar {{ $barClass }}" style="width: {{ min(100, $pct) }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between text-secondary small">
                        <span>Terpakai: <strong>{{ $balance['used'] }}</strong></span>
                        <span>Kuota: <strong>{{ $balance['quota'] }}</strong></span>
                    </div>
                    @if($balance['type']->description)
                        <p class="text-secondary small mt-2 mb-0">{{ $balance['type']->description }}</p>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="empty">
                        <div class="empty-icon"><i class="ti ti-calculator fs-1"></i></div>
                        <p class="empty-title">Tidak ada data saldo cuti</p>
                        <p class="empty-subtitle text-secondary">Belum ada tipe cuti yang dikonfigurasi.</p>
                    </div>
                </div>
            </div>
        </div>
    @endforelse
</div>

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">Riwayat Pemakaian Cuti Tahun {{ now()->year }}</h3>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Tipe</th>
                    <th>Tanggal</th>
                    <th>Hari</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($balances as $balance)
                    @foreach($balance['type']->leaveRequests()->where('employee_id', $employee->id)->whereYear('start_date', now()->year)->orderBy('created_at', 'desc')->get() as $request)
                        <tr>
                            <td>{{ $balance['type']->name }}</td>
                            <td>{{ $request->start_date->format('d/m/Y') }} - {{ $request->end_date->format('d/m/Y') }}</td>
                            <td>{{ $request->total_days }} hari</td>
                            <td>
                                @php
                                    $sc = ['pending' => 'bg-warning', 'approved' => 'bg-success', 'rejected' => 'bg-danger'];
                                    $sl = ['pending' => 'Pending', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];
                                @endphp
                                <span class="badge {{ $sc[$request->status] ?? 'bg-secondary' }}">{{ $sl[$request->status] ?? $request->status }}</span>
                            </td>
                            <td>{{ Str::limit($request->reason, 50) }}</td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <p class="text-secondary mb-0">Belum ada riwayat pemakaian cuti.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

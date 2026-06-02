@extends('layouts.app')
@section('title', 'Log Aktivitas')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Log Aktivitas</h2>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body border-bottom">
        <form method="GET" action="{{ route('activity-logs.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Modul</label>
                <select name="module" class="form-select">
                    <option value="">Semua Modul</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod }}" {{ request('module') === $mod ? 'selected' : '' }}>{{ ucfirst($mod) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Aksi</label>
                <select name="action" class="form-select">
                    <option value="">Semua Aksi</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ ucfirst($act) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tipe Log</label>
                <select name="log_type" class="form-select">
                    <option value="">Semua Tipe</option>
                    @foreach($logTypes as $type)
                        <option value="{{ $type }}" {{ request('log_type') === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
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
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="ti ti-filter"></i>
                </button>
            </div>
            @if(request()->anyFilled(['module', 'action', 'log_type', 'start_date', 'end_date']))
                <div class="col-12">
                    <a href="{{ route('activity-logs.index') }}" class="btn btn-sm btn-outline-secondary">
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
                    <th>Timestamp</th>
                    <th>User</th>
                    <th>Aksi</th>
                    <th>Modul</th>
                    <th>Deskripsi</th>
                    <th>IP Address</th>
                    <th class="w-1">Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="text-nowrap">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs me-2" style="background-image: url({{ $log->user?->avatar ? asset('storage/'.$log->user->avatar) : '' }})"></span>
                                {{ $log->user?->name ?? 'System' }}
                            </div>
                        </td>
                        <td>
                            @php
                                $actionClasses = ['create' => 'bg-green', 'update' => 'bg-blue', 'delete' => 'bg-red', 'check_in' => 'bg-green', 'check_out' => 'bg-yellow', 'approve' => 'bg-green', 'reject' => 'bg-red', 'submit' => 'bg-blue'];
                                $actionIcons = ['create' => 'ti-plus', 'update' => 'ti-edit', 'delete' => 'ti-trash', 'check_in' => 'ti-login', 'check_out' => 'ti-logout', 'approve' => 'ti-check', 'reject' => 'ti-x', 'submit' => 'ti-send'];
                            @endphp
                            <span class="badge {{ $actionClasses[$log->action] ?? 'bg-secondary' }}">
                                <i class="ti {{ $actionIcons[$log->action] ?? 'ti-info-circle' }} me-1"></i>
                                {{ ucfirst($log->action) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ ucfirst($log->module) }}</span>
                        </td>
                        <td class="text-truncate" style="max-width:250px;">{{ $log->description ?? '-' }}</td>
                        <td><code>{{ $log->ip_address ?? '-' }}</code></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#logDetail-{{ $log->id }}">
                                <i class="ti ti-chevron-down"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="collapse" id="logDetail-{{ $log->id }}">
                        <td colspan="7" class="p-0">
                            <div class="p-3 bg-light">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Data Lama</h6>
                                        @if($log->old_values)
                                            <pre class="bg-dark text-light p-2 rounded" style="font-size:11px;max-height:200px;overflow:auto;">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                        @else
                                            <p class="text-secondary">-</p>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Data Baru</h6>
                                        @if($log->new_values)
                                            <pre class="bg-dark text-light p-2 rounded" style="font-size:11px;max-height:200px;overflow:auto;">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                        @else
                                            <p class="text-secondary">-</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-secondary">
                                        URL: {{ $log->url ?? '-' }} |
                                        Method: {{ $log->method ?? '-' }} |
                                        User Agent: {{ Str::limit($log->user_agent, 50) }}
                                    </small>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="empty">
                                <div class="empty-icon"><i class="ti ti-activity fs-1"></i></div>
                                <p class="empty-title">Tidak ada log aktivitas</p>
                                <p class="empty-subtitle text-secondary">Belum ada aktivitas yang tercatat.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
        <div class="card-footer d-flex align-items-center justify-content-center">
            {{ $logs->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

@extends('layouts.app')
@section('title', 'Data Shift')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Master Data</div>
                <h2 class="page-title">Data Shift</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('shifts.schedule') }}" class="btn btn-secondary">
                        <span class="ti ti-calendar me-2"></span>Jadwal
                    </a>
                    <a href="{{ route('shifts.assign') }}" class="btn btn-secondary">
                        <span class="ti ti-users me-2"></span>Assign
                    </a>
                    <a href="{{ route('shifts.create') }}" class="btn btn-primary">
                        <span class="ti ti-plus me-2"></span>Tambah Shift
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="row row-deck row-cards mb-4">
            @if(isset($shifts) && $shifts->count() > 0)
                @foreach($shifts as $shift)
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <span class="avatar rounded-circle" style="background-color: {{ $shift->color }}; color: #fff;">
                                    <span class="ti ti-clock"></span>
                                </span>
                                <div class="ms-3">
                                    <div class="font-weight-medium">{{ $shift->name }}</div>
                                    <div class="text-secondary small">{{ $shift->code }}</div>
                                </div>
                            </div>
                            <div class="list-group list-group-flush">
                                <div class="list-group-item px-0">
                                    <div class="row align-items-center">
                                        <div class="col text-secondary">Tipe</div>
                                        <div class="col-auto">
                                            <span class="badge bg-{{ $shift->type == 'regular' ? 'blue' : ($shift->type == 'flexible' ? 'purple' : 'orange') }}">
                                                {{ ucfirst($shift->type) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-group-item px-0">
                                    <div class="row align-items-center">
                                        <div class="col text-secondary">Jam Kerja</div>
                                        <div class="col-auto">{{ $shift->start_time }} - {{ $shift->end_time }}</div>
                                    </div>
                                </div>
                                <div class="list-group-item px-0">
                                    <div class="row align-items-center">
                                        <div class="col text-secondary">Toleransi</div>
                                        <div class="col-auto">{{ $shift->tolerance ?? 15 }} menit</div>
                                    </div>
                                </div>
                                <div class="list-group-item px-0">
                                    <div class="row align-items-center">
                                        <div class="col text-secondary">Status</div>
                                        <div class="col-auto">
                                            <span class="badge bg-{{ $shift->status == 'active' ? 'green' : 'red' }}">
                                                {{ $shift->status == 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex">
                                    <a href="{{ route('shifts.edit', $shift) }}" class="btn btn-sm btn-outline-primary me-2">
                                        <span class="ti ti-edit"></span>
                                    </a>
                                    <form action="{{ route('shifts.destroy', $shift) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <span class="ti ti-trash"></span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th class="w-1">No</th>
                            <th>Kode</th>
                            <th>Nama Shift</th>
                            <th>Tipe</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Toleransi</th>
                            <th>Warna</th>
                            <th>Status</th>
                            <th class="w-1">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($shifts) && $shifts->count() > 0)
                            @foreach($shifts as $shift)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><span class="badge bg-secondary">{{ $shift->code }}</span></td>
                                <td class="font-weight-medium">{{ $shift->name }}</td>
                                <td>
                                    <span class="badge bg-{{ $shift->type == 'regular' ? 'blue' : ($shift->type == 'flexible' ? 'purple' : 'orange') }}">
                                        {{ ucfirst($shift->type) }}
                                    </span>
                                </td>
                                <td>{{ $shift->start_time }}</td>
                                <td>{{ $shift->end_time }}</td>
                                <td>{{ $shift->tolerance ?? 15 }} menit</td>
                                <td>
                                    <span class="badge" style="background-color: {{ $shift->color }}; color: #fff;">
                                        {{ $shift->color }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $shift->status == 'active' ? 'green' : 'red' }}">
                                        {{ $shift->status == 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="{{ route('shifts.edit', $shift) }}" class="btn btn-icon btn-outline-primary">
                                            <span class="ti ti-edit"></span>
                                        </a>
                                        <form action="{{ route('shifts.destroy', $shift) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-outline-danger">
                                                <span class="ti ti-trash"></span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                        <tr>
                            <td colspan="10" class="text-center text-secondary py-6">
                                <div class="mb-2"><span class="ti ti-clock fs-2"></span></div>
                                <h3>Belum ada data shift</h3>
                                <p class="mb-3">Klik tombol "Tambah Shift" untuk menambahkan data baru.</p>
                                <a href="{{ route('shifts.create') }}" class="btn btn-primary">
                                    <span class="ti ti-plus me-2"></span>Tambah Shift
                                </a>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

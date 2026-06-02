@extends('layouts.app')
@section('title', 'Data Karyawan')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Master Data</div>
                <h2 class="page-title">Data Karyawan</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('employees.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                        <span class="ti ti-plus me-2"></span>Tambah Karyawan
                    </a>
                    <a href="{{ route('employees.create') }}" class="btn btn-primary d-sm-none btn-icon">
                        <span class="ti ti-plus"></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="card mb-4">
            <div class="card-body border-bottom py-3">
                <form action="{{ route('employees.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-icon">
                                <span class="input-icon-addon"><span class="ti ti-search"></span></span>
                                <input type="text" class="form-control" name="search" placeholder="Cari nama, NIK, email..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="department">
                                <option value="">Semua Department</option>
                                @if(isset($departments))
                                    @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="status">
                                <option value="">Semua Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                <option value="contract" {{ request('status') == 'contract' ? 'selected' : '' }}>Kontrak</option>
                                <option value="probation" {{ request('status') == 'probation' ? 'selected' : '' }}>Probation</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-secondary w-100">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th class="w-1">No</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Department</th>
                            <th>Jabatan</th>
                            <th>Status</th>
                            <th>Shift</th>
                            <th>Foto</th>
                            <th class="w-1">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($employees) && $employees->count() > 0)
                            @foreach($employees as $employee)
                            <tr>
                                <td>{{ $loop->iteration + ($employees->currentPage() - 1) * $employees->perPage() }}</td>
                                <td><span class="badge bg-secondary">{{ $employee->nik }}</span></td>
                                <td>
                                    <div class="d-flex py-1 align-items-center">
                                        @if($employee->photo)
                                        <span class="avatar avatar-sm me-2 rounded" style="background-image: url({{ asset('storage/'.$employee->photo) }})"></span>
                                        @else
                                        <span class="avatar avatar-sm me-2 rounded-circle bg-primary text-white">{{ substr($employee->name, 0, 1) }}</span>
                                        @endif
                                        <div class="flex-fill">
                                            <div class="font-weight-medium">{{ $employee->name }}</div>
                                            <div class="text-secondary small">{{ $employee->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $employee->department->name ?? '-' }}</td>
                                <td>{{ $employee->position->name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $employee->status == 'active' ? 'green' : ($employee->status == 'inactive' ? 'red' : ($employee->status == 'probation' ? 'orange' : 'blue')) }}">
                                        {{ ucfirst($employee->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($employee->shift)
                                    <span class="badge" style="background-color: {{ $employee->shift->color }}; color: #fff;">
                                        {{ $employee->shift->name }}
                                    </span>
                                    @else
                                    <span class="text-secondary">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($employee->photo)
                                    <span class="avatar avatar-sm rounded" style="background-image: url({{ asset('storage/'.$employee->photo) }})"></span>
                                    @else
                                    <span class="avatar avatar-sm rounded-circle bg-secondary"><span class="ti ti-user"></span></span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="{{ route('employees.show', $employee) }}" class="btn btn-icon btn-outline-secondary" data-bs-toggle="tooltip" title="Lihat Detail">
                                            <span class="ti ti-eye"></span>
                                        </a>
                                        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-icon btn-outline-primary" data-bs-toggle="tooltip" title="Edit">
                                            <span class="ti ti-edit"></span>
                                        </a>
                                        <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-outline-danger" data-bs-toggle="tooltip" title="Hapus">
                                                <span class="ti ti-trash"></span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                        <tr>
                            <td colspan="9" class="text-center text-secondary py-6">
                                <div class="mb-2"><span class="ti ti-users fs-2"></span></div>
                                <h3>Belum ada data karyawan</h3>
                                <p class="mb-3">Klik tombol "Tambah Karyawan" untuk menambahkan data baru.</p>
                                <a href="{{ route('employees.create') }}" class="btn btn-primary">
                                    <span class="ti ti-plus me-2"></span>Tambah Karyawan
                                </a>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if(isset($employees) && $employees->hasPages())
            <div class="card-footer d-flex align-items-center">
                <p class="m-0 text-secondary">
                    Menampilkan {{ $employees->firstItem() ?? 0 }} sampai {{ $employees->lastItem() ?? 0 }} dari {{ $employees->total() }} data
                </p>
                <ul class="pagination m-0 ms-auto">
                    {{ $employees->links() }}
                </ul>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

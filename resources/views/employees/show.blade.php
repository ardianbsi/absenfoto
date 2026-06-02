@extends('layouts.app')
@section('title', 'Detail Karyawan')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Master Data</div>
                <h2 class="page-title">Detail Karyawan</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-primary">
                        <span class="ti ti-edit me-2"></span>Edit
                    </a>
                    <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <span class="ti ti-trash me-2"></span>Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        @if($employee->photo)
                        <span class="avatar avatar-xl rounded-circle" style="background-image: url({{ asset('storage/'.$employee->photo) }})"></span>
                        @else
                        <span class="avatar avatar-xl rounded-circle bg-primary text-white">{{ substr($employee->name, 0, 1) }}</span>
                        @endif
                    </div>
                    <div class="col">
                        <div class="page-title mb-1">{{ $employee->name }}</div>
                        <div class="text-secondary">
                            <span class="badge bg-secondary me-2">{{ $employee->nik }}</span>
                            <span class="me-3">{{ $employee->department->name ?? '-' }}</span>
                            <span class="me-3">{{ $employee->position->name ?? '-' }}</span>
                            <span class="badge bg-{{ $employee->status == 'active' ? 'green' : 'orange' }}">
                                {{ ucfirst($employee->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                    <li class="nav-item">
                        <a href="#tab-info" class="nav-link active" data-bs-toggle="tab">
                            <span class="ti ti-user me-2"></span>Informasi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#tab-attendance" class="nav-link" data-bs-toggle="tab">
                            <span class="ti ti-fingerprint me-2"></span>Riwayat Absensi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#tab-leave" class="nav-link" data-bs-toggle="tab">
                            <span class="ti ti-calendar-off me-2"></span>Riwayat Cuti
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#tab-overtime" class="nav-link" data-bs-toggle="tab">
                            <span class="ti ti-clock-plus me-2"></span>Riwayat Lembur
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane active show" id="tab-info">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Akun Pengguna</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="text-secondary small">Email</div>
                                            <div>{{ $employee->email }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="text-secondary small">Role</div>
                                            <div><span class="badge bg-blue">{{ $employee->role ?? 'Staff' }}</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Informasi Pribadi</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="text-secondary small">NIK</div>
                                                <div>{{ $employee->nik }}</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="text-secondary small">No. Telepon</div>
                                                <div>{{ $employee->phone ?? '-' }}</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="text-secondary small">Tempat, Tanggal Lahir</div>
                                                <div>{{ $employee->place_of_birth ?? '-' }}{{ $employee->date_of_birth ? ', ' . optional($employee->date_of_birth)->format('d/m/Y') : '' }}</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="text-secondary small">Jenis Kelamin</div>
                                                <div>{{ $employee->gender ? ucfirst($employee->gender) : '-' }}</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="text-secondary small">Agama</div>
                                                <div>{{ $employee->religion ? ucfirst($employee->religion) : '-' }}</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="text-secondary small">Status Pernikahan</div>
                                                <div>{{ $employee->marital_status ? ucfirst($employee->marital_status) : '-' }}</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="text-secondary small">Golongan Darah</div>
                                                <div>{{ $employee->blood_type ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Informasi Kepegawaian</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="text-secondary small">Department</div>
                                                <div>{{ $employee->department->name ?? '-' }}</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="text-secondary small">Jabatan</div>
                                                <div>{{ $employee->position->name ?? '-' }}</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="text-secondary small">Manager/Atasan</div>
                                                <div>{{ $employee->manager->name ?? '-' }}</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="text-secondary small">Tanggal Bergabung</div>
                                                <div>{{ optional($employee->join_date)->format('d/m/Y') ?? '-' }}</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="text-secondary small">Status Kerja</div>
                                                <div>
                                                    <span class="badge bg-{{ $employee->status == 'active' ? 'green' : 'orange' }}">
                                                        {{ ucfirst($employee->status) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="text-secondary small">Tanggal Akhir Kontrak</div>
                                                <div>{{ optional($employee->contract_end_date)->format('d/m/Y') ?? '-' }}</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="text-secondary small">Shift</div>
                                                <div>
                                                    @if($employee->shift)
                                                    <span class="badge" style="background-color: {{ $employee->shift->color }}; color: #fff;">
                                                        {{ $employee->shift->name }} ({{ $employee->shift->start_time }} - {{ $employee->shift->end_time }})
                                                    </span>
                                                    @else
                                                    -
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Ringkasan Kehadiran</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="text-green h3 mb-1">20</div>
                                                <div class="text-secondary small">Hadir</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="text-orange h3 mb-1">2</div>
                                                <div class="text-secondary small">Telat</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="text-blue h3 mb-1">3</div>
                                                <div class="text-secondary small">Cuti</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab-attendance">
                        <div class="table-responsive">
                            <table class="table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Check In</th>
                                        <th>Check Out</th>
                                        <th>Durasi</th>
                                        <th>Status</th>
                                        <th>Lokasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($attendances) && $attendances->count() > 0)
                                        @foreach($attendances as $attendance)
                                        <tr>
                                            <td>{{ optional($attendance->date)->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $attendance->check_in_status == 'on_time' ? 'green' : 'orange' }}">
                                                    {{ $attendance->check_in ?? '-' }}
                                                </span>
                                            </td>
                                            <td>{{ $attendance->check_out ?? '-' }}</td>
                                            <td>{{ $attendance->duration ?? '-' }} jam</td>
                                            <td>
                                                <span class="badge bg-{{ $attendance->status == 'present' ? 'green' : ($attendance->status == 'late' ? 'orange' : 'red') }}">
                                                    {{ ucfirst($attendance->status) }}
                                                </span>
                                            </td>
                                            <td class="text-secondary small">{{ $attendance->location ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    @else
                                    <tr>
                                        <td colspan="6" class="text-center text-secondary py-4">
                                            Belum ada data absensi
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab-leave">
                        <div class="table-responsive">
                            <table class="table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>Tipe Cuti</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Durasi</th>
                                        <th>Alasan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($leaves) && $leaves->count() > 0)
                                        @foreach($leaves as $leave)
                                        <tr>
                                            <td><span class="badge bg-blue">{{ ucfirst($leave->type) }}</span></td>
                                            <td>{{ optional($leave->start_date)->format('d/m/Y') }}</td>
                                            <td>{{ optional($leave->end_date)->format('d/m/Y') }}</td>
                                            <td>{{ $leave->days ?? 0 }} hari</td>
                                            <td>{{ $leave->reason ?? '-' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $leave->status == 'approved' ? 'green' : ($leave->status == 'rejected' ? 'red' : 'yellow') }}">
                                                    {{ ucfirst($leave->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                    <tr>
                                        <td colspan="6" class="text-center text-secondary py-4">
                                            Belum ada data cuti
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab-overtime">
                        <div class="table-responsive">
                            <table class="table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Waktu Mulai</th>
                                        <th>Waktu Selesai</th>
                                        <th>Durasi</th>
                                        <th>Alasan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($overtimes) && $overtimes->count() > 0)
                                        @foreach($overtimes as $overtime)
                                        <tr>
                                            <td>{{ optional($overtime->date)->format('d/m/Y') }}</td>
                                            <td>{{ $overtime->start_time ?? '-' }}</td>
                                            <td>{{ $overtime->end_time ?? '-' }}</td>
                                            <td>{{ $overtime->duration ?? 0 }} jam</td>
                                            <td>{{ $overtime->reason ?? '-' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $overtime->status == 'approved' ? 'green' : ($overtime->status == 'rejected' ? 'red' : 'yellow') }}">
                                                    {{ ucfirst($overtime->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                    <tr>
                                        <td colspan="6" class="text-center text-secondary py-4">
                                            Belum ada data lembur
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

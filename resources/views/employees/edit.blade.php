@extends('layouts.app')
@section('title', 'Edit Karyawan')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Master Data</div>
                <h2 class="page-title">Edit Karyawan</h2>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <form action="{{ route('employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row row-deck">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Akun Pengguna</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $employee->name) }}" placeholder="Masukkan nama lengkap">
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $employee->email) }}" placeholder="name@company.com">
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Biarkan kosong jika tidak diubah">
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Konfirmasi Password</label>
                                    <input type="password" class="form-control" name="password_confirmation" placeholder="Ulangi password">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Pribadi</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">NIK <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nik') is-invalid @enderror" name="nik" value="{{ old('nik', $employee->nik) }}" placeholder="Nomor Induk Karyawan">
                                    @error('nik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">No. Telepon</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $employee->phone) }}" placeholder="08xxxxxxxxxx">
                                    @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Golongan Darah</label>
                                    <select class="form-select" name="blood_type">
                                        <option value="">Pilih</option>
                                        <option value="A" {{ old('blood_type', $employee->blood_type) == 'A' ? 'selected' : '' }}>A</option>
                                        <option value="B" {{ old('blood_type', $employee->blood_type) == 'B' ? 'selected' : '' }}>B</option>
                                        <option value="AB" {{ old('blood_type', $employee->blood_type) == 'AB' ? 'selected' : '' }}>AB</option>
                                        <option value="O" {{ old('blood_type', $employee->blood_type) == 'O' ? 'selected' : '' }}>O</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tempat Lahir</label>
                                    <input type="text" class="form-control @error('place_of_birth') is-invalid @enderror" name="place_of_birth" value="{{ old('place_of_birth', $employee->place_of_birth) }}" placeholder="Kota kelahiran">
                                    @error('place_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" name="date_of_birth" value="{{ old('date_of_birth', optional($employee->date_of_birth)->format('Y-m-d')) }}">
                                    @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <div class="form-selectgroup">
                                        <label class="form-selectgroup-item">
                                            <input type="radio" name="gender" value="male" class="form-selectgroup-input" {{ old('gender', $employee->gender) == 'male' ? 'checked' : '' }}>
                                            <span class="form-selectgroup-label"><span class="ti ti-gender-male me-2"></span>Laki-laki</span>
                                        </label>
                                        <label class="form-selectgroup-item">
                                            <input type="radio" name="gender" value="female" class="form-selectgroup-input" {{ old('gender', $employee->gender) == 'female' ? 'checked' : '' }}>
                                            <span class="form-selectgroup-label"><span class="ti ti-gender-female me-2"></span>Perempuan</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Agama</label>
                                    <select class="form-select" name="religion">
                                        <option value="">Pilih</option>
                                        <option value="islam" {{ old('religion', $employee->religion) == 'islam' ? 'selected' : '' }}>Islam</option>
                                        <option value="kristen" {{ old('religion', $employee->religion) == 'kristen' ? 'selected' : '' }}>Kristen</option>
                                        <option value="katolik" {{ old('religion', $employee->religion) == 'katolik' ? 'selected' : '' }}>Katolik</option>
                                        <option value="hindu" {{ old('religion', $employee->religion) == 'hindu' ? 'selected' : '' }}>Hindu</option>
                                        <option value="buddha" {{ old('religion', $employee->religion) == 'buddha' ? 'selected' : '' }}>Buddha</option>
                                        <option value="konghucu" {{ old('religion', $employee->religion) == 'konghucu' ? 'selected' : '' }}>Konghucu</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Status Pernikahan</label>
                                    <select class="form-select" name="marital_status">
                                        <option value="">Pilih</option>
                                        <option value="single" {{ old('marital_status', $employee->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
                                        <option value="married" {{ old('marital_status', $employee->marital_status) == 'married' ? 'selected' : '' }}>Menikah</option>
                                        <option value="divorced" {{ old('marital_status', $employee->marital_status) == 'divorced' ? 'selected' : '' }}>Cerai</option>
                                        <option value="widowed" {{ old('marital_status', $employee->marital_status) == 'widowed' ? 'selected' : '' }}>Janda/Duda</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Kepegawaian</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Department <span class="text-danger">*</span></label>
                                    <select class="form-select @error('department_id') is-invalid @enderror" name="department_id">
                                        <option value="">Pilih Department</option>
                                        @if(isset($departments))
                                            @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                                    <select class="form-select @error('position_id') is-invalid @enderror" name="position_id">
                                        <option value="">Pilih Jabatan</option>
                                        @if(isset($positions))
                                            @foreach($positions as $pos)
                                            <option value="{{ $pos->id }}" {{ old('position_id', $employee->position_id) == $pos->id ? 'selected' : '' }}>{{ $pos->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('position_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Manager/Atasan</label>
                                    <select class="form-select" name="manager_id">
                                        <option value="">Tidak ada</option>
                                        @if(isset($employees))
                                            @foreach($employees as $emp)
                                                @if($emp->id != $employee->id)
                                                <option value="{{ $emp->id }}" {{ old('manager_id', $employee->manager_id) == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Bergabung <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('join_date') is-invalid @enderror" name="join_date" value="{{ old('join_date', optional($employee->join_date)->format('Y-m-d')) }}">
                                    @error('join_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Status Kerja <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" name="status">
                                        <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                                        <option value="probation" {{ old('status', $employee->status) == 'probation' ? 'selected' : '' }}>Probation</option>
                                        <option value="contract" {{ old('status', $employee->status) == 'contract' ? 'selected' : '' }}>Kontrak</option>
                                        <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                    </select>
                                    @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Tanggal Akhir Kontrak</label>
                                    <input type="date" class="form-control @error('contract_end_date') is-invalid @enderror" name="contract_end_date" value="{{ old('contract_end_date', optional($employee->contract_end_date)->format('Y-m-d')) }}">
                                    @error('contract_end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Shift</label>
                                    <select class="form-select" name="shift_id">
                                        <option value="">Pilih Shift</option>
                                        @if(isset($shifts))
                                            @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}" {{ old('shift_id', $employee->shift_id) == $shift->id ? 'selected' : '' }}>{{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Mode Absen Default</label>
                                    <select class="form-select" name="default_attendance_type">
                                        <option value="wfo" {{ old('default_attendance_type', $employee->default_attendance_type) == 'wfo' ? 'selected' : '' }}>WFO (Work From Office)</option>
                                        <option value="waf" {{ old('default_attendance_type', $employee->default_attendance_type) == 'waf' ? 'selected' : '' }}>WAF (Work From Anywhere)</option>
                                        <option value="wfh" {{ old('default_attendance_type', $employee->default_attendance_type) == 'wfh' ? 'selected' : '' }}>WFH (Work From Home)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Foto Profil</h3>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                @if($employee->photo)
                                <span class="avatar avatar-xl rounded-circle" id="photo-preview" style="background-image: url({{ asset('storage/'.$employee->photo) }})"></span>
                                @else
                                <span class="avatar avatar-xl rounded-circle bg-primary text-white" id="photo-preview">
                                    <span class="ti ti-user"></span>
                                </span>
                                @endif
                            </div>
                            <input type="file" class="form-control @error('photo') is-invalid @enderror" name="photo" id="photo-input" accept="image/*">
                            @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="text-secondary small mt-2">Format: JPG, PNG. Max: 2MB</div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Aksi</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <span class="ti ti-device-floppy me-2"></span>Simpan Perubahan
                                </button>
                                <a href="{{ route('employees.show', $employee) }}" class="btn btn-secondary">
                                    <span class="ti ti-arrow-left me-2"></span>Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('photo-input').addEventListener('change', function(e) {
    const preview = document.getElementById('photo-preview');
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.style.backgroundImage = 'url(' + e.target.result + ')';
            preview.innerHTML = '';
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush

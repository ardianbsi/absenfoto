@extends('layouts.app')
@section('title', 'Pengaturan')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Pengaturan</h2>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Profil</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('settings.profile') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-auto">
                            <div class="avatar avatar-xl" style="background-image: url({{ $user->avatar ? asset('storage/'.$user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=primary&color=fff' }})"></div>
                        </div>
                        <div class="col d-flex align-items-center">
                            <div>
                                <input type="file" name="avatar" class="form-control @error('avatar') is-invalid @enderror" accept="image/jpeg,image/png">
                                @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="form-hint">Format JPG/PNG, maks 2MB</small>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Nama</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-device-floppy me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Ubah Password</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('settings.profile') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Password Saat Ini</label>
                        <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror">
                        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror">
                        @error('new_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="new_password_confirmation" class="form-control">
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-lock me-2"></i>Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tema</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('settings.theme') }}" id="themeForm">
                    @csrf
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <i class="ti ti-sun me-2 fs-2 {{ $user->theme_preference === 'light' ? 'text-warning' : 'text-secondary' }}"></i>
                            <span class="fw-bold">Mode Terang</span>
                        </div>
                        <label class="form-check form-switch form-switch-lg">
                            <input class="form-check-input" type="radio" name="theme" value="light" {{ $user->theme_preference === 'light' ? 'checked' : '' }} onchange="this.form.submit()">
                        </label>
                    </div>
                    <hr>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <i class="ti ti-moon me-2 fs-2 {{ $user->theme_preference === 'dark' ? 'text-primary' : 'text-secondary' }}"></i>
                            <span class="fw-bold">Mode Gelap</span>
                        </div>
                        <label class="form-check form-switch form-switch-lg">
                            <input class="form-check-input" type="radio" name="theme" value="dark" {{ $user->theme_preference === 'dark' ? 'checked' : '' }} onchange="this.form.submit()">
                        </label>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Notifikasi</h3>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <i class="ti ti-bell me-2 fs-2 text-secondary"></i>
                        <span>Email Notifikasi</span>
                    </div>
                    <label class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" checked disabled>
                    </label>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <i class="ti ti-bell-ringing me-2 fs-2 text-secondary"></i>
                        <span>Notifikasi Aplikasi</span>
                    </div>
                    <label class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" checked disabled>
                    </label>
                </div>
                <p class="text-secondary small mt-3 mb-0">Pengaturan notifikasi akan tersedia di versi mendatang.</p>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Akun</h3>
            </div>
            <div class="card-body">
                <div class="datagrid">
                    <div class="datagrid-item">
                        <div class="datagrid-title">Role</div>
                        <div class="datagrid-content">{{ $user->role_name ?? '-' }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Terdaftar</div>
                        <div class="datagrid-content">{{ $user->created_at->format('d M Y') }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Terakhir Login</div>
                        <div class="datagrid-content">{{ $user->last_login_at?->format('d/m/Y H:i') ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

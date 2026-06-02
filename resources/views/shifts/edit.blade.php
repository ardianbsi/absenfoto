@extends('layouts.app')
@section('title', 'Edit Shift')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Master Data</div>
                <h2 class="page-title">Edit Shift</h2>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <form action="{{ route('shifts.update', $shift) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Shift</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kode Shift <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ old('code', $shift->code) }}">
                                    @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Shift <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $shift->name) }}">
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tipe Shift <span class="text-danger">*</span></label>
                                    <select class="form-select @error('type') is-invalid @enderror" name="type">
                                        <option value="regular" {{ old('type', $shift->type) == 'regular' ? 'selected' : '' }}>Regular</option>
                                        <option value="flexible" {{ old('type', $shift->type) == 'flexible' ? 'selected' : '' }}>Flexible</option>
                                        <option value="night" {{ old('type', $shift->type) == 'night' ? 'selected' : '' }}>Night Shift</option>
                                    </select>
                                    @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Warna</label>
                                    <input type="color" class="form-control" name="color" value="{{ old('color', $shift->color) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control @error('start_time') is-invalid @enderror" name="start_time" value="{{ old('start_time', $shift->start_time) }}">
                                    @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control @error('end_time') is-invalid @enderror" name="end_time" value="{{ old('end_time', $shift->end_time) }}">
                                    @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Toleransi Keterlambatan (menit)</label>
                                    <input type="number" class="form-control" name="tolerance" value="{{ old('tolerance', $shift->tolerance ?? 15) }}" min="0">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="status">
                                        <option value="active" {{ old('status', $shift->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                                        <option value="inactive" {{ old('status', $shift->status) == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea class="form-control" name="description" rows="3">{{ old('description', $shift->description) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('shifts.index') }}" class="btn btn-secondary">
                                    <span class="ti ti-arrow-left me-2"></span>Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <span class="ti ti-device-floppy me-2"></span>Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

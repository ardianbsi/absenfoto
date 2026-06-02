@extends('layouts.app')
@section('title', $announcement->title ?? 'Detail Pengumuman')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="{{ route('announcements.index') }}" class="text-secondary">
                        <span class="ti ti-arrow-left me-1"></span>Kembali
                    </a>
                </div>
                <h2 class="page-title">{{ $announcement->title }}</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modal-edit">
                        <span class="ti ti-edit me-2"></span>Edit
                    </button>
                    <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" class="d-inline delete-form">
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
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <span class="avatar rounded-circle bg-{{ $announcement->priority == 'high' ? 'red' : ($announcement->priority == 'medium' ? 'orange' : 'blue') }} text-white me-3">
                                <span class="ti ti-bell"></span>
                            </span>
                            <div>
                                <div class="card-title m-0">{{ $announcement->title }}</div>
                                <div class="text-secondary small">
                                    Dipublikasikan: {{ optional($announcement->created_at)->format('d F Y H:i') }}
                                    @if($announcement->created_at != $announcement->updated_at)
                                    | Diupdate: {{ optional($announcement->updated_at)->format('d F Y H:i') }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-actions">
                            <span class="badge bg-{{ $announcement->priority == 'high' ? 'red' : ($announcement->priority == 'medium' ? 'orange' : 'blue') }}">
                                {{ ucfirst($announcement->priority) }}
                            </span>
                            @if($announcement->is_important)
                            <span class="badge bg-yellow">
                                <span class="ti ti-star me-1"></span>Penting
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="markdown">
                            {!! nl2br(e($announcement->content)) !!}
                        </div>
                    </div>
                    @if($announcement->expires_at)
                    <div class="card-footer">
                        <div class="d-flex align-items-center">
                            <span class="ti ti-clock me-2 text-secondary"></span>
                            <span class="text-secondary">Pengumuman ini berlaku sampai {{ optional($announcement->expires_at)->format('d F Y') }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-edit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('announcements.update', $announcement) }}" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Pengumuman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Judul Pengumuman <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="title" value="{{ old('title', $announcement->title) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Isi Pengumuman <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="content" rows="6">{{ old('content', $announcement->content) }}</textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Prioritas</label>
                        <select class="form-select" name="priority">
                            <option value="low" {{ old('priority', $announcement->priority) == 'low' ? 'selected' : '' }}>Rendah</option>
                            <option value="medium" {{ old('priority', $announcement->priority) == 'medium' ? 'selected' : '' }}>Sedang</option>
                            <option value="high" {{ old('priority', $announcement->priority) == 'high' ? 'selected' : '' }}>Tinggi</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tampilkan Sampai</label>
                        <input type="date" class="form-control" name="expires_at" value="{{ old('expires_at', optional($announcement->expires_at)->format('Y-m-d')) }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_important" value="1" {{ old('is_important', $announcement->is_important) ? 'checked' : '' }}>
                        <span class="form-check-label">Tandai sebagai penting</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <span class="ti ti-device-floppy me-2"></span>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

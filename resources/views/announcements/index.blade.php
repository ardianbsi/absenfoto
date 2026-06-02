@extends('layouts.app')
@section('title', 'Pengumuman')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Komunikasi</div>
                <h2 class="page-title">Pengumuman</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-create">
                    <span class="ti ti-plus me-2"></span>Buat Pengumuman
                </button>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        @if(isset($announcements) && $announcements->count() > 0)
        <div class="row row-deck">
            @foreach($announcements as $announcement)
            <div class="col-lg-4 col-md-6">
                <div class="card card-hover h-100">
                    <div class="card-header">
                        <div class="card-title">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm rounded-circle bg-{{ $announcement->priority == 'high' ? 'red' : ($announcement->priority == 'medium' ? 'orange' : 'blue') }} text-white me-2">
                                    <span class="ti ti-bell"></span>
                                </span>
                                <div>
                                    <div class="fw-bold">{{ $announcement->title }}</div>
                                    <div class="text-secondary small">{{ optional($announcement->created_at)->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="card-actions">
                            <span class="badge bg-{{ $announcement->priority == 'high' ? 'red' : ($announcement->priority == 'medium' ? 'orange' : 'blue') }}">
                                {{ ucfirst($announcement->priority) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-secondary">
                            {{ Str::limit(strip_tags($announcement->content), 150) }}
                        </p>
                        @if($announcement->is_important)
                        <div>
                            <span class="badge bg-yellow">
                                <span class="ti ti-star me-1"></span>Penting
                            </span>
                        </div>
                        @endif
                    </div>
                    <div class="card-footer d-flex">
                        <a href="{{ route('announcements.show', $announcement) }}" class="btn btn-primary">
                            <span class="ti ti-eye me-2"></span>Baca Selengkapnya
                        </a>
                        <div class="ms-auto">
                            <button type="button" class="btn btn-icon btn-outline-primary edit-btn" data-id="{{ $announcement->id }}" data-title="{{ $announcement->title }}" data-content="{{ $announcement->content }}" data-priority="{{ $announcement->priority }}" data-is-important="{{ $announcement->is_important ? '1' : '0' }}" data-bs-toggle="modal" data-bs-target="#modal-edit">
                                <span class="ti ti-edit"></span>
                            </button>
                            <button type="button" class="btn btn-icon btn-outline-danger delete-btn" data-id="{{ $announcement->id }}" data-title="{{ $announcement->title }}" data-bs-toggle="modal" data-bs-target="#modal-delete">
                                <span class="ti ti-trash"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @if($announcements->hasPages())
        <div class="d-flex align-items-center mt-4">
            <p class="m-0 text-secondary">
                Menampilkan {{ $announcements->firstItem() ?? 0 }} sampai {{ $announcements->lastItem() ?? 0 }} dari {{ $announcements->total() }} pengumuman
            </p>
            <ul class="pagination m-0 ms-auto">
                {{ $announcements->links() }}
            </ul>
        </div>
        @endif
        @else
        <div class="card">
            <div class="card-body text-center py-6">
                <div class="mb-2"><span class="ti ti-bell fs-2 text-secondary"></span></div>
                <h3>Belum ada pengumuman</h3>
                <p class="mb-3 text-secondary">Buat pengumuman pertama untuk semua karyawan.</p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-create">
                    <span class="ti ti-plus me-2"></span>Buat Pengumuman
                </button>
            </div>
        </div>
        @endif
    </div>
</div>
<div class="modal fade" id="modal-create" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('announcements.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Buat Pengumuman Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Judul Pengumuman <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" placeholder="Masukkan judul pengumuman">
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Isi Pengumuman <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('content') is-invalid @enderror" name="content" rows="6" placeholder="Tulis isi pengumuman...">{{ old('content') }}</textarea>
                    @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Prioritas</label>
                        <select class="form-select" name="priority">
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Rendah</option>
                            <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Sedang</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Tinggi</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tampilkan Sampai</label>
                        <input type="date" class="form-control" name="expires_at" value="{{ old('expires_at') }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_important" value="1" {{ old('is_important') ? 'checked' : '' }}>
                        <span class="form-check-label">Tandai sebagai penting</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <span class="ti ti-send me-2"></span>Publish
                </button>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="modal-edit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="" method="POST" class="modal-content" id="form-edit">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Pengumuman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Judul Pengumuman <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="title" id="edit-title">
                </div>
                <div class="mb-3">
                    <label class="form-label">Isi Pengumuman <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="content" rows="6" id="edit-content"></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Prioritas</label>
                        <select class="form-select" name="priority" id="edit-priority">
                            <option value="low">Rendah</option>
                            <option value="medium">Sedang</option>
                            <option value="high">Tinggi</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tampilkan Sampai</label>
                        <input type="date" class="form-control" name="expires_at" id="edit-expires-at">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_important" value="1" id="edit-is-important">
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
<div class="modal fade" id="modal-delete" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="" method="POST" class="modal-content" id="form-delete">
            @csrf
            @method('DELETE')
            <div class="modal-body text-center py-4">
                <span class="ti ti-alert-circle text-red mb-3" style="font-size: 3rem;"></span>
                <h3>Hapus Pengumuman</h3>
                <p class="text-secondary">Anda yakin ingin menghapus pengumuman <strong id="delete-title"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Hapus</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        document.getElementById('form-edit').action = '{{ url('announcements') }}/' + id;
        document.getElementById('edit-title').value = this.dataset.title;
        document.getElementById('edit-content').value = this.dataset.content;
        document.getElementById('edit-priority').value = this.dataset.priority;
        document.getElementById('edit-is-important').checked = this.dataset.isImportant == '1';
    });
});
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        document.getElementById('form-delete').action = '{{ url('announcements') }}/' + id;
        document.getElementById('delete-title').textContent = this.dataset.title;
    });
});
</script>
@endpush

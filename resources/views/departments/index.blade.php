@extends('layouts.app')
@section('title', 'Data Department')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Master Data</div>
                <h2 class="page-title">Data Department</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-create">
                    <span class="ti ti-plus me-2"></span>Tambah Department
                </button>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th class="w-1">No</th>
                            <th>Kode</th>
                            <th>Nama Department</th>
                            <th>Deskripsi</th>
                            <th>Total Jabatan</th>
                            <th>Total Karyawan</th>
                            <th>Status</th>
                            <th class="w-1">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($departments) && $departments->count() > 0)
                            @foreach($departments as $dept)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><span class="badge bg-blue">{{ $dept->code }}</span></td>
                                <td class="font-weight-medium">{{ $dept->name }}</td>
                                <td class="text-secondary">{{ $dept->description ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $dept->positions_count ?? 0 }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-green">{{ $dept->employees_count ?? 0 }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $dept->status == 'active' ? 'green' : 'red' }}">
                                        {{ $dept->status == 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <button type="button" class="btn btn-icon btn-outline-primary edit-btn" data-id="{{ $dept->id }}" data-code="{{ $dept->code }}" data-name="{{ $dept->name }}" data-description="{{ $dept->description }}" data-status="{{ $dept->status }}" data-bs-toggle="modal" data-bs-target="#modal-edit">
                                            <span class="ti ti-edit"></span>
                                        </button>
                                        <button type="button" class="btn btn-icon btn-outline-danger delete-btn" data-id="{{ $dept->id }}" data-name="{{ $dept->name }}" data-bs-toggle="modal" data-bs-target="#modal-delete">
                                            <span class="ti ti-trash"></span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                        <tr>
                            <td colspan="8" class="text-center text-secondary py-6">
                                <div class="mb-2"><span class="ti ti-building fs-2"></span></div>
                                <h3>Belum ada data department</h3>
                                <p class="mb-3">Klik tombol "Tambah Department" untuk menambahkan data baru.</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-create">
                                    <span class="ti ti-plus me-2"></span>Tambah Department
                                </button>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if(isset($departments) && $departments->hasPages())
            <div class="card-footer d-flex align-items-center">
                <p class="m-0 text-secondary">
                    Menampilkan {{ $departments->firstItem() ?? 0 }} sampai {{ $departments->lastItem() ?? 0 }} dari {{ $departments->total() }} data
                </p>
                <ul class="pagination m-0 ms-auto">
                    {{ $departments->links() }}
                </ul>
            </div>
            @endif
        </div>
    </div>
</div>
<div class="modal fade" id="modal-create" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('departments.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Department Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Kode Department <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ old('code') }}" placeholder="Contoh: IT, HRD, FIN">
                    @error('code')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Department <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Nama department">
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" name="description" rows="3" placeholder="Deskripsi department">{{ old('description') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="modal-edit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="" method="POST" class="modal-content" id="form-edit">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Kode Department <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="code" id="edit-code">
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Department <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="edit-name">
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" name="description" rows="3" id="edit-description"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status" id="edit-status">
                        <option value="active">Aktif</option>
                        <option value="inactive">Tidak Aktif</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
                <h3>Hapus Department</h3>
                <p class="text-secondary">Anda yakin ingin menghapus department <strong id="delete-name"></strong>?</p>
                <p class="text-secondary small">Data yang dihapus tidak dapat dikembalikan.</p>
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
        document.getElementById('form-edit').action = '{{ url('departments') }}/' + id;
        document.getElementById('edit-code').value = this.dataset.code;
        document.getElementById('edit-name').value = this.dataset.name;
        document.getElementById('edit-description').value = this.dataset.description || '';
        document.getElementById('edit-status').value = this.dataset.status;
    });
});
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        document.getElementById('form-delete').action = '{{ url('departments') }}/' + id;
        document.getElementById('delete-name').textContent = this.dataset.name;
    });
});
</script>
@endpush

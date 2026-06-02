@extends('layouts.app')
@section('title', 'Data Jabatan')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Master Data</div>
                <h2 class="page-title">Data Jabatan</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-create">
                    <span class="ti ti-plus me-2"></span>Tambah Jabatan
                </button>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="card mb-4">
            <div class="card-body border-bottom py-3">
                <form action="{{ route('positions.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <select class="form-select" name="department">
                                <option value="">Semua Department</option>
                                @if(isset($departments))
                                    @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                    @endforeach
                                @endif
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
                            <th>Nama Jabatan</th>
                            <th>Department</th>
                            <th>Deskripsi</th>
                            <th>Total Karyawan</th>
                            <th>Status</th>
                            <th class="w-1">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($positions) && $positions->count() > 0)
                            @foreach($positions as $pos)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="font-weight-medium">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm rounded-circle bg-purple text-white me-2">
                                            <span class="ti ti-briefcase"></span>
                                        </span>
                                        {{ $pos->name }}
                                    </div>
                                </td>
                                <td><span class="badge bg-blue">{{ $pos->department->name ?? '-' }}</span></td>
                                <td class="text-secondary">{{ $pos->description ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-green">{{ $pos->employees_count ?? 0 }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $pos->status == 'active' ? 'green' : 'red' }}">
                                        {{ $pos->status == 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <button type="button" class="btn btn-icon btn-outline-primary edit-btn" data-id="{{ $pos->id }}" data-name="{{ $pos->name }}" data-department-id="{{ $pos->department_id }}" data-description="{{ $pos->description }}" data-status="{{ $pos->status }}" data-bs-toggle="modal" data-bs-target="#modal-edit">
                                            <span class="ti ti-edit"></span>
                                        </button>
                                        <button type="button" class="btn btn-icon btn-outline-danger delete-btn" data-id="{{ $pos->id }}" data-name="{{ $pos->name }}" data-bs-toggle="modal" data-bs-target="#modal-delete">
                                            <span class="ti ti-trash"></span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-6">
                                <div class="mb-2"><span class="ti ti-briefcase fs-2"></span></div>
                                <h3>Belum ada data jabatan</h3>
                                <p class="mb-3">Klik tombol "Tambah Jabatan" untuk menambahkan data baru.</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-create">
                                    <span class="ti ti-plus me-2"></span>Tambah Jabatan
                                </button>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if(isset($positions) && $positions->hasPages())
            <div class="card-footer d-flex align-items-center">
                <p class="m-0 text-secondary">
                    Menampilkan {{ $positions->firstItem() ?? 0 }} sampai {{ $positions->lastItem() ?? 0 }} dari {{ $positions->total() }} data
                </p>
                <ul class="pagination m-0 ms-auto">
                    {{ $positions->links() }}
                </ul>
            </div>
            @endif
        </div>
    </div>
</div>
<div class="modal fade" id="modal-create" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('positions.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Jabatan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Department <span class="text-danger">*</span></label>
                    <select class="form-select @error('department_id') is-invalid @enderror" name="department_id">
                        <option value="">Pilih Department</option>
                        @if(isset($departments))
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('department_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Contoh: Staff IT, Manager HRD">
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" name="description" rows="3" placeholder="Deskripsi jabatan">{{ old('description') }}</textarea>
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
                <h5 class="modal-title">Edit Jabatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Department <span class="text-danger">*</span></label>
                    <select class="form-select" name="department_id" id="edit-department-id">
                        @if(isset($departments))
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
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
                <h3>Hapus Jabatan</h3>
                <p class="text-secondary">Anda yakin ingin menghapus jabatan <strong id="delete-name"></strong>?</p>
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
        document.getElementById('form-edit').action = '{{ url('positions') }}/' + id;
        document.getElementById('edit-name').value = this.dataset.name;
        document.getElementById('edit-department-id').value = this.dataset.departmentId;
        document.getElementById('edit-description').value = this.dataset.description || '';
        document.getElementById('edit-status').value = this.dataset.status;
    });
});
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        document.getElementById('form-delete').action = '{{ url('positions') }}/' + id;
        document.getElementById('delete-name').textContent = this.dataset.name;
    });
});
</script>
@endpush

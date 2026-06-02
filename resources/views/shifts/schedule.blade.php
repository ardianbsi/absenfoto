@extends('layouts.app')
@section('title', 'Jadwal Shift')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Master Data</div>
                <h2 class="page-title">Jadwal Shift</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                            <span class="ti ti-calendar me-2"></span>{{ $currentMonthName ?? 'Bulan Ini' }} {{ $currentYear ?? date('Y') }}
                        </button>
                        <div class="dropdown-menu">
                            @for($i = 0; $i < 12; $i++)
                            <a class="dropdown-item" href="?month={{ $i + 1 }}&year={{ date('Y') }}">
                                {{ \Carbon\Carbon::create()->month($i + 1)->monthName }}
                            </a>
                            @endfor
                        </div>
                    </div>
                    <a href="{{ route('shifts.index') }}" class="btn btn-secondary">
                        <span class="ti ti-arrow-left me-2"></span>Data Shift
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-center gap-4">
                    <div class="text-secondary">
                        <span class="ti ti-arrow-left me-1"></span>
                        <a href="?month={{ $prevMonth }}&year={{ $prevYear }}">Sebelumnya</a>
                    </div>
                    <div class="h3 mb-0">{{ $currentMonthName ?? 'Bulan Ini' }} {{ $currentYear ?? date('Y') }}</div>
                    <div class="text-secondary">
                        <a href="?month={{ $nextMonth }}&year={{ $nextYear }}">Selanjutnya</a>
                        <span class="ti ti-arrow-right ms-1"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Legenda</h3>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @if(isset($shifts))
                        @foreach($shifts as $shift)
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <span class="badge me-2" style="background-color: {{ $shift->color }}; color: #fff;">
                                    {{ $shift->code }}
                                </span>
                                <span>{{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})</span>
                            </div>
                        </div>
                        @endforeach
                    @endif
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-gray me-2">LIBUR</span>
                            <span>Hari Libur</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-bordered table-vcenter">
                    <thead>
                        <tr>
                            <th class="w-1">Hari</th>
                            @for($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                $date = \Carbon\Carbon::create($currentYear ?? date('Y'), $currentMonth ?? date('n'), $day);
                                $isWeekend = $date->isWeekend();
                            @endphp
                            <th class="text-center {{ $isWeekend ? 'bg-red-lt' : '' }}">
                                <div>{{ $date->shortDayName }}</div>
                                <div class="fw-bold">{{ $day }}</div>
                            </th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($employees) && $employees->count() > 0)
                            @foreach($employees as $employee)
                            <tr>
                                <td>
                                    <div class="d-flex py-1 align-items-center">
                                        @if($employee->photo)
                                        <span class="avatar avatar-sm me-2 rounded" style="background-image: url({{ asset('storage/'.$employee->photo) }})"></span>
                                        @else
                                        <span class="avatar avatar-sm me-2 rounded-circle bg-primary text-white">{{ substr($employee->name, 0, 1) }}</span>
                                        @endif
                                        <div class="flex-fill">
                                            <div class="font-weight-medium">{{ $employee->name }}</div>
                                            <div class="text-secondary small">{{ $employee->position->name ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                @for($day = 1; $day <= $daysInMonth; $day++)
                                @php
                                    $date = \Carbon\Carbon::create($currentYear ?? date('Y'), $currentMonth ?? date('n'), $day);
                                    $isWeekend = $date->isWeekend();
                                    $employeeShift = $employee->shift;
                                @endphp
                                <td class="text-center {{ $isWeekend ? 'bg-red-lt' : '' }}">
                                    @if($employeeShift)
                                    <span class="badge cursor-pointer" style="background-color: {{ $employeeShift->color }}; color: #fff;" data-bs-toggle="modal" data-bs-target="#modal-override" data-employee-id="{{ $employee->id }}" data-date="{{ $date->format('Y-m-d') }}" data-current-shift="{{ $employeeShift->id }}">
                                        {{ $employeeShift->code }}
                                    </span>
                                    @else
                                    <span class="badge bg-gray">LIBUR</span>
                                    @endif
                                </td>
                                @endfor
                            </tr>
                            @endforeach
                        @else
                        <tr>
                            <td colspan="{{ $daysInMonth + 1 }}" class="text-center text-secondary py-6">
                                <div class="mb-2"><span class="ti ti-users fs-2"></span></div>
                                <h3>Belum ada data karyawan</h3>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-override" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('shifts.override') }}" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="employee_id" id="override-employee-id">
            <input type="hidden" name="date" id="override-date">
            <div class="modal-header">
                <h5 class="modal-title">Override Shift</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="text" class="form-control" id="override-date-display" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Shift</label>
                    <select class="form-select" name="shift_id">
                        <option value="">Libur / Tidak Masuk</option>
                        @if(isset($shifts))
                            @foreach($shifts as $shift)
                            <option value="{{ $shift->id }}">{{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea class="form-control" name="note" rows="2" placeholder="Alasan override shift"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('modal-override').addEventListener('show.bs.modal', function(e) {
    const button = e.relatedTarget;
    const employeeId = button.dataset.employeeId;
    const date = button.dataset.date;
    document.getElementById('override-employee-id').value = employeeId;
    document.getElementById('override-date').value = date;
    document.getElementById('override-date-display').value = date;
});
</script>
@endpush

@extends('layouts.app')
@section('title', 'Check In')

@section('content')
@if($todayAttendance && $todayAttendance->check_in)
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <span class="avatar avatar-xl bg-success text-white">
                            <i class="ti ti-user-check fs-1"></i>
                        </span>
                    </div>
                    <h3>Anda Sudah Check In</h3>
                    <p class="text-secondary mb-1">Waktu Check In:</p>
                    <p class="display-6 fw-bold text-success">{{ $todayAttendance->check_in->format('H:i:s') }}</p>
                    <p class="text-secondary mb-0">{{ $todayAttendance->date->format('l, d F Y') }}</p>
                    @if($todayAttendance->status === 'telat')
                        <span class="badge bg-orange mt-3">Telat {{ $todayAttendance->late_minutes }} menit</span>
                    @else
                        <span class="badge bg-success mt-3">Tepat Waktu</span>
                    @endif
                    <div class="mt-4">
                        <a href="{{ route('attendances.my') }}" class="btn btn-primary">
                            <i class="ti ti-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="row justify-content-center" style="min-height: 70vh;">
        <div class="col-md-8 col-lg-6 d-flex flex-column justify-content-center">
            <div class="card border-0 shadow-none">
                <div class="card-body text-center py-5">
                    <div id="live-clock" class="mb-4">
                        <p class="text-secondary mb-0" id="current-date"></p>
                        <p class="display-3 fw-bold mb-0" id="current-time">00:00:00</p>
                    </div>

                    <div class="card card-sm border mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="mb-0 text-secondary">Shift Hari Ini</p>
                                    <p class="fw-bold mb-0">{{ $employee->shift?->name ?? 'Reguler' }}</p>
                                </div>
                                <div class="text-end">
                                    <p class="mb-0 text-secondary">Jam Masuk</p>
                                    <p class="fw-bold mb-0">{{ $employee->shift?->start_time ? \Carbon\Carbon::parse($employee->shift->start_time)->format('H:i') : '08:00' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('attendances.store-check-in') }}" enctype="multipart/form-data" id="checkinForm">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Foto Check In</label>
                            <div id="camera-preview" class="mb-2" style="display:none;">
                                <video id="video" class="w-100 rounded border" style="max-height:300px;" autoplay playsinline></video>
                                <canvas id="canvas" style="display:none;"></canvas>
                                <div class="mt-2">
                                    <button type="button" id="capture-btn" class="btn btn-outline-primary btn-sm">
                                        <i class="ti ti-camera me-1"></i>Ambil Foto
                                    </button>
                                    <button type="button" id="retake-btn" class="btn btn-outline-warning btn-sm" style="display:none;">
                                        <i class="ti ti-camera-off me-1"></i>Ulangi
                                    </button>
                                </div>
                            </div>
                            <div id="camera-start" class="text-center p-4 border rounded bg-light">
                                <button type="button" id="start-camera" class="btn btn-outline-dark">
                                    <i class="ti ti-camera-plus me-2"></i>Buka Kamera
                                </button>
                                <p class="text-secondary mt-2 mb-0 small">Atau upload foto</p>
                                <input type="file" name="photo" class="form-control mt-2" accept="image/*" id="photo-input">
                            </div>
                            <input type="hidden" name="photo_data" id="photo-data">
                            @error('photo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan (opsional)</label>
                            <textarea name="note" class="form-control @error('note') is-invalid @enderror" rows="2" placeholder="Tambahkan catatan...">{{ old('note') }}</textarea>
                            @error('note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Lokasi GPS</label>
                            <div class="input-group">
                                <input type="text" name="location" id="location-input" class="form-control" placeholder="Mendeteksi lokasi..." readonly>
                                <button type="button" id="detect-location" class="btn btn-outline-secondary">
                                    <i class="ti ti-crosshair"></i>
                                </button>
                            </div>
                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">
                            <div id="location-status" class="small mt-1 text-secondary"></div>
                            @error('latitude')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 py-3" id="checkin-btn">
                            <i class="ti ti-login fs-1 me-2"></i>
                            <span class="fs-3">CHECK IN</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@push('styles')
<style>
    @media (max-width: 768px) {
        .page-body {
            margin-top: 0 !important;
        }
        .container-xl {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
(function() {
    function updateClock() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
        const dateStr = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        const el = document.getElementById('current-time');
        const de = document.getElementById('current-date');
        if (el) el.textContent = timeStr;
        if (de) de.textContent = dateStr;
    }
    updateClock();
    setInterval(updateClock, 1000);
})();
</script>
<script>
(function() {
    const startBtn = document.getElementById('start-camera');
    const cameraPreview = document.getElementById('camera-preview');
    const cameraStart = document.getElementById('camera-start');
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const captureBtn = document.getElementById('capture-btn');
    const retakeBtn = document.getElementById('retake-btn');
    const photoData = document.getElementById('photo-data');
    const photoInput = document.getElementById('photo-input');
    let stream = null;

    if (startBtn) {
        startBtn.addEventListener('click', async function() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } } });
                video.srcObject = stream;
                cameraPreview.style.display = 'block';
                cameraStart.style.display = 'none';
            } catch (err) {
                cameraStart.querySelector('p').textContent = 'Kamera tidak tersedia, silakan upload foto';
                photoInput.style.display = 'block';
            }
        });
    }

    if (captureBtn) {
        captureBtn.addEventListener('click', function() {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            photoData.value = canvas.toDataURL('image/jpeg', 0.8);
            video.style.display = 'none';
            captureBtn.style.display = 'none';
            retakeBtn.style.display = 'inline-block';
            photoInput.disabled = true;
            if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
        });
    }

    if (retakeBtn) {
        retakeBtn.addEventListener('click', async function() {
            video.style.display = 'block';
            captureBtn.style.display = 'inline-block';
            retakeBtn.style.display = 'none';
            photoData.value = '';
            photoInput.disabled = false;
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                video.srcObject = stream;
            } catch(e) {}
        });
    }

    const detectBtn = document.getElementById('detect-location');
    if (detectBtn) {
        detectBtn.addEventListener('click', function() {
            const locInput = document.getElementById('location-input');
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const locStatus = document.getElementById('location-status');
            locStatus.textContent = 'Mendeteksi lokasi...';
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(pos) {
                    latInput.value = pos.coords.latitude;
                    lngInput.value = pos.coords.longitude;
                    locInput.value = pos.coords.latitude + ', ' + pos.coords.longitude;
                    locStatus.textContent = 'Lokasi terdeteksi';
                    locStatus.className = 'small mt-1 text-success';
                }, function() {
                    locInput.value = '';
                    locInput.readOnly = false;
                    locInput.placeholder = 'Masukkan koordinat manual';
                    locStatus.textContent = 'Gagal deteksi, masukkan manual';
                    locStatus.className = 'small mt-1 text-danger';
                }, { enableHighAccuracy: true, timeout: 10000 });
            } else {
                locInput.readOnly = false;
                locInput.placeholder = 'Masukkan koordinat manual';
                locStatus.textContent = 'Geolokasi tidak didukung browser';
                locStatus.className = 'small mt-1 text-danger';
            }
        });
        detectBtn.click();
    }

    const form = document.getElementById('checkinForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const btn = document.getElementById('checkin-btn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
        });
    }
})();
</script>
@endpush

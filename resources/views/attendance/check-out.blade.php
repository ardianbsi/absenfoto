@extends('layouts.app')
@section('title', 'Check Out')

@section('content')
@if($attendance->check_out)
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <span class="avatar avatar-xl bg-info text-white">
                            <i class="ti ti-clipboard-check fs-1"></i>
                        </span>
                    </div>
                    <h3>Check Out Selesai</h3>
                    <div class="row g-3 mt-3">
                        <div class="col-6">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <p class="text-secondary mb-1">Check In</p>
                                    <p class="fw-bold mb-0 fs-3">{{ $attendance->check_in->format('H:i:s') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <p class="text-secondary mb-1">Check Out</p>
                                    <p class="fw-bold mb-0 fs-3">{{ $attendance->check_out->format('H:i:s') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <p class="text-secondary mb-1">Total Durasi</p>
                        <p class="display-6 fw-bold">{{ $attendance->duration_formatted }}</p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('attendance.my') }}" class="btn btn-primary">
                            <i class="ti ti-arrow-left me-2"></i>Lihat Riwayat
                        </a>
                                </div>
                            </div>
                            <div class="mt-2">
                                @php
                                    $typeClasses = ['wfo' => 'bg-blue', 'waf' => 'bg-green', 'wfh' => 'bg-indigo'];
                                    $typeLabels = ['wfo' => 'WFO', 'waf' => 'WAF', 'wfh' => 'WFH'];
                                    $type = $attendance->attendance_type ?? 'wfo';
                                @endphp
                                <span class="badge {{ $typeClasses[$type] ?? 'bg-secondary' }}">
                                    {{ $typeLabels[$type] ?? 'WFO' }}
                                </span>
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
                                <div class="text-start">
                                    <p class="mb-0 text-secondary">Check In</p>
                                    <p class="fw-bold mb-0 fs-4">{{ $attendance->check_in->format('H:i:s') }}</p>
                                </div>
                                <div class="text-end">
                                    <p class="mb-0 text-secondary">Durasi</p>
                                    <p class="fw-bold mb-0 fs-4" id="current-duration">00:00:00</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('attendance.store-check-out', $attendance) }}" enctype="multipart/form-data" id="checkoutForm">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Foto Check Out (opsional)</label>
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

                        <button type="submit" class="btn btn-warning btn-lg w-100 py-3" id="checkout-btn">
                            <i class="ti ti-logout fs-1 me-2"></i>
                            <span class="fs-3">CHECK OUT</span>
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
    const checkinTime = '{{ $attendance->check_in->format('H:i:s') }}';
    const checkinDate = '{{ $attendance->check_in->format('Y-m-d H:i:s') }}';
    function updateDuration() {
        const checkin = new Date(checkinDate).getTime();
        const now = new Date().getTime();
        const diff = Math.floor((now - checkin) / 1000);
        const h = String(Math.floor(diff / 3600)).padStart(2, '0');
        const m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
        const s = String(diff % 60).padStart(2, '0');
        const el = document.getElementById('current-duration');
        if (el) el.textContent = h + ':' + m + ':' + s;
    }
    updateDuration();
    setInterval(updateDuration, 1000);
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

    const form = document.getElementById('checkoutForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const btn = document.getElementById('checkout-btn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
        });
    }
})();
</script>
@endpush

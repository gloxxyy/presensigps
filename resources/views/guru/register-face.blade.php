@extends('layouts.admin.tabler')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Registrasi Wajah Guru</h2>
            </div>
            <div class="col-auto ms-auto">
                <a href="/guru" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M4 8V6a2 2 0 0 1 2-2h2"/><path d="M4 16v2a2 2 0 0 0 2 2h2"/><path d="M16 4h2a2 2 0 0 1 2 2v2"/><path d="M16 20h2a2 2 0 0 0 2-2v-2"/><circle cx="12" cy="12" r="3"/></svg>
                            Daftarkan Wajah: <strong>{{ $guru->nama_lengkap }}</strong>
                        </h3>
                    </div>
                    <div class="card-body">

                        {{-- Status wajah saat ini --}}
                        <div class="alert {{ !empty($guru->face_descriptor) ? 'alert-success' : 'alert-warning' }} mb-3">
                            @if (!empty($guru->face_descriptor))
                                <strong>✅ Wajah sudah terdaftar</strong> — terdaftar pada {{ $guru->face_registered_at ?? '-' }}. Anda dapat mendaftarkan ulang di bawah ini.
                            @else
                                <strong>⚠️ Wajah belum terdaftar</strong> — Silakan ambil foto wajah di bawah ini.
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                {{-- Kamera --}}
                                <div class="position-relative" style="border-radius:12px;overflow:hidden;background:#000;">
                                    <video id="video" width="100%" height="360" autoplay muted playsinline style="display:block;border-radius:12px;"></video>
                                    <canvas id="overlay" style="position:absolute;top:0;left:0;"></canvas>
                                    <div id="statusBox" style="position:absolute;bottom:10px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,0.7);color:white;padding:6px 16px;border-radius:20px;font-size:14px;white-space:nowrap;">
                                        Memuat model...
                                    </div>
                                </div>

                                <div class="mt-3 d-flex gap-2">
                                    <button id="btnAmbilFoto" class="btn btn-primary flex-fill" disabled>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 4h-5l-1 1H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-3.5z"/><circle cx="12" cy="13" r="3"/></svg>
                                        Ambil Foto & Daftarkan Wajah
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light h-100">
                                    <div class="card-body">
                                        <h4 class="card-title">Info Guru</h4>
                                        <table class="table table-sm">
                                            <tr><th>NIP</th><td>{{ $guru->nip }}</td></tr>
                                            <tr><th>Nama</th><td>{{ $guru->nama_lengkap }}</td></tr>
                                            <tr><th>Mapel</th><td>{{ $guru->mata_pelajaran }}</td></tr>
                                        </table>

                                        <hr>

                                        <h5>Panduan:</h5>
                                        <ol class="small text-muted">
                                            <li>Pastikan wajah menghadap kamera dengan pencahayaan cukup</li>
                                            <li>Tunggu kotak wajah terdeteksi (berwarna hijau)</li>
                                            <li>Klik tombol "Ambil Foto"</li>
                                            <li>Data wajah tersimpan otomatis</li>
                                        </ol>

                                        <div id="hasilRegistrasi" class="mt-3" style="display:none;">
                                            <div class="alert alert-success">
                                                <strong>✅ Wajah berhasil didaftarkan!</strong>
                                            </div>
                                        </div>
                                        <div id="errorRegistrasi" class="mt-3" style="display:none;">
                                            <div class="alert alert-danger" id="errorMsg"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Canvas tersembunyi untuk capture --}}
<canvas id="captureCanvas" style="display:none;"></canvas>
@endsection

@push('myscript')
{{-- face-api.js dari CDN --}}
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
const video       = document.getElementById('video');
const overlay     = document.getElementById('overlay');
const statusBox   = document.getElementById('statusBox');
const btnAmbil    = document.getElementById('btnAmbilFoto');
const hasilDiv    = document.getElementById('hasilRegistrasi');
const errorDiv    = document.getElementById('errorRegistrasi');
const errorMsg    = document.getElementById('errorMsg');

const MODEL_URL   = '/face-api/models';
const NIP         = '{{ $guru->nip }}';
const CSRF        = '{{ csrf_token() }}';

let faceDetected  = false;
let currentDetections = null;

async function loadModels() {
    statusBox.textContent = 'Memuat model AI...';
    await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
    await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
    await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
    statusBox.textContent = 'Model siap. Arahkan wajah ke kamera.';
    startCamera();
}

async function startCamera() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 360 } });
        video.srcObject = stream;
        video.onloadedmetadata = () => {
            overlay.width  = video.videoWidth;
            overlay.height = video.videoHeight;
            detectLoop();
        };
    } catch(e) {
        statusBox.textContent = 'Tidak dapat mengakses kamera: ' + e.message;
    }
}

async function detectLoop() {
    const ctx = overlay.getContext('2d');

    setInterval(async () => {
        const detections = await faceapi.detectAllFaces(
            video,
            new faceapi.TinyFaceDetectorOptions({ scoreThreshold: 0.5 })
        ).withFaceLandmarks().withFaceDescriptors();

        ctx.clearRect(0, 0, overlay.width, overlay.height);
        faceapi.draw.drawDetections(overlay, detections);
        faceapi.draw.drawFaceLandmarks(overlay, detections);

        if (detections.length === 1) {
            faceDetected       = true;
            currentDetections  = detections;
            statusBox.textContent = '✅ Wajah terdeteksi! Siap untuk daftar.';
            statusBox.style.background = 'rgba(0, 150, 0, 0.8)';
            btnAmbil.disabled  = false;
        } else if (detections.length === 0) {
            faceDetected       = false;
            currentDetections  = null;
            statusBox.textContent = '❌ Wajah tidak terdeteksi';
            statusBox.style.background = 'rgba(180, 0, 0, 0.8)';
            btnAmbil.disabled  = true;
        } else {
            faceDetected       = false;
            currentDetections  = null;
            statusBox.textContent = '⚠️ Terdeteksi lebih dari 1 wajah';
            statusBox.style.background = 'rgba(180, 100, 0, 0.8)';
            btnAmbil.disabled  = true;
        }
    }, 300);
}

btnAmbil.addEventListener('click', async () => {
    if (!faceDetected || !currentDetections) {
        alert('Wajah belum terdeteksi!');
        return;
    }

    // Ambil face descriptor (128 float angka unik)
    const descriptor = currentDetections[0].descriptor;
    const descriptorJSON = JSON.stringify(Array.from(descriptor));

    btnAmbil.disabled = true;
    btnAmbil.textContent = 'Menyimpan...';

    try {
        const response = await fetch('/guru/store-face-descriptor', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF
            },
            body: JSON.stringify({ nip: NIP, face_descriptor: descriptorJSON })
        });

        const result = await response.json();

        if (result.status === 'success') {
            hasilDiv.style.display = 'block';
            errorDiv.style.display = 'none';
            statusBox.textContent = '✅ Wajah berhasil didaftarkan!';
        } else {
            errorMsg.textContent = '❌ Gagal: ' + result.message;
            errorDiv.style.display = 'block';
        }
    } catch(e) {
        errorMsg.textContent = '❌ Error: ' + e.message;
        errorDiv.style.display = 'block';
    }

    btnAmbil.disabled = false;
    btnAmbil.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 4h-5l-1 1H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-3.5z"/><circle cx="12" cy="13" r="3"/></svg> Ambil Foto & Daftarkan Wajah`;
});

// Mulai load model saat halaman ready
window.onload = loadModels;
</script>
@endpush

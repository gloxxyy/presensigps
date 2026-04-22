@extends('layouts.presensi')
@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">E-Presensi Guru</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->
    <style>
        .webcam-capture,
        .webcam-capture video {
            display: inline-block;
            width: 100% !important;
            margin: auto;
            height: auto !important;
            border-radius: 15px;
        }

        #map { height: 200px; }

        .jam-digital-malasngoding {
            background-color: #27272783;
            position: absolute;
            top: 65px;
            right: 10px;
            z-index: 9999;
            width: 150px;
            border-radius: 10px;
            padding: 5px;
        }

        .jam-digital-malasngoding p {
            color: #fff;
            font-size: 16px;
            text-align: left;
            margin-top: 0;
            margin-bottom: 0;
        }

        #faceStatusBadge {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 9999;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            background: rgba(0,0,0,0.6);
            color: white;
            transition: background 0.3s;
        }
        #faceStatusBadge.success { background: rgba(0,150,0,0.85); }
        #faceStatusBadge.danger  { background: rgba(200,0,0,0.85); }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <!-- face-api.js -->
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
@endsection

@section('content')
    <div class="row" style="margin-top: 60px">
        <div class="col">
            <input type="hidden" id="lokasi">
            <div style="position:relative;">
                <div class="webcam-capture" style="display:none;"></div>
                <!-- Overlay video untuk face recognition & absen -->
                <video id="faceVideo" autoplay muted playsinline style="display:block; width:100%; border-radius:15px;"></video>
                <canvas id="faceOverlay" style="position:absolute; top:0; left:0; z-index:10; pointer-events:none;"></canvas>
                <div id="faceStatusBadge">⏳ Memuat AI...</div>
            </div>
        </div>
    </div>
    <div class="jam-digital-malasngoding">
        <p>{{ $hariini }}</p>
        <p id="jam"></p>
        <p>Presensi: 07:00 - 17:00</p>
        @if ($cek > 0)
            <p>✅ Masuk: {{ $datapresensi->jam_in ?? '-' }}</p>
        @else
            <p>⏳ Belum Absen Masuk</p>
        @endif
    </div>

    <!-- Info face recognition -->
    <div class="row mt-2">
        <div class="col">
            <div class="alert alert-info py-2 mb-1" id="faceInfoBox">
                🔍 Sistem sedang memverifikasi wajah Anda...
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            @if ($cek > 0)
                <button id="takeabsen" class="btn btn-danger btn-block" disabled>
                    <ion-icon name="camera-outline"></ion-icon>
                    Absen Pulang
                </button>
            @else
                <button id="takeabsen" class="btn btn-primary btn-block" disabled>
                    <ion-icon name="camera-outline"></ion-icon>
                    Absen Masuk
                </button>
            @endif
        </div>
    </div>
    <div class="row mt-2">
        <div class="col">
            <div id="map"></div>
        </div>
    </div>

    <audio id="notifikasi_in">
        <source src="{{ asset('assets/sound/notifikasi_in.mp3') }}" type="audio/mpeg">
    </audio>
    <audio id="notifikasi_out">
        <source src="{{ asset('assets/sound/notifikasi_out.mp3') }}" type="audio/mpeg">
    </audio>
    <audio id="radius_sound">
        <source src="{{ asset('assets/sound/radius.mp3') }}" type="audio/mpeg">
    </audio>
@endsection

@push('myscript')
    <script type="text/javascript">
        window.onload = function() {
            jam();
            initFaceRecognition();
        }

        function jam() {
            var e = document.getElementById('jam'), d = new Date(), h, m, s;
            h = d.getHours(); m = set(d.getMinutes()); s = set(d.getSeconds());
            e.innerHTML = h + ':' + m + ':' + s;
            setTimeout('jam()', 1000);
        }

        function set(e) {
            e = e < 10 ? '0' + e : e;
            return e;
        }
    </script>

    <script>
        var notifikasi_in  = document.getElementById('notifikasi_in');
        var notifikasi_out = document.getElementById('notifikasi_out');
        var radius_sound   = document.getElementById('radius_sound');

        // Webcam untuk foto absen
        // Webcam.set({ height: 480, width: 640, image_format: 'jpeg', jpeg_quality: 80 });
        // SEMENTARA DIMATIKAN UNTUK TEST KONFLIK KAMERA: Webcam.attach('.webcam-capture');

        // Geolocation
        var lokasi = document.getElementById('lokasi');
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
        }

        function successCallback(position) {
            lokasi.value = position.coords.latitude + "," + position.coords.longitude;
            var map = L.map('map').setView([position.coords.latitude, position.coords.longitude], 18);
            var lokasi_sekolah = "{{ $lok_sekolah->lokasi_sekolah }}";
            var lok = lokasi_sekolah.split(",");
            var lat_sekolah  = lok[0];
            var long_sekolah = lok[1];
            var radius = "{{ $lok_sekolah->radius_sekolah }}";
            L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                maxZoom: 20, subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            }).addTo(map);
            L.marker([position.coords.latitude, position.coords.longitude]).addTo(map);
            L.circle([lat_sekolah, long_sekolah], {
                color: 'red', fillColor: '#f03', fillOpacity: 0.5, radius: radius
            }).addTo(map);
        }

        function errorCallback() {}

        // ============================================================
        // FACE RECOGNITION
        // ============================================================
        const MODEL_URL      = '/face-api/models';
        const faceStatusBadge = document.getElementById('faceStatusBadge');
        const faceInfoBox    = document.getElementById('faceInfoBox');
        const btnAbsen       = document.getElementById('takeabsen');

        let faceVerified     = false;
        let labeledDescriptors = null;

        async function initFaceRecognition() {
            try {
                faceStatusBadge.textContent = '⏳ Memuat AI...';

                // Muat model face-api.js
                await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
                await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
                await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);

                // Ambil face descriptors dari server
                const response = await fetch('/presensi/get-face-descriptors');
                const guruList = await response.json();

                if (guruList.length === 0) {
                    faceStatusBadge.textContent = '⚠️ Tidak ada data wajah';
                    faceInfoBox.className = 'alert alert-warning py-2 mb-1';
                    faceInfoBox.textContent = 'Belum ada wajah yang terdaftar. Hubungi admin untuk mendaftarkan wajah.';
                    btnAbsen.disabled = false; // Izinkan tanpa face recognition jika tidak ada data
                    return;
                }

            const myNip = "{{ Auth::guard('guru')->user()->nip }}";
            
            // Cek apakah NIP user saat ini ada di dalam list yang ditarik dari server
            const myFaceData = guruList.find(g => String(g.nip) === String(myNip));
            if (!myFaceData) {
                faceStatusBadge.textContent = '⚠️ Wajah Anda belum terdaftar';
                faceInfoBox.className = 'alert alert-warning py-2 mb-1';
                faceInfoBox.textContent = 'Wajah Anda belum terdaftar dalam sistem. Hubungi admin untuk mendaftarkan wajah Anda.';
                btnAbsen.disabled = false; // Bypass jika memang tidak wajib atau izinkan dengan peringatan
                return;
            }

            // Buat labeled descriptors HANYA untuk wajah user ini agar presisi lebih tinggi dan tidak tertukar
            const descriptorArray = JSON.parse(myFaceData.face_descriptor);
            labeledDescriptors = [
                new faceapi.LabeledFaceDescriptors(
                    myNip,
                    [new Float32Array(descriptorArray)]
                )
            ];

            faceStatusBadge.textContent = '📷 Verifikasi wajah...';
            faceInfoBox.textContent = '📷 Arahkan wajah ke kamera untuk verifikasi...';

            await startFaceCamera();

        } catch(e) {
            console.error('Face recognition error:', e);
            faceStatusBadge.textContent = '⚠️ Face recognition tidak tersedia';
            faceInfoBox.className = 'alert alert-warning py-2 mb-1';
            faceInfoBox.textContent = 'Face recognition tidak tersedia (' + e.message + '). Presensi tetap bisa dilakukan.';
            btnAbsen.disabled = false;
        }
    }

    async function startFaceCamera() {
        const video = document.getElementById('faceVideo');
        const constraints = {
            video: {
                width: { ideal: 640 },
                height: { ideal: 480 },
                facingMode: "user" // Memastikan kamera depan yang digunakan
            }
        };
    
        try {
            // Cek apakah browser mendukung
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                throw new Error("Browser Anda tidak mendukung akses kamera.");
            }
    
            const stream = await navigator.mediaDevices.getUserMedia(constraints);
            video.srcObject = stream;
    
            video.onloadedmetadata = () => {
                video.play();
                detectFaceLoop(video);
            };
        } catch (e) {
            console.error("Error akses kamera:", e);
            btnAbsen.disabled = false;
            // Tampilkan pesan error yang lebih jelas di UI
            faceInfoBox.className = 'alert alert-danger py-2 mb-1';
            faceInfoBox.textContent = 'Gagal akses kamera: ' + e.message + '. Pastikan izin diberikan di pengaturan browser.';
            
            // Trigger manual popup jika gagal otomatis (khusus beberapa browser)
            Swal.fire({
                title: 'Izin Kamera Dibutuhkan',
                text: 'Klik tombol di pojok alamat web (ikon gembok) untuk mengizinkan kamera, lalu refresh halaman.',
                icon: 'warning'
            });
        }
    }

    async function detectFaceLoop(video) {
        // Threshold deteksi (semakin kecil semakin ketat/mirip, 0.58 agar sedikit toleran dengan pencahayaan)
        const faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.58);
        const myNip = "{{ Auth::guard('guru')->user()->nip }}";

        const detectInterval = setInterval(async () => {
            if (faceVerified) {
                clearInterval(detectInterval);
                return;
            }

            const detections = await faceapi.detectAllFaces(
                video, new faceapi.TinyFaceDetectorOptions({ scoreThreshold: 0.5 })
            ).withFaceLandmarks().withFaceDescriptors();

            if (detections.length === 1) {
                const bestMatch = faceMatcher.findBestMatch(detections[0].descriptor);

                // HARUS cocok dengan NIP user yang login
                if (bestMatch.label === String(myNip)) {
                    faceVerified = true;
                    clearInterval(detectInterval);

                    faceStatusBadge.className = 'success';
                    faceStatusBadge.textContent = '✅ Wajah ' + myNip + ' Dikenali';
                    faceInfoBox.className = 'alert alert-success py-2 mb-1';
                    faceInfoBox.textContent = '✅ Wajah Anda berhasil diverifikasi! Silakan absen sekarang.';
                    btnAbsen.disabled = false;

                    // Biarkan kamera menyala untuk dijepret saat tombol absen ditekan
                    // video.srcObject.getTracks().forEach(t => t.stop());
                } else {
                    faceStatusBadge.className = 'danger';
                    faceStatusBadge.textContent = '❌ Wajah tidak cocok';
                    faceInfoBox.className = 'alert alert-danger py-2 mb-1';
                    faceInfoBox.textContent = '❌ Wajah tidak cocok dengan data NIP Anda. Coba lagi.';
                    btnAbsen.disabled = true;
                }
                } else if (detections.length === 0) {
                    faceStatusBadge.textContent = '🔍 Mencari wajah...';
                    faceStatusBadge.className = '';
                }
            }, 800);
        }

        // ============================================================
        // ABSEN BUTTON
        // ============================================================
        $("#takeabsen").click(function(e) {
            var videoCapture = document.getElementById('faceVideo');
            
            // Buat canvas untuk capture
            var canvas = document.createElement('canvas');
            // Gunakan ukuran asli video dari stream
            canvas.width = videoCapture.videoWidth;
            canvas.height = videoCapture.videoHeight;
            
            var ctx = canvas.getContext('2d');
            // Mirroring jika kamera depan (opsional, tapi biasanya guru lebih suka ini)
            // ctx.translate(canvas.width, 0);
            // ctx.scale(-1, 1);
            
            ctx.drawImage(videoCapture, 0, 0, canvas.width, canvas.height);
            var image = canvas.toDataURL('image/jpeg', 0.8);

            var lokasi = $("#lokasi").val();
            
            if (lokasi === "") {
                Swal.fire({title: 'Lokasi Belum Ditemukan!', text: 'Sistem belum mendapatkan koordinat GPS Anda. Pastikan Fitur Lokasi/GPS di HP menyala.', icon: 'warning'});
                return;
            }

            $.ajax({
                type: 'POST',
                url: '/presensi/store',
                data: {
                    _token: "{{ csrf_token() }}",
                    image: image,
                    lokasi: lokasi
                },
                cache: false,
                success: function(respond) {
                    var status = respond.split("|");
                    if (status[0] == "success") {
                        if (status[2] == "in") {
                            notifikasi_in.play();
                        } else {
                            notifikasi_out.play();
                        }
                        Swal.fire({
                            title: 'Berhasil!',
                            text: status[1],
                            icon: 'success'
                        });
                        setTimeout("location.href='/dashboard'", 3000);
                    } else {
                        if (status[2] == "radius") {
                            radius_sound.play();
                        }
                        Swal.fire({
                            title: 'Error!',
                            text: status[1],
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Server Error!',
                        text: 'Terjadi kesalahan sistem: ' + xhr.status,
                        icon: 'error'
                    });
                }
            });
        });
    </script>
@endpush
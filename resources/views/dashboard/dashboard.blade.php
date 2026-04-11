@extends('layouts.presensi')
@section('content')
    <style>
        .logout {
            position: absolute;
            color: white;
            font-size: 30px;
            text-decoration: none;
            right: 15px;
            top: 25px;
            transition: all 0.3s;
        }

        .logout:hover {
            color: #ffeaa7;
            transform: scale(1.1);
        }

        /* Modern Gradient Background for User Section */
        #user-section {
            background: linear-gradient(135deg, #0984e3 0%, #74b9ff 100%) !important;
            border-bottom-left-radius: 35px;
            border-bottom-right-radius: 35px;
            box-shadow: 0 10px 20px rgba(9, 132, 227, 0.2);
            padding: 30px 20px 30px 20px !important;
            margin-bottom: 0px !important;
            height: auto !important;
        }

        #menu-section {
            margin-top: 15px !important;
        }

        #user-detail {
            display: flex;
            align-items: center;
        }

        #user-detail .avatar {
            margin-right: 15px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,0.8);
            overflow: hidden;
            transition: transform 0.3s;
        }
        #user-detail .avatar:hover {
            transform: scale(1.05);
        }

        .user-info-custom h3 {
            color: white;
            font-weight: 700;
            margin-bottom: 2px;
            font-size: 1.3rem;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.25);
            letter-spacing: 0.5px;
        }

        .user-info-custom span {
            color: rgba(255,255,255,0.95);
            font-size: 0.85rem;
            font-weight: 500;
            display: block;
        }

        /* Menu Icons Animation */
        .item-menu {
            transition: transform 0.2s ease-in-out;
            cursor: pointer;
        }
        .item-menu:hover {
            transform: translateY(-5px);
        }
        .item-menu .menu-icon a {
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
            transition: all 0.3s ease;
        }
        .item-menu:hover .menu-icon a {
            filter: drop-shadow(0 6px 10px rgba(0,0,0,0.15));
        }
        .menu-name {
            font-weight: 600;
            color: #2d3436;
            margin-top: 5px;
        }

        /* Modern Cards */
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
            overflow: hidden;
            background: #ffffff;
        }
        .card:hover {
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        /* Presence Cards with Modern Gradients */
        .gradasigreen {
            background: linear-gradient(135deg, #1dd1a1 0%, #10ac84 100%) !important;
            color: white;
        }
        .gradasired {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5253 100%) !important;
            color: white;
        }
        .presence-content-custom {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .presence-title-custom {
            color: white !important;
            font-weight: 700 !important;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.15);
            margin: 0;
            font-size: 0.9rem;
        }
        .presence-detail-custom {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 3px;
        }
        .presence-detail-custom span {
            color: white !important;
            font-weight: 800;
            font-size: 1.15rem;
            background: rgba(0,0,0,0.2);
            padding: 3px 8px;
            border-radius: 8px;
            display: inline-block;
        }

        /* Rekap Item Tweaks */
        #rekappresensi h3 {
            font-weight: 700;
            color: #2d3436;
            margin-bottom: 12px;
        }
        #rekappresensi span.badge {
            border-radius: 8px;
            padding: 4px 6px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            border: 2px solid white;
        }

        .icon-presence-custom {
            background: rgba(255, 255, 255, 0.25) !important;
            border-radius: 12px !important;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .icon-presence-custom ion-icon {
            font-size: 26px;
            color: white;
        }
        
    </style>
    <div class="section" id="user-section">
        <a href="/proseslogout" class="logout">
            <ion-icon name="exit-outline"></ion-icon>
        </a>
        <div id="user-detail">
            <div class="avatar">
                @if (!empty(Auth::guard('guru')->user()->foto))
                    @php
                        $path = Storage::url('uploads/guru/' . Auth::guard('guru')->user()->foto);
                    @endphp
                    <img src="{{ url($path) }}" alt="avatar" class="imaged w64" style="height:60px">
                @else
                    <img src="assets/img/sample/avatar/avatar1.jpg" alt="avatar" class="imaged w64 rounded">
                @endif
            </div>
            <div class="user-info-custom" style="display: flex; flex-direction: column; justify-content: center;">
                <h3>{{ Auth::guard('guru')->user()->nama_lengkap }}</h3>
                <span>{{ $sekolah->nama_sekolah }}</span>
                <span style="font-size: 0.75rem; color: rgba(255,255,255,0.85);">{{ Auth::guard('guru')->user()->mata_pelajaran }} &mdash; {{ $jurusan->nama_jurusan }}</span>
            </div>
        </div>
    </div>

    <div class="section" id="menu-section">
        <div class="card">
            <div class="card-body text-center">
                <div class="list-menu">
                    <div class="item-menu text-center">
                        <div class="menu-icon">
                            <a href="/editprofile" class="green" style="font-size: 40px;">
                                <ion-icon name="person-sharp"></ion-icon>
                            </a>
                        </div>
                        <div class="menu-name">
                            <span class="text-center">Profil</span>
                        </div>
                    </div>
                    <div class="item-menu text-center">
                        <div class="menu-icon">
                            <a href="/presensi/izin" class="danger" style="font-size: 40px;">
                                <ion-icon name="calendar-number"></ion-icon>
                            </a>
                        </div>
                        <div class="menu-name">
                            <span class="text-center">Cuti</span>
                        </div>
                    </div>
                    <div class="item-menu text-center">
                        <div class="menu-icon">
                            <a href="/presensi/histori" class="warning" style="font-size: 40px;">
                                <ion-icon name="document-text"></ion-icon>
                            </a>
                        </div>
                        <div class="menu-name">
                            <span class="text-center">Histori</span>
                        </div>
                    </div>
                    <div class="item-menu text-center">
                        <div class="menu-icon">
                            <a href="" class="orange" style="font-size: 40px;">
                                <ion-icon name="location"></ion-icon>
                            </a>
                        </div>
                        <div class="menu-name">
                            Lokasi
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="section mt-2" id="presence-section">
        <div class="todaypresence">
            <div class="row">

                <div class="col-6">
                    <div class="card gradasigreen">
                        <div class="card-body">
                            <div class="presence-content-custom">
                                <div class="icon-presence-custom">
                                    @if ($presensihariini != null)
                                        @if ($presensihariini->foto_in != null)
                                            @php
                                                $path = Storage::url('uploads/absensi/' . $presensihariini->foto_in);
                                            @endphp
                                            <img src="{{ url($path) }}" alt="" class="imaged w48">
                                        @else
                                            <ion-icon name="camera"></ion-icon>
                                        @endif
                                    @else
                                        <ion-icon name="camera"></ion-icon>
                                    @endif
                                </div>
                                <div class="presence-detail-custom">
                                    <h4 class="presence-title-custom">Masuk</h4>
                                    <span>{{ $presensihariini != null ? $presensihariini->jam_in : 'Belum Absen' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card gradasired">
                        <div class="card-body">
                            <div class="presence-content-custom">
                                <div class="icon-presence-custom">
                                    @if ($presensihariini != null && $presensihariini->jam_out != null)
                                        @if ($presensihariini->foto_out != null)
                                            @php
                                                $path = Storage::url('uploads/absensi/' . $presensihariini->foto_out);
                                            @endphp
                                            <img src="{{ url($path) }}" alt="" class="imaged w48">
                                        @else
                                            <ion-icon name="camera"></ion-icon>
                                        @endif
                                    @else
                                        <ion-icon name="camera"></ion-icon>
                                    @endif
                                </div>
                                <div class="presence-detail-custom">
                                    <h4 class="presence-title-custom">Pulang</h4>
                                    <span>{{ $presensihariini != null && $presensihariini->jam_out != null ? $presensihariini->jam_out : 'Belum Absen' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="rekappresensi">
            <h3>Rekap Presensi Bulan {{ $namabulan[$bulanini] }} Tahun {{ $tahunini }}</h3>
            <div class="row">
                <div class="col-3">
                    <div class="card">
                        <div class="card-body text-center" style="padding: 12px 12px !important; line-height:0.8rem">
                            <span class="badge bg-danger"
                                style="position: absolute; top:3px; right:10px; font-size:0.6rem; z-index:999">{{ $rekappresensi->jmlhadir }}</span>
                            <ion-icon name="accessibility-outline" style="font-size: 1.6rem;"
                                class="text-primary mb-1"></ion-icon>
                            <br>
                            <span style="font-size: 0.8rem; font-weight:500">Hadir</span>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card">
                        <div class="card-body text-center" style="padding: 12px 12px !important; line-height:0.8rem">
                            <span class="badge bg-danger"
                                style="position: absolute; top:3px; right:10px; font-size:0.6rem; z-index:999">
                                {{ $rekappresensi->jmlizin }}
                            </span>
                            <ion-icon name="newspaper-outline" style="font-size: 1.6rem;"
                                class="text-success mb-1"></ion-icon>
                            <br>
                            <span style="font-size: 0.8rem; font-weight:500">Izin</span>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card">
                        <div class="card-body text-center" style="padding: 12px 12px !important; line-height:0.8rem">
                            <span class="badge bg-danger"
                                style="position: absolute; top:3px; right:10px; font-size:0.6rem; z-index:999">
                                {{ $rekappresensi->jmlsakit }}</span>
                            <ion-icon name="medkit-outline" style="font-size: 1.6rem;"
                                class="text-warning mb-1"></ion-icon>
                            <br>
                            <span style="font-size: 0.8rem; font-weight:500">Sakit</span>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card">
                        <div class="card-body text-center" style="padding: 12px 12px !important; line-height:0.8rem">
                            <span class="badge bg-danger"
                                style="position: absolute; top:3px; right:10px; font-size:0.6rem; z-index:999">
                                {{ $rekappresensi->jmlcuti }}
                            </span>
                            <ion-icon name="document-outline" style="font-size: 1.6rem;"
                                class="text-danger mb-1"></ion-icon>
                            <br>
                            <span style="font-size: 0.8rem; font-weight:500">Cuti</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="presencetab mt-2">
            <div class="tab-pane fade show active" id="pilled" role="tabpanel">
                <ul class="nav nav-tabs style1" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#home" role="tab">
                            Bulan Ini
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#profile" role="tab">
                            Leaderboard
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tab-content mt-2" style="margin-bottom:100px;">
                <div class="tab-pane fade show active" id="home" role="tabpanel">
                    <!--
                                                                                                                                                                                                                    <ul class="listview image-listview">
                                                                                                                                                                                                                        @foreach ($historibulanini as $d)
    @php
        $path = Storage::url('uploads/absensi/' . $d->foto_in);
    @endphp
                                                                                                                                                                                                                        <li>
                                                                                                                                                                                                                            <div class="item">
                                                                                                                                                                                                                                <div class="icon-box bg-primary">
                                                                                                                                                                                                                                    <ion-icon name="finger-print-outline"></ion-icon>
                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                <div class="in">
                                                                                                                                                                                                                                    <div>{{ date('d-m-Y', strtotime($d->tgl_presensi)) }}</div>
                                                                                                                                                                                                                                    <span class="badge badge-success">{{ $d->jam_in }}</span>
                                                                                                                                                                                                                                    <span class="badge badge-danger">{{ $presensihariini != null && $d->jam_out != null ? $d->jam_out : 'Belum Absen' }}</span>
                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                        </li>
    @endforeach
                                                                                                                                                                                                                    </ul>
                                                                                                                                                                                                                -->

                    @foreach ($historibulanini as $d)
                        @if ($d->status == 'h')
                            <div class="card mb-1" style="border : 1px solid blue">
                                <div class="card-body">
                                    <div class="historicontent">
                                        <div class="iconpresensi">
                                            <ion-icon name="finger-print-outline" style="font-size: 48px;"
                                                class="text-success"></ion-icon>
                                        </div>
                                        <div class="datapresensi">
                                            <h3 style="line-height: 3px">{{ $d->nama_jam_kerja }}</h3>
                                            <h4 style="margin:0px !important">
                                                {{ date('d-m-Y', strtotime($d->tgl_presensi)) }}</h4>
                                            <span style="color:green">{{ date('H:i', strtotime($d->jam_masuk)) }} -
                                                {{ date('H:i', strtotime($d->jam_pulang)) }}</span>
                                            <br>
                                            <span>
                                                {!! $d->jam_in != null ? date('H:i', strtotime($d->jam_in)) : '<span class="text-danger">Belum Scan</span>' !!}
                                            </span>
                                            <span>
                                                {!! $d->jam_out != null
                                                    ? '-' . date('H:i', strtotime($d->jam_out))
                                                    : '<span class="text-danger">- Belum Scan</span>' !!}
                                            </span>
                                            <br>
                                            @php
                                                //Jam Ketika dia Absen
                                                $jam_in = date('H:i', strtotime($d->jam_in));

                                                //Jam Jadwal Masuk
                                                $jam_masuk = date('H:i', strtotime($d->jam_masuk));

                                                $jadwal_jam_masuk = $d->tgl_presensi . ' ' . $jam_masuk;
                                                $jam_presensi = $d->tgl_presensi . ' ' . $jam_in;
                                            @endphp
                                            @if ($jam_in > $jam_masuk)
                                                @php
                                                    $jmlterlambat = hitungjamterlambat(
                                                        $jadwal_jam_masuk,
                                                        $jam_presensi,
                                                    );
                                                    $jmlterlambatdesimal = hitungjamterlambatdesimal(
                                                        $jadwal_jam_masuk,
                                                        $jam_presensi,
                                                    );
                                                @endphp
                                                <span class="danger">Terlambat {{ $jmlterlambat }}
                                                    ({{ $jmlterlambatdesimal }} Jam)
                                                </span>
                                            @else
                                                <span style="color:green">Tepat Waktu</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($d->status == 'i')
                            <div class="card mb-1">
                                <div class="card-body">
                                    <div class="historicontent">
                                        <div class="iconpresensi">
                                            <ion-icon name="document-outline" style="font-size: 48px;"
                                                class="text-warning"></ion-icon>
                                        </div>
                                        <div class="datapresensi">
                                            <h3 style="line-height: 3px">IZIN - {{ $d->kode_izin }}</h3>
                                            <h4 style="margin:0px !important">
                                                {{ date('d-m-Y', strtotime($d->tgl_presensi)) }}</h4>
                                            <span>
                                                {{ $d->keterangan }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($d->status == 's')
                            <div class="card mb-1">
                                <div class="card-body">
                                    <div class="historicontent">
                                        <div class="iconpresensi">
                                            <ion-icon name="medkit-outline" style="font-size: 48px;"
                                                class="text-primary"></ion-icon>
                                        </div>
                                        <div class="datapresensi">
                                            <h3 style="line-height: 3px">SAKIT - {{ $d->kode_izin }}</h3>
                                            <h4 style="margin:0px !important">
                                                {{ date('d-m-Y', strtotime($d->tgl_presensi)) }}</h4>
                                            <span>
                                                {{ $d->keterangan }}
                                            </span>
                                            <br>
                                            @if (!empty($d->doc_sid))
                                                <span style="color: blue">
                                                    <ion-icon name="document-attach-outline"></ion-icon> SID
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($d->status == 'c')
                            <div class="card mb-1">
                                <div class="card-body">
                                    <div class="historicontent">
                                        <div class="iconpresensi">
                                            <ion-icon name="document-outline" style="font-size: 48px;"
                                                class="text-info"></ion-icon>
                                        </div>
                                        <div class="datapresensi">
                                            <h3 style="line-height: 3px">CUTI - {{ $d->kode_izin }}</h3>
                                            <h4 style="margin:0px !important">
                                                {{ date('d-m-Y', strtotime($d->tgl_presensi)) }}</h4>
                                            <span class="text-info">
                                                {{ $d->nama_cuti }}
                                            </span>
                                            <br>
                                            <span>
                                                {{ $d->keterangan }}
                                            </span>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="tab-pane fade" id="profile" role="tabpanel">
                    <ul class="listview image-listview">
                        @foreach ($leaderboard as $d)
                            <li>
                                <div class="item">
                                    <img src="assets/img/sample/avatar/avatar1.jpg" alt="image" class="image">
                                    <div class="in">
                                        <div>
                                            <b>{{ $d->nama_lengkap }}</b><br>
                                            <small class="text-muted">{{ $d->mata_pelajaran }}</small>
                                        </div>
                                        <span class="badge {{ $d->jam_in < '07:00' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $d->jam_in }}
                                        </span>
                                    </div>
                                </div>
                            </li>
                        @endforeach

                    </ul>
                </div>

            </div>
        </div>
    </div>
@endsection

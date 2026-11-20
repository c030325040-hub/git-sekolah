<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Sekolah - {{ $sekolah->nama }}</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }

        header {
            background: #1e3a8a;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .container {
            max-width: 750px;
            margin: 30px auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px #00000015;
        }

        .logo {
            width: 120px;
            height: 120px;
            object-fit: contain;
            display: block;
            margin: 0 auto 15px;
        }

        h2 {
            text-align: center;
            color: #1e3a8a;
        }

        .identitas {
            text-align: center;
            margin-bottom: 25px;
        }

        .data {
            border-top: 1px solid #e2e8f0;
            padding: 12px 0;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .label {
            width: 180px;
            font-weight: bold;
        }

        .tombol {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 16px;
            background: #1e3a8a;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .tombol:hover {
            background: #1d4ed8;
        }
    </style>
</head>

<body>

<header>
    <h1>Profil Sekolah</h1>
    <p>Informasi Data Sekolah</p>
</header>

<div class="container">

    @if ($sekolah->profil?->logo_path)
        <img
            src="{{ asset('storage/' . $sekolah->profil->logo_path) }}"
            alt="Logo {{ $sekolah->nama }}"
            class="logo"
        >
    @endif

    <div class="identitas">
        <h2>{{ $sekolah->nama }}</h2>
        <p>NPSN: {{ $sekolah->npsn }}</p>
    </div>

    <div class="data">
        <div class="label">Jenjang</div>
        <div>{{ $sekolah->jenjang ?: '-' }}</div>
    </div>

    <div class="data">
        <div class="label">Status</div>
        <div>{{ $sekolah->status ?: '-' }}</div>
    </div>

    <div class="data">
        <div class="label">Alamat</div>
        <div>{{ $sekolah->alamat ?: '-' }}</div>
    </div>

    <div class="data">
        <div class="label">Kecamatan</div>
        <div>{{ $sekolah->kecamatan ?: '-' }}</div>
    </div>

    <div class="data">
        <div class="label">Telepon</div>
        <div>{{ $sekolah->telepon ?: '-' }}</div>
    </div>

    <div class="data">
        <div class="label">Kepala Sekolah</div>
        <div>{{ $sekolah->profil?->kepala_sekolah ?: '-' }}</div>
    </div>

    <div class="data">
        <div class="label">Akreditasi</div>
        <div>{{ $sekolah->profil?->akreditasi ?: '-' }}</div>
    </div>

    <div class="data">
        <div class="label">Jumlah Siswa</div>
        <div>{{ $sekolah->profil?->jumlah_siswa ?? '-' }}</div>
    </div>

    <div class="data">
        <div class="label">Jumlah Guru</div>
        <div>{{ $sekolah->profil?->jumlah_guru ?? '-' }}</div>
    </div>

    <a href="{{ route('peta.index') }}" class="tombol">
        Kembali ke Peta
    </a>

</div>

</body>
</html>

@php
    $dataSekolah = $sekolah ?? null;
@endphp

@if ($errors->any())
    <div style="color: red;">
        <strong>Periksa kembali data berikut:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div>
    <label for="npsn">NPSN</label><br>
    <input
        id="npsn"
        type="text"
        name="npsn"
        value="{{ old('npsn', $dataSekolah->npsn ?? '') }}"
        maxlength="8"
        @readonly(auth()->user()->isSekolah())
        required
    >
</div>
<br>

<div>
    <label for="nama">Nama Sekolah</label><br>
    <input
        id="nama"
        type="text"
        name="nama"
        value="{{ old('nama', $dataSekolah->nama ?? '') }}"
        required
    >
</div>
<br>

<div>
    <label for="jenjang">Jenjang</label><br>
    <select id="jenjang" name="jenjang" required>
        <option value="">-- Pilih Jenjang --</option>
        @foreach (['SD', 'SMP', 'SMA', 'SMK'] as $jenjang)
            <option value="{{ $jenjang }}"
                @selected(old('jenjang', $dataSekolah->jenjang ?? '') == $jenjang)>
                {{ $jenjang }}
            </option>
        @endforeach
    </select>
</div>
<br>

<div>
    <label for="status">Status</label><br>
    <select id="status" name="status" required>
        <option value="">-- Pilih Status --</option>
        @foreach (['Negeri', 'Swasta'] as $status)
            <option value="{{ $status }}"
                @selected(old('status', $dataSekolah->status ?? '') == $status)>
                {{ $status }}
            </option>
        @endforeach
    </select>
</div>
<br>

<div>
    <label for="alamat">Alamat</label><br>
    <textarea id="alamat" name="alamat" required>{{ old('alamat', $dataSekolah->alamat ?? '') }}</textarea>
</div>
<br>

<div>
    <label for="kecamatan">Kecamatan</label><br>
    <input
        id="kecamatan"
        type="text"
        name="kecamatan"
        value="{{ old('kecamatan', $dataSekolah->kecamatan ?? '') }}"
        required
    >
</div>
<br>

<div>
    <label for="telepon">Telepon</label><br>
    <input
        id="telepon"
        type="text"
        name="telepon"
        value="{{ old('telepon', $dataSekolah->telepon ?? '') }}"
    >
</div>
<br>

<div>
    <label for="latitude">Latitude</label><br>
    <input
        id="latitude"
        type="number"
        step="any"
        name="latitude"
        value="{{ old('latitude', $dataSekolah->latitude ?? '') }}"
        required
    >
</div>
<br>

<div>
    <label for="longitude">Longitude</label><br>
    <input
        id="longitude"
        type="number"
        step="any"
        name="longitude"
        value="{{ old('longitude', $dataSekolah->longitude ?? '') }}"
        required
    >
</div>
<br>

<button type="button" id="gunakan-lokasi">
    Gunakan lokasi saya
</button>

<p id="status-lokasi" role="status"></p>

<p>Klik peta atau geser marker untuk menentukan lokasi sekolah.</p>

<div id="map" style="height: 350px; width: 100%;"></div>

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const statusLokasi = document.getElementById('status-lokasi');
    const tombolLokasi = document.getElementById('gunakan-lokasi');

    const defaultLat = -3.3194;
    const defaultLng = 114.5908;

    const lat = parseFloat(latInput.value) || defaultLat;
    const lng = parseFloat(lngInput.value) || defaultLng;

    const map = L.map('map').setView([lat, lng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const marker = L.marker([lat, lng], {
        draggable: true
    }).addTo(map);

    function updateCoordinates(position) {
        latInput.value = position.lat.toFixed(7);
        lngInput.value = position.lng.toFixed(7);
    }

    function tampilkanLokasi(position) {
        const lokasi = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
        };

        marker.setLatLng(lokasi);
        map.setView(lokasi, 17);
        updateCoordinates(lokasi);
        statusLokasi.textContent = 'Koordinat lokasi berhasil diisi.';
    }

    tombolLokasi.addEventListener('click', function () {
        if (!navigator.geolocation) {
            statusLokasi.textContent =
                'Browser tidak mendukung fitur lokasi.';
            return;
        }

        statusLokasi.textContent = 'Sedang mengambil lokasi...';

        navigator.geolocation.getCurrentPosition(
            tampilkanLokasi,
            function () {
                statusLokasi.textContent =
                    'Lokasi gagal diambil. Periksa izin lokasi browser.';
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    });

    map.on('click', function (event) {
        marker.setLatLng(event.latlng);
        updateCoordinates(event.latlng);
    });

    marker.on('dragend', function () {
        updateCoordinates(marker.getLatLng());
    });

    function pindahKeKoordinatInput() {
        const newLat = parseFloat(latInput.value);
        const newLng = parseFloat(lngInput.value);

        if (
            Number.isFinite(newLat) &&
            Number.isFinite(newLng) &&
            newLat >= -90 && newLat <= 90 &&
            newLng >= -180 && newLng <= 180
        ) {
            const lokasi = { lat: newLat, lng: newLng };
            marker.setLatLng(lokasi);
            map.setView(lokasi, 17);
        }
    }

    latInput.addEventListener('change', pindahKeKoordinatInput);
    lngInput.addEventListener('change', pindahKeKoordinatInput);

    setTimeout(function () {
        map.invalidateSize();
    }, 200);
});
</script>

<br>

<button type="submit">Simpan Data</button>

<a href="{{ auth()->user()->isDinas()
    ? route('dinas.sekolah.index')
    : route('dashboard') }}">
    Batal
</a>

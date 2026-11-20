<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Peta Sekolah</title>

    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <link rel="stylesheet"
          href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">

    <link rel="stylesheet"
          href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css">

    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: system-ui, sans-serif;
        }

        header {
            min-height: 56px;
            box-sizing: border-box;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            background: #1e3a8a;
            color: #fff;
        }

        header h1 {
            font-size: 18px;
            margin: 0;
        }

        header a {
            color: #bfdbfe;
            margin-left: auto;
        }

        #map {
            height: calc(100% - 56px);
            min-height: 400px;
        }

        select, input {
            padding: 6px;
        }

        .legenda {
            background: white;
            padding: 8px 10px;
            line-height: 1.7;
            border-radius: 5px;
            box-shadow: 0 1px 5px #999;
        }

        .titik-legenda {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 6px;
            border: 1px solid #fff;
        }

        .logo-popup {
            width: 90px;
            height: 90px;
            object-fit: contain;
            display: block;
            margin: 0 auto 10px;
        }

        .tombol-profil {
            display: inline-block;
            margin-top: 10px;
            padding: 7px 12px;
            background: #1e3a8a;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
        }

        .tombol-profil:hover {
            background: #1d4ed8;
        }
    </style>
</head>

<body>
    <header>
        <h1>Peta Sekolah</h1>

        <select id="filter-jenjang">
            <option value="">Semua jenjang</option>
            <option value="SD">SD</option>
            <option value="SMP">SMP</option>
            <option value="SMA">SMA</option>
            <option value="SMK">SMK</option>
        </select>

        <select id="filter-status">
            <option value="">Semua status</option>
            <option value="Negeri">Negeri</option>
            <option value="Swasta">Swasta</option>
        </select>

        <input
            type="search"
            id="cari-sekolah"
            placeholder="Cari nama sekolah..."
            autocomplete="off"
        >

        <span id="jumlah">0 sekolah</span>

        @auth
            <a href="{{ route('dashboard') }}">Dashboard</a>
        @else
            <a href="{{ route('login') }}">Masuk</a>
        @endauth
    </header>

    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

    <script>
        const map = L.map('map').setView([-3.3194, 114.5908], 12);

        // Peta dasar OpenStreetMap.
        const osm = L.tileLayer(
            'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
            {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }
        );

        // Peta dasar alternatif.
        const osmHumanitarian = L.tileLayer(
            'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png',
            {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors, Tiles style by Humanitarian OpenStreetMap Team'
            }
        );

        osm.addTo(map);

        L.control.layers({
            'OpenStreetMap': osm,
            'OpenStreetMap Humanitarian': osmHumanitarian
        }).addTo(map);

        // Mengamankan teks sebelum ditampilkan pada popup.
        function esc(teks) {
            const d = document.createElement('div');
            d.textContent = teks ?? '';
            return d.innerHTML;
        }

        // URL dasar untuk halaman profil publik.
        const profilUrl = @json(route('peta.sekolah.profil', ['sekolah' => '__ID__']));

        // Warna marker berdasarkan jenjang sekolah.
        function ikonJenjang(jenjang) {
            const warna = {
                SD: '#16a34a',
                SMP: '#2563eb',
                SMA: '#ea580c',
                SMK: '#9333ea'
            };

            return L.divIcon({
                className: '',
                html: `
                    <span style="
                        display:block;
                        width:18px;
                        height:18px;
                        border:2px solid white;
                        border-radius:50%;
                        background:${warna[jenjang] || '#64748b'};
                        box-shadow:0 1px 4px #555;
                    "></span>
                `,
                iconSize: [22, 22],
                iconAnchor: [11, 11]
            });
        }

        // Layer data sekolah.
        const lapisan = L.geoJSON(null, {
            pointToLayer: (fitur, latlng) => {
                return L.marker(latlng, {
                    icon: ikonJenjang(fitur.properties.jenjang)
                });
            },

            onEachFeature: (fitur, marker) => {
                const p = fitur.properties;
                const [lng, lat] = fitur.geometry.coordinates;

                // Menampilkan logo sekolah jika tersedia.
                const logo = p.logo_url
                    ? `
                        <div style="text-align:center; margin-bottom:10px;">
                            <img
                                src="${esc(p.logo_url)}"
                                alt="Logo ${esc(p.nama)}"
                                class="logo-popup"
                            >
                        </div>
                    `
                    : '';

                // Membuat alamat halaman profil berdasarkan ID sekolah.
                const linkProfil = profilUrl.replace(
                    '__ID__',
                    encodeURIComponent(p.id)
                );

                // Popup informasi sekolah.
                marker.bindPopup(`
                    ${logo}

                    <strong>${esc(p.nama)}</strong><br>
                    NPSN: ${esc(p.npsn)}<br>
                    ${esc(p.jenjang)} - ${esc(p.status)}<br>
                    ${esc(p.alamat)}, ${esc(p.kecamatan)}<br>
                    Telp: ${esc(p.telepon) || '-'}<br>

                    <hr>

                    <strong>Profil Sekolah</strong><br>
                    Kepala sekolah: ${esc(p.kepala_sekolah) || '-'}<br>
                    Akreditasi: ${esc(p.akreditasi) || '-'}<br>

                    Siswa: ${p.jumlah_siswa ?? '-'} |
                    Guru: ${p.jumlah_guru ?? '-'}<br>

                    <small>Koordinat: ${lat}, ${lng}</small><br>

                    <a href="${linkProfil}" class="tombol-profil">
                        Lihat Profil Lengkap
                    </a>
                `);
            }
        });

        // Mengelompokkan marker yang berdekatan.
        const kelompokMarker = L.markerClusterGroup();
        map.addLayer(kelompokMarker);

        // Legenda warna marker.
        const legenda = L.control({ position: 'bottomright' });

        legenda.onAdd = function () {
            const div = L.DomUtil.create('div', 'legenda');

            div.innerHTML = `
                <strong>Jenjang Sekolah</strong><br>
                <span class="titik-legenda" style="background:#16a34a"></span>SD<br>
                <span class="titik-legenda" style="background:#2563eb"></span>SMP<br>
                <span class="titik-legenda" style="background:#ea580c"></span>SMA<br>
                <span class="titik-legenda" style="background:#9333ea"></span>SMK
            `;

            return div;
        };

        legenda.addTo(map);

        // Memuat data sekolah dari server.
        async function muatData() {
            const jenjang = document.getElementById('filter-jenjang').value;
            const status = document.getElementById('filter-status').value;

            const parameter = new URLSearchParams();

            if (jenjang) parameter.set('jenjang', jenjang);
            if (status) parameter.set('status', status);

            const url = `{{ route('peta.data') }}?${parameter.toString()}`;
            const respon = await fetch(url);

            if (!respon.ok) {
                alert('Data sekolah gagal dimuat.');
                return;
            }

            const data = await respon.json();

            lapisan.clearLayers();
            kelompokMarker.clearLayers();

            lapisan.addData(data);

            lapisan.eachLayer((marker) => {
                kelompokMarker.addLayer(marker);
            });

            document.getElementById('jumlah').textContent =
                data.features.length + ' sekolah';

            if (kelompokMarker.getLayers().length > 0) {
                map.fitBounds(kelompokMarker.getBounds().pad(0.1));
            }
        }

        // Pencarian sekolah berdasarkan nama.
        function cariSekolah() {
            const kataKunci = document
                .getElementById('cari-sekolah')
                .value
                .trim()
                .toLowerCase();

            if (!kataKunci) return;

            let ditemukan = false;

            lapisan.eachLayer((marker) => {
                const nama = String(marker.feature?.properties?.nama ?? '')
                    .toLowerCase();

                if (nama.includes(kataKunci)) {
                    ditemukan = true;

                    kelompokMarker.zoomToShowLayer(marker, function () {
                        marker.openPopup();
                    });
                }
            });

            if (!ditemukan) {
                alert('Sekolah tidak ditemukan pada data yang sedang ditampilkan.');
            }
        }

        // Event filter jenjang.
        document.getElementById('filter-jenjang')
            .addEventListener('change', muatData);

        // Event filter status.
        document.getElementById('filter-status')
            .addEventListener('change', muatData);

        // Event pencarian.
        document.getElementById('cari-sekolah')
            .addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    cariSekolah();
                }
            });

        // Memuat data pertama kali.
        muatData();
    </script>
</body>
</html>

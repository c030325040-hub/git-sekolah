@extends('layouts.public')

@section('title', 'Cek Lokasi')

@section('content')
<div class="toolbar">
    Klik di mana saja pada peta untuk melihat alamat dan sekolah terdekat.
</div>

<div id="peta" class="peta"></div>

<div id="hasil" class="panel"
     style="top: 64px; right: 12px; width: 320px; max-height: 70%;
            overflow: auto; display: none;"></div>
@endsection

@push('scripts')
<script>
    const map = Peta.buat('peta');
    const lapisan = L.layerGroup().addTo(map);
    const panel = document.getElementById('hasil');

    let semua = [];
    let terakhir = 0;

    // Mengambil data sekolah
    Peta.ambilSekolah().then((fitur) => {
        semua = fitur;
    }).catch((error) => {
        console.error('Gagal mengambil data sekolah:', error);
    });

    // Menghitung n sekolah terdekat dari titik [lat, lng]
    function terdekat(titik, n = 3) {
        return semua
            .map((f) => ({
                fitur: f,
                jarak: Peta.jarakKm(titik, Peta.latlng(f))
            }))
            .sort((a, b) => a.jarak - b.jarak)
            .slice(0, n);
    }

    // Mengambil alamat berdasarkan koordinat
    async function ambilAlamat(lat, lng) {
        const url = 'https://nominatim.openstreetmap.org/reverse'
            + '?format=jsonv2&addressdetails=1&accept-language=id'
            + `&lat=${lat}&lon=${lng}`;

        const respon = await fetch(url);

        if (!respon.ok) {
            throw new Error('HTTP ' + respon.status);
        }

        return respon.json();
    }

    // Ketika pengguna mengeklik peta
    map.on('click', async (e) => {
        // Kebijakan Nominatim: maksimal 1 permintaan per detik
        const sekarang = Date.now();

        if (sekarang - terakhir < 1200) return;

        terakhir = sekarang;

        const { lat, lng } = e.latlng;
        const titik = [lat, lng];

        // Menghapus lapisan sebelumnya
        lapisan.clearLayers();

        // Menampilkan titik yang diklik
        L.circleMarker(titik, {
            radius: 9,
            color: '#fff',
            weight: 3,
            fillColor: '#ef4444',
            fillOpacity: 1,
        }).addTo(lapisan);

        // Menghitung tiga sekolah terdekat
        const dekat = terdekat(titik);

        // Menggambar garis menuju sekolah terdekat
        dekat.forEach((d) => {
            L.polyline([titik, Peta.latlng(d.fitur)], {
                color: '#2563eb',
                weight: 2,
                dashArray: '6 6',
            }).addTo(lapisan);
        });

        panel.style.display = 'block';
        panel.textContent = 'Mencari alamat...';

        let bagianAlamat = '<em>Alamat tidak dapat dimuat.</em>';

        try {
            const data = await ambilAlamat(lat, lng);
            const a = data.address || {};

            bagianAlamat = `
                <strong>${Peta.esc(data.display_name || 'Alamat tidak ditemukan')}</strong>
            `;
        } catch (error) {
            console.error('Gagal mengambil alamat:', error);
        }

        // Menampilkan sekolah terdekat
        const daftarSekolah = dekat.length
            ? dekat.map((d, i) => {
                const properti = d.fitur.properties || {};
                const nama = properti.nama || properti.nama_sekolah || 'Sekolah tanpa nama';

                return `
                    <li>
                        <strong>${Peta.esc(nama)}</strong><br>
                        Jarak: ${d.jarak.toFixed(2)} km
                    </li>
                `;
            }).join('')
            : '<li>Data sekolah tidak ditemukan.</li>';

        panel.innerHTML = `
            <h3>Hasil Cek Lokasi</h3>

            <p><strong>Koordinat:</strong><br>
            ${lat.toFixed(6)}, ${lng.toFixed(6)}</p>

            <p><strong>Alamat:</strong><br>
            ${bagianAlamat}</p>

            <hr>

            <strong>3 Sekolah Terdekat:</strong>
            <ol>
                ${daftarSekolah}
            </ol>
        `;
    });
</script>
@endpush

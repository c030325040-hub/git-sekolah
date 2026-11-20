@extends('layouts.public')

@section('title', 'Wilayah Sekolah')

@section('content')
<div class="toolbar">
    <label>Jenjang
        <select id="jenjang">
            <option value="">Semua jenjang</option>
            <option value="SD">SD</option>
            <option value="SMP">SMP</option>
            <option value="SMA">SMA</option>
            <option value="SMK">SMK</option>
        </select>
    </label>

    <span id="jumlah"></span>
</div>

<div id="peta" class="peta"></div>
@endsection

@push('scripts')
<script>
    const map = Peta.buat('peta');

    let layerWilayah = null;

    const warna = [
        '#2563eb',
        '#16a34a',
        '#f59e0b',
        '#dc2626',
        '#9333ea'
    ];

    async function muatWilayah() {
        const jenjang = document.getElementById('jenjang').value;

        const url = new URL(
            window.PETA_URL.wilayah,
            window.location.origin
        );

        if (jenjang) {
            url.searchParams.set('jenjang', jenjang);
        }

        try {
            const response = await fetch(url);

            if (!response.ok) {
                throw new Error('Gagal mengambil data wilayah');
            }

            const geojson = await response.json();

            if (layerWilayah) {
                map.removeLayer(layerWilayah);
            }

            layerWilayah = L.geoJSON(geojson, {
                style: function (feature) {
                    const index = warna.indexOf(
                        warna[feature.properties.urutan]
                    );

                    return {
                        color: warna[index >= 0 ? index : 0],
                        weight: 2,
                        fillColor: warna[index >= 0 ? index : 0],
                        fillOpacity: 0.35
                    };
                },

                onEachFeature: function (feature, layer) {
                    const p = feature.properties;

                    layer.bindPopup(`
                        <strong>${p.kecamatan}</strong><br>
                        Jumlah sekolah: ${p.jumlah_sekolah}<br>
                        Jumlah siswa: ${p.jumlah_siswa}
                    `);
                }
            }).addTo(map);

            document.getElementById('jumlah').textContent =
                geojson.features.length + ' kecamatan';

            if (geojson.features.length > 0) {
                map.fitBounds(layerWilayah.getBounds().pad(0.1));
            }
        } catch (error) {
            console.error(error);

            document.getElementById('jumlah').textContent =
                'Gagal memuat data wilayah';
        }
    }

    document.getElementById('jenjang')
        .addEventListener('change', muatWilayah);

    muatWilayah();
</script>
@endpush

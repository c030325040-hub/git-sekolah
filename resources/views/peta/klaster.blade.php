@extends('layouts.public')

@section('title', 'Klaster Sekolah')

@push('styles')
<link rel="stylesheet"
      href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">

<link rel="stylesheet"
      href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css">
@endpush

@section('content')
<div class="toolbar">
    <label>Jenjang
        <select id="filter-jenjang">
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
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<script>
    const map = Peta.buat('peta');

    const klaster = L.markerClusterGroup();

    map.addLayer(klaster);

    async function muat(jenjang = '') {
        try {
            const fitur = await Peta.ambilSekolah(jenjang);

            klaster.clearLayers();

            fitur.forEach((f) => {
                const marker = L.marker(Peta.latlng(f))
                    .bindPopup(Peta.popup(f));

                klaster.addLayer(marker);
            });

            document.getElementById('jumlah').textContent =
                fitur.length + ' sekolah';

            if (fitur.length > 0) {
                map.fitBounds(klaster.getBounds().pad(0.1));
            }
        } catch (error) {
            console.error(error);

            document.getElementById('jumlah').textContent =
                'Gagal memuat data';
        }
    }

    document.getElementById('filter-jenjang')
        .addEventListener('change', (e) => {
            muat(e.target.value);
        });

    muat();
</script>
@endpush

@extends('layouts.public')

@section('title', 'Peta Sekolah')

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

    <button type="button" id="btn-unduh">
        Unduh GeoJSON
    </button>

    <span id="jumlah"></span>
</div>

<div id="peta" class="peta"></div>
@endsection

@push('scripts')
<script>
    const map = Peta.buat('peta');
    const lapisan = L.layerGroup().addTo(map);

    async function muat(jenjang = '') {
        try {
            const fitur = await Peta.ambilSekolah(jenjang);

            lapisan.clearLayers();

            fitur.forEach((f) => {
                L.marker(Peta.latlng(f))
                    .bindPopup(Peta.popup(f))
                    .addTo(lapisan);
            });

            document.getElementById('jumlah').textContent =
                fitur.length + ' sekolah';

            if (fitur.length > 0) {
                map.fitBounds(
                    L.latLngBounds(fitur.map(Peta.latlng)).pad(0.1)
                );
            }
        } catch (e) {
            console.error(e);

            document.getElementById('jumlah').textContent =
                'Gagal memuat data';
        }
    }

    document.getElementById('filter-jenjang')
        .addEventListener('change', (e) => muat(e.target.value));

    // Mengunduh data sekolah dalam format GeoJSON
    document.getElementById('btn-unduh')
        .addEventListener('click', async () => {
            try {
                const jenjang = document.getElementById('filter-jenjang').value;

                const url = new URL(
                    window.PETA_URL.data,
                    window.location.origin
                );

                if (jenjang) {
                    url.searchParams.set('jenjang', jenjang);
                }

                const response = await fetch(url);

                if (!response.ok) {
                    throw new Error('Gagal mengambil data GeoJSON');
                }

                const data = await response.json();

                const file = new Blob(
                    [JSON.stringify(data, null, 2)],
                    { type: 'application/geo+json' }
                );

                const link = document.createElement('a');
                const alamatFile = URL.createObjectURL(file);

                link.href = alamatFile;
                link.download = 'data-sekolah.geojson';

                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                URL.revokeObjectURL(alamatFile);

            } catch (error) {
                console.error(error);
                alert('Gagal mengunduh GeoJSON. Silakan coba lagi.');
            }
        });

    muat();
</script>
@endpush

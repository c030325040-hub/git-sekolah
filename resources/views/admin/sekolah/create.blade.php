
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Sekolah - SIG Sekolah</title>
</head>
<body>
    <h1>Tambah Data Sekolah</h1>

    <form action="{{ route('dinas.sekolah.store') }}" method="POST">
        @csrf

        @include('admin.sekolah._form')

        <button type="button" id="gunakan-lokasi">
            Gunakan lokasi saya
        </button>

        <p id="status-lokasi" role="status"></p>
    </form>

    <script>
        document.getElementById('gunakan-lokasi').addEventListener('click', function () {
            const status = document.getElementById('status-lokasi');

            if (!navigator.geolocation) {
                status.textContent = 'Browser tidak mendukung fitur lokasi.';
                return;
            }

            status.textContent = 'Sedang mengambil lokasi...';

            navigator.geolocation.getCurrentPosition(
                function (posisi) {
                    const latitude = document.getElementById('latitude');
                    const longitude = document.getElementById('longitude');

                    if (!latitude || !longitude) {
                        status.textContent =
                            'Input latitude atau longitude tidak ditemukan di form.';
                        return;
                    }

                    latitude.value = posisi.coords.latitude;
                    longitude.value = posisi.coords.longitude;

                    status.textContent = 'Koordinat berhasil diisi.';
                },
                function () {
                    status.textContent =
                        'Lokasi gagal diambil. Periksa izin lokasi browser dan coba lagi.';
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });
    </script>
</body>
</html>

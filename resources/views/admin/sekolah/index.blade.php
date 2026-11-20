<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Sekolah - SIG Sekolah</title>
</head>
<body>
    <h1>Sistem Informasi Geografis Sekolah</h1>
    <h2>Daftar Data Sekolah</h2>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <p>
        <a href="{{ route('dinas.sekolah.create') }}">+ Tambah Sekolah</a>
    </p>

    <form action="{{ route('dinas.sekolah.index') }}" method="GET">
        <input
            type="text"
            name="q"
            placeholder="Cari nama, NPSN, atau kecamatan"
            value="{{ request('q') }}"
        >
        <button type="submit">Cari</button>
        <a href="{{ route('dinas.sekolah.index') }}">Reset</a>
    </form>

    <br>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>No.</th>
                <th>NPSN</th>
                <th>Nama Sekolah</th>
                <th>Jenjang</th>
                <th>Status</th>
                <th>Kecamatan</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sekolahs as $item)
                <tr>
                    <td>{{ $loop->iteration + ($sekolahs->currentPage() - 1) * $sekolahs->perPage() }}</td>
                    <td>{{ $item->npsn }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->jenjang }}</td>
                    <td>{{ $item->status }}</td>
                    <td>{{ $item->kecamatan }}</td>
                    <td>{{ $item->latitude }}</td>
                    <td>{{ $item->longitude }}</td>
                    <td>
                        <a href="{{ route('sekolah.edit', $item) }}">Edit</a>

                        <form
                            action="{{ route('dinas.sekolah.destroy', $item) }}"
                            method="POST"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                onclick="return confirm('Yakin ingin menghapus data ini?')"
                            >
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">Data sekolah belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <br>
    {{ $sekolahs->links() }}
</body>
</html>

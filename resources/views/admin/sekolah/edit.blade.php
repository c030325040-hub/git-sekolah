<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Sekolah - SIG Sekolah</title>
</head>
<body>
    <h1>Edit Data Sekolah</h1>

    {{-- Tab Data Sekolah dan Profil Sekolah --}}
    @include('sekolah._tab', ['sekolah' => $sekolah])

    <form action="{{ route('sekolah.update', $sekolah) }}" method="POST">
        @csrf
        @method('PUT')

        @include('admin.sekolah._form')
    </form>
</body>
</html>

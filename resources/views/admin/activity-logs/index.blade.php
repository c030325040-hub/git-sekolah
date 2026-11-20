@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold">Log Aktivitas</h1>
    <p class="text-gray-600 mt-1">
        Riwayat perubahan data sekolah oleh pengguna.
    </p>
</div>

<div class="bg-white rounded shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-200 text-left">
            <tr>
                <th class="p-3">No.</th>
                <th class="p-3">Pengguna</th>
                <th class="p-3">Sekolah</th>
                <th class="p-3">Aktivitas</th>
                <th class="p-3">Perubahan Data</th>
                <th class="p-3">Waktu</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($logs as $log)
                <tr class="border-t align-top">
                    <td class="p-3">
                        {{ $logs->firstItem() + $loop->index }}
                    </td>

                    <td class="p-3">
                        {{ $log->user?->name ?? 'Pengguna dihapus' }}
                    </td>

                    <td class="p-3">
                        {{ $log->sekolah?->nama ?? 'Sekolah tidak tersedia' }}
                    </td>

                    <td class="p-3">
                        {{ $log->aksi }}
                    </td>

                    <td class="p-3">
                        @if ($log->perubahan)
                            @foreach ($log->perubahan['sebelum'] ?? [] as $kolom => $nilaiLama)
                                <div class="mb-2">
                                    <strong>{{ $kolom }}</strong>
                                    <br>
                                    <span class="text-red-600">
                                        Sebelum: {{ $nilaiLama ?? '-' }}
                                    </span>
                                    <br>
                                    <span class="text-green-700">
                                        Sesudah:
                                        {{ $log->perubahan['sesudah'][$kolom] ?? '-' }}
                                    </span>
                                </div>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>

                    <td class="p-3">
                        {{ $log->created_at?->format('d-m-Y H:i:s') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-4 text-center">
                        Belum ada aktivitas yang tercatat.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $logs->links() }}
</div>
@endsection

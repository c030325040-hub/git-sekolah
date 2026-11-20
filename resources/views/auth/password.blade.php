@extends('layouts.app')

@section('title', 'Ubah Kata Sandi')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-6">Ubah Kata Sandi</h1>

    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="current_password" class="block font-medium mb-1">
                Kata Sandi Lama
            </label>
            <input type="password" id="current_password"
                   name="current_password"
                   class="w-full border rounded px-3 py-2" required>
            @error('current_password')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="block font-medium mb-1">
                Kata Sandi Baru
            </label>
            <input type="password" id="password" name="password"
                   class="w-full border rounded px-3 py-2" required>
            @error('password')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="password_confirmation" class="block font-medium mb-1">
                Konfirmasi Kata Sandi Baru
            </label>
            <input type="password" id="password_confirmation"
                   name="password_confirmation"
                   class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="bg-blue-700 text-white px-5 py-2 rounded">
                Simpan
            </button>

            <a href="{{ route('dashboard') }}"
               class="bg-gray-300 px-5 py-2 rounded">
                Kembali
            </a>
        </div>
    </form>
</div>
@endsection

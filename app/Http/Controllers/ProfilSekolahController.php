<?php

namespace App\Http\Controllers;

use App\Models\ProfilSekolah;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfilSekolahController extends Controller
{
    public function edit(Sekolah $sekolah)
    {
        Gate::authorize('update', $sekolah);

        $profil = $sekolah->profil ?? new ProfilSekolah();

        return view('sekolah.profil', compact('sekolah', 'profil'));
    }

    public function update(Request $request, Sekolah $sekolah)
    {
        Gate::authorize('update', $sekolah);

        $data = $request->validate([
            'kepala_sekolah' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'website' => ['nullable', 'url', 'max:255'],
            'tahun_berdiri' => [
                'nullable',
                'integer',
                'between:1800,' . date('Y')
            ],
            'akreditasi' => [
                'nullable',
                Rule::in(['A', 'B', 'C', 'Belum Terakreditasi'])
            ],
            'jumlah_siswa' => ['nullable', 'integer', 'min:0'],
            'jumlah_guru' => ['nullable', 'integer', 'min:0'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],

            // Validasi logo sekolah
            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ], [
            'logo.image' => 'File logo harus berupa gambar.',
            'logo.mimes' => 'Logo harus berformat JPG, JPEG, PNG, atau WEBP.',
            'logo.max' => 'Ukuran logo maksimal 2 MB.',
        ]);

        // Ambil profil yang sudah ada
        $profil = $sekolah->profil ?? new ProfilSekolah();

        // Upload logo baru jika ada
        if ($request->hasFile('logo')) {
            $logoBaru = $request->file('logo')
                ->store('logos/sekolah', 'public');

            // Hapus logo lama setelah logo baru berhasil disimpan
            if ($profil->logo_path) {
                Storage::disk('public')->delete($profil->logo_path);
            }

            $data['logo_path'] = $logoBaru;
        }

        unset($data['logo']);

        $sekolah->profil()->updateOrCreate([], $data);

        return back()->with('success', 'Profil sekolah berhasil disimpan.');
    }
}

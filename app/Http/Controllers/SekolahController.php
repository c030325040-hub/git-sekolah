<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SekolahController extends Controller
{
    // Batas maksimal perubahan koordinat: 100 meter
    private const BATAS_JARAK_METER = 100;

    public function index(Request $request)
    {
        $sekolahs = Sekolah::query()
            ->when($request->q, function ($query, $q) {
                $query->where(function ($w) use ($q) {
                    $w->whereLike('nama', "%{$q}%")
                        ->orWhereLike('npsn', "%{$q}%")
                        ->orWhereLike('kecamatan', "%{$q}%");
                });
            })
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('admin.sekolah.index', compact('sekolahs'));
    }

    public function create()
    {
        return view('admin.sekolah.create');
    }

    public function store(Request $request)
    {
        Sekolah::create($request->validate($this->rules()));

        return redirect()->route('dinas.sekolah.index')
            ->with('success', 'Data sekolah berhasil ditambahkan.');
    }

    public function edit(Sekolah $sekolah)
    {
        Gate::authorize('update', $sekolah);

        return view('admin.sekolah.edit', compact('sekolah'));
    }

    public function update(Request $request, Sekolah $sekolah)
    {
        Gate::authorize('update', $sekolah);

        $rules = $this->rules($sekolah->id);

        // Sekolah tidak boleh mengubah NPSN
        if ($request->user()->isSekolah()) {
            unset($rules['npsn']);
        }

        $data = $request->validate($rules);

        if ($request->user()->isSekolah()) {
            // NPSN tetap menggunakan data lama
            unset($data['npsn']);

            // Periksa jarak perubahan koordinat
            $jarak = $this->hitungJarak(
                (float) $sekolah->latitude,
                (float) $sekolah->longitude,
                (float) $data['latitude'],
                (float) $data['longitude']
            );

            if ($jarak > self::BATAS_JARAK_METER) {
                throw ValidationException::withMessages([
                    'latitude' => 'Perubahan lokasi sekolah tidak boleh lebih dari 100 meter dari titik semula.',
                ]);
            }
        }

        $sekolah->update($data);

        return $this->kembali($request)
            ->with('success', 'Data sekolah berhasil diperbarui.');
    }

    // Menghitung jarak dua koordinat menggunakan rumus Haversine
    private function hitungJarak(
        float $latAwal,
        float $lonAwal,
        float $latBaru,
        float $lonBaru
    ): float {
        $radiusBumi = 6371000;

        $dLat = deg2rad($latBaru - $latAwal);
        $dLon = deg2rad($lonBaru - $lonAwal);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($latAwal))
            * cos(deg2rad($latBaru))
            * sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $radiusBumi * $c;
    }

    public function destroy(Sekolah $sekolah)
    {
        $sekolah->delete();

        return redirect()->route('dinas.sekolah.index')
            ->with('success', 'Data sekolah berhasil dihapus.');
    }

    private function kembali(Request $request)
    {
        return $request->user()->isDinas()
            ? redirect()->route('dinas.sekolah.index')
            : redirect()->route('dashboard');
    }

    private function rules(?int $id = null): array
    {
        return [
            'npsn' => [
                'required',
                'digits:8',
                Rule::unique('sekolahs', 'npsn')->ignore($id),
            ],
            'nama' => ['required', 'string', 'max:255'],
            'jenjang' => ['required', Rule::in(['SD', 'SMP', 'SMA', 'SMK'])],
            'status' => ['required', Rule::in(['Negeri', 'Swasta'])],
            'alamat' => ['required', 'string'],
            'kecamatan' => ['required', 'string', 'max:100'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }
}

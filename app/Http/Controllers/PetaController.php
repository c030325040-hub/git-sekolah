<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Support\Geometri;
use Illuminate\Http\Request;

class PetaController extends Controller
{
    // Halaman utama peta sekolah
    public function index()
    {
        return view('peta.index');
    }

    // Halaman pencarian sekolah
    public function cari()
    {
        return view('peta.cari');
    }

    // Halaman klaster sekolah
    public function klaster()
    {
        return view('peta.klaster');
    }

    // Halaman peta panas sekolah
    public function panas()
    {
        return view('peta.panas');
    }

    // Halaman wilayah sekolah
    public function wilayah()
    {
        return view('peta.wilayah');
    }

    // Halaman rute kunjungan sekolah (Praktikum 25)
    public function rute()
    {
        return view('peta.rute');
    }

    // Halaman cek lokasi (Praktikum 26)
    public function alamat()
    {
        return view('peta.alamat');
    }

    // Mengambil data poligon wilayah sekolah
    public function geojsonWilayah(Request $request)
    {
        $features = Sekolah::with('profil')
            ->when($request->jenjang, fn ($q, $j) => $q->where('jenjang', $j))
            ->get()
            ->groupBy('kecamatan')
            ->map(function ($kelompok, $kecamatan) {
                $titik = $kelompok
                    ->map(fn ($s) => [$s->longitude, $s->latitude])
                    ->all();

                $hull = Geometri::convexHull($titik);

                // Poligon memerlukan minimal 3 titik
                if (count($hull) < 3) {
                    return null;
                }

                // Menutup cincin poligon GeoJSON
                $hull[] = $hull[0];

                return [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Polygon',
                        'coordinates' => [$hull],
                    ],
                    'properties' => [
                        'kecamatan' => $kecamatan,
                        'jumlah_sekolah' => $kelompok->count(),
                        'jumlah_siswa' => (int) $kelompok->sum(
                            fn ($s) => $s->profil?->jumlah_siswa ?? 0
                        ),
                    ],
                ];
            })
            ->filter()
            ->values();

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    // Mengambil data sekolah dalam format GeoJSON
    public function geojson(Request $request)
    {
        $features = Sekolah::with('profil')
            ->when($request->jenjang, function ($query, $jenjang) {
                $query->where('jenjang', $jenjang);
            })
            ->get()
            ->map(function ($sekolah) {
                return [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [
                            $sekolah->longitude,
                            $sekolah->latitude
                        ],
                    ],
                    'properties' => [
                        'npsn' => $sekolah->npsn,
                        'nama' => $sekolah->nama,
                        'jenjang' => $sekolah->jenjang,
                        'status' => $sekolah->status,
                        'alamat' => $sekolah->alamat,
                        'kecamatan' => $sekolah->kecamatan,
                        'telepon' => $sekolah->telepon,
                        'kepala_sekolah' => $sekolah->profil?->kepala_sekolah,
                        'akreditasi' => $sekolah->profil?->akreditasi,
                        'jumlah_siswa' => $sekolah->profil?->jumlah_siswa,
                        'jumlah_guru' => $sekolah->profil?->jumlah_guru,
                    ],
                ];
            });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    // Profil sekolah publik
    public function profil(Sekolah $sekolah)
    {
        $sekolah->load('profil');

        return view('peta.profil', compact('sekolah'));
    }
}

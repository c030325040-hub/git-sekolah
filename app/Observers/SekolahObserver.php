<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Sekolah;
use Illuminate\Support\Facades\Auth;

class SekolahObserver
{
    public function updating(Sekolah $sekolah): void
    {
        $dataLama = [];

        foreach (array_keys($sekolah->getDirty()) as $kolom) {
            $dataLama[$kolom] = $sekolah->getOriginal($kolom);
        }

        $sekolah->setRelation('activityLogOldValues', $dataLama);
    }

    public function updated(Sekolah $sekolah): void
    {
        $dataLama = $sekolah->getRelation('activityLogOldValues') ?? [];

        $dataBaru = $sekolah->getChanges();

        unset(
            $dataBaru['updated_at'],
            $dataBaru['created_at']
        );

        $sekolah->unsetRelation('activityLogOldValues');

        if (empty($dataBaru)) {
            return;
        }

        $dataLama = array_intersect_key($dataLama, $dataBaru);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'sekolah_id' => $sekolah->id,
            'aksi' => 'Memperbarui data sekolah',
            'perubahan' => [
                'sebelum' => $dataLama,
                'sesudah' => $dataBaru,
            ],
        ]);
    }
}

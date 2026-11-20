<?php

namespace App\Policies;

use App\Models\Sekolah;
use App\Models\User;

class SekolahPolicy
{
    public function update(User $user, Sekolah $sekolah): bool
    {
        // Dinas boleh mengubah semua sekolah
        if ($user->isDinas()) {
            return true;
        }

        // Sekolah hanya boleh mengubah sekolah miliknya sendiri
        return $user->isSekolah()
            && $user->sekolah_id !== null
            && (int) $user->sekolah_id === $sekolah->id;
    }
}

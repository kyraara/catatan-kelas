<?php

namespace App\Policies;

use App\Models\CatatanHarian;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CatatanHarianPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CatatanHarian $catatan): bool
    {
        return $user->hasRole('admin')
            || $user->id === $catatan->user_id // Guru hanya lihat data sendiri
            || (
                $user->hasRole('wali_kelas')
                && $catatan->jadwal
                && $catatan->jadwal->kelas
                && $catatan->jadwal->kelas->wali_kelas_id === $user->id
            );
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('guru');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CatatanHarian $catatan): bool
    {
        return $user->hasRole('admin') || $user->id === $catatan->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CatatanHarian $catatan): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CatatanHarian $catatan): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, CatatanHarian $catatan): bool
    {
        return $user->hasRole('admin');
    }

    public function approve(User $user, CatatanHarian $catatan): bool
    {
        return $user->hasRole('admin')
            || (
                $user->hasRole('wali_kelas')
                && $catatan->jadwal
                && $catatan->jadwal->kelas
                && $catatan->jadwal->kelas->wali_kelas_id === $user->id
            );
    }
}

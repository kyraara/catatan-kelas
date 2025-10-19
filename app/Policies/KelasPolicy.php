<?php

namespace App\Policies;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class KelasPolicy
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
    public function view(User $user, Kelas $kelas): bool
    {
        // Admin bisa lihat semua, wali_kelas hanya kelas yg dia ampu
        return $user->hasRole('admin')
            || ($user->hasRole('wali_kelas') && $kelas->wali_kelas_id === $user->id)
            || $user->hasRole('guru');
    }

    public function create(User $user): bool
    {
        // Hanya admin boleh create kelas baru
        return $user->hasRole('admin');
    }

    public function update(User $user, Kelas $kelas): bool
    {
        // Admin bisa update semua kelas, wali_kelas hanya kelas yg dia ampu
        return $user->hasRole('admin')
            || ($user->hasRole('wali_kelas') && $kelas->wali_kelas_id === $user->id);
    }

    public function delete(User $user, Kelas $kelas): bool
    {
        // Hanya admin
        return $user->hasRole('admin');
    }

    public function restore(User $user, Kelas $kelas): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Kelas $kelas): bool
    {
        return $user->hasRole('admin');
    }
}

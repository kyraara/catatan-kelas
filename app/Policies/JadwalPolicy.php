<?php

namespace App\Policies;

use App\Models\Jadwal;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JadwalPolicy
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
    public function view(User $user, Jadwal $jadwal): bool
    {
        return $user->hasRole('admin')
            || ($user->hasRole('guru') && $jadwal->guru_id === $user->id)
            || ($user->hasRole('wali_kelas') && $jadwal->kelas && $jadwal->kelas->wali_kelas_id === $user->id);
    }

    public function create(User $user): bool
    {
        // Hanya admin boleh buat jadwal baru.
        return $user->hasRole('admin');
    }

    public function update(User $user, Jadwal $jadwal): bool
    {
        // Admin bisa update semua, guru bisa update jadwal miliknya sendiri
        return $user->hasRole('admin')
            || ($user->hasRole('guru') && $jadwal->guru_id === $user->id);
    }

    public function delete(User $user, Jadwal $jadwal): bool
    {
        // Hanya admin
        return $user->hasRole('admin');
    }

    public function restore(User $user, Jadwal $jadwal): bool
    {
        // Hanya admin
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Jadwal $jadwal): bool
    {
        // Hanya admin
        return $user->hasRole('admin');
    }
}

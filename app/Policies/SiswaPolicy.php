<?php

namespace App\Policies;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SiswaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin, wali_kelas, dan guru boleh lihat daftar siswa
        return $user->hasRole('admin') || $user->hasRole('wali_kelas') || $user->hasRole('guru');
    }

    public function view(User $user, Siswa $siswa): bool
    {
        // Admin bisa lihat semua, wali_kelas bisa lihat siswa di kelas yang diampu
        return $user->hasRole('admin')
            || ($user->hasRole('wali_kelas')
                && $siswa->kelas
                && $siswa->kelas->wali_kelas_id === $user->id)
            || ($user->hasRole('guru'));
    }

    public function create(User $user): bool
    {
        // Hanya admin boleh create siswa baru
        return $user->hasRole('admin');
    }

    public function update(User $user, Siswa $siswa): bool
    {
        // Admin boleh update semua, wali_kelas boleh update siswa di kelas sendiri
        return $user->hasRole('admin')
            || ($user->hasRole('wali_kelas')
                && $siswa->kelas
                && $siswa->kelas->wali_kelas_id === $user->id);
    }

    public function delete(User $user, Siswa $siswa): bool
    {
        return $user->hasRole('admin');
    }

    public function restore(User $user, Siswa $siswa): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Siswa $siswa): bool
    {
        return $user->hasRole('admin');
    }
}

<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */

    /**
     * Determine whether the user can view the model.
     */
    public function viewAny(User $user): bool
    {
        // Semua admin boleh lihat daftar user, wali_kelas & guru tidak bisa
        return $user->hasRole('admin');
    }

    public function view(User $user, User $model): bool
    {
        // Admin bisa lihat semua, user bisa lihat data sendiri
        return $user->hasRole('admin') || $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        // Hanya admin boleh membuat user baru
        return $user->hasRole('admin');
    }

    public function update(User $user, User $model): bool
    {
        // Admin boleh, user boleh update data sendiri (misal ganti profil)
        return $user->hasRole('admin') || $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        // Hanya admin boleh delete user lain
        return $user->hasRole('admin');
    }

    public function restore(User $user, User $model): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->hasRole('admin');
    }
}

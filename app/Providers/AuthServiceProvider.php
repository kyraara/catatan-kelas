<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */

    protected $policies = [
        \App\Models\CatatanHarian::class => \App\Policies\CatatanHarianPolicy::class,
        \App\Models\Jadwal::class        => \App\Policies\JadwalPolicy::class,
        \App\Models\Kelas::class         => \App\Policies\KelasPolicy::class,
        \App\Models\Siswa::class         => \App\Policies\SiswaPolicy::class,
        \App\Models\User::class          => \App\Policies\UserPolicy::class,

    ];

    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}

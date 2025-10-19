<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;
use App\Models\MataPelajaran;

class StatsOverviewMasterData extends BaseWidget
{
    public static int $dashboardSort = 1;
    protected function getCards(): array
    {
        return [
            Stat::make('Siswa', Siswa::count())->icon('heroicon-o-academic-cap')->color('success'),
            Stat::make('Guru', User::role('guru')->count())->icon('heroicon-o-user-group'),
            Stat::make('Kelas', Kelas::count())->icon('heroicon-o-users'),
            Stat::make('Mata Pelajaran', MataPelajaran::count())->icon('heroicon-o-book-open'),
        ];
    }
}

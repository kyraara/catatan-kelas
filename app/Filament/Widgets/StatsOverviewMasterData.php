<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Support\Facades\Cache;

class StatsOverviewMasterData extends BaseWidget
{
    public static int $dashboardSort = 1;
    
    // Polling interval untuk auto-refresh (null = tidak auto refresh)
    protected static ?string $pollingInterval = null;
    
    protected function getCards(): array
    {
        // Cache stats selama 5 menit untuk menghindari query berulang
        $stats = Cache::remember('dashboard_stats', 300, function () {
            return [
                'siswa' => Siswa::count(),
                'guru' => User::role('guru')->count(),
                'kelas' => Kelas::count(),
                'mapel' => MataPelajaran::count(),
            ];
        });

        return [
            Stat::make('Siswa', $stats['siswa'])->icon('heroicon-o-academic-cap')->color('success'),
            Stat::make('Guru', $stats['guru'])->icon('heroicon-o-user-group'),
            Stat::make('Kelas', $stats['kelas'])->icon('heroicon-o-users'),
            Stat::make('Mata Pelajaran', $stats['mapel'])->icon('heroicon-o-book-open'),
        ];
    }
}

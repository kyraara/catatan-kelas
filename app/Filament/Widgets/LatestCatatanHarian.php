<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\CatatanHarian;
use Filament\Support\Facades\FilamentBanner;
use Filament\Notifications\Notification;

class LatestCatatanHarian extends BaseWidget
{
    // protected static ?string $maxWidth = null;
    public int | string | array $columnSpan = 'full';

    protected static ?string $heading = '10 Catatan Harian Terbaru';
    public static int $dashboardSort = 2;
    
    // Nonaktifkan polling untuk mengurangi request otomatis
    protected static ?string $pollingInterval = null;


    public function getTableQuery(): Builder
    {
        return CatatanHarian::query()
            ->with(['jadwal.kelas', 'jadwal.mataPelajaran', 'user'])
            ->latest()
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('tanggal'),
            Tables\Columns\TextColumn::make('jadwal.kelas.nama')->label('Kelas'),
            Tables\Columns\TextColumn::make('jadwal.mataPelajaran.nama')->label('Mapel'),
            Tables\Columns\TextColumn::make('user.name')->label('Diinput Oleh'),
            Tables\Columns\TextColumn::make('approved_at')
                ->label('Status')
                ->formatStateUsing(fn($state) => $state ? '✔ Approved' : 'Pending')
                ->color(fn($state) => $state ? 'success' : 'warning'),
        ];
    }

    public function mount()
    {
        if (auth()->user()->hasRole('wali_kelas')) {
            $jumlah = CatatanHarian::whereNull('approved_at')
                ->whereHas('jadwal.kelas', fn($q) => $q->where('wali_kelas_id', auth()->id()))
                ->count();

            if ($jumlah) {
                Notification::make()
                    ->title("Ada {$jumlah} catatan harian belum di-approve!")
                    ->warning()
                    ->send();
            }
        }
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'wali_kelas', 'guru']);
    }
}

<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\Kelas;
use Filament\Pages\Page;
use App\Models\CatatanHarian;
use Filament\Facades\Filament;


class LaporanCatatanHarian extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    public static ?string $navigationGroup = 'Laporan';
    protected static string $view = 'filament.pages.laporan-catatan-harian';
    public static ?int $navigationSort = 30;

    public $kelasId;
    public $guruId;
    public $tanggalFrom;
    public $tanggalTo;

    // Query Data Laporan
    public function getCatatan()
    {
        $user = Filament::auth()->user();

        $isAdmin = $user->hasRole('admin');
        $isGuru = $user->hasRole('guru');
        $isWaliKelas = $user->hasRole('wali_kelas');
        $daftarKelasId = $user->kelas()->pluck('id')->toArray();

        return CatatanHarian::with(['jadwal.kelas', 'jadwal.guru', 'jadwal.mataPelajaran', 'user'])
            ->when(
                $isGuru || $isWaliKelas,
                function ($q) use ($user, $isGuru, $isWaliKelas, $daftarKelasId) {
                    $q->where(function ($sub) use ($user, $isGuru, $isWaliKelas, $daftarKelasId) {
                        if ($isGuru) {
                            $sub->orWhereHas('jadwal', fn($j) => $j->where('guru_id', $user->id));
                        }
                        if ($isWaliKelas && !empty($daftarKelasId)) {
                            $sub->orWhereHas('jadwal', fn($j) => $j->whereIn('kelas_id', $daftarKelasId));
                        }
                    });
                }
            )
            // admin: filter guru
            ->when(
                $isAdmin && request('guruId'),
                fn($q) => $q->whereHas('jadwal', fn($j) => $j->where('guru_id', request('guruId')))
            )
            // filter kelas/tanggal tetap diletakkan di bawah
            ->when(
                request('kelasId'),
                fn($q) => $q->whereHas('jadwal', fn($j) => $j->where('kelas_id', request('kelasId')))
            )
            ->when(
                request('tanggalFrom'),
                fn($q) => $q->where('tanggal', '>=', request('tanggalFrom'))
            )
            ->when(
                request('tanggalTo'),
                fn($q) => $q->where('tanggal', '<=', request('tanggalTo'))
            )
            ->get();
    }



    public function getShowGuruFilter()
    {
        $user = Filament::auth()->user();
        return $user->hasRole('admin');
    }

    public function getShowKelasFilter()
    {
        $user = Filament::auth()->user();
        return $user->hasRole('admin') || $user->hasRole('wali_kelas');
    }
}

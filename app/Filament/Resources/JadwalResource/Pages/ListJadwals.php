<?php

namespace App\Filament\Resources\JadwalResource\Pages;

use App\Filament\Resources\JadwalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Select;
use Filament\Pages\Actions\ButtonAction;
use Maatwebsite\Excel\Facades\Excel;

class ListJadwals extends ListRecords
{
    protected static string $resource = JadwalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ButtonAction::make('exportJadwal')
                ->label('Export Jadwal (Filter)')
                ->form([
                    Select::make('kelas_id')
                        ->label('Kelas')
                        ->options(\App\Models\Kelas::pluck('nama', 'id'))
                        ->searchable()
                        ->required(),
                    Select::make('hari')
                        ->label('Hari')
                        ->options([
                            'Senin' => 'Senin',
                            'Selasa' => 'Selasa',
                            'Rabu'   => 'Rabu',
                            'Kamis'  => 'Kamis',
                            'Jumat'  => 'Jumat',
                            'Sabtu'  => 'Sabtu'
                        ])
                        ->required(),
                    Select::make('guru_id')
                        ->label('Guru')
                        ->options(\App\Models\User::role('guru')->pluck('name', 'id'))
                        ->searchable()
                        ->nullable(),

                ])
                ->action(function ($data) {
                    $jadwal = \App\Models\Jadwal::with(['kelas', 'guru', 'mataPelajaran'])
                        ->where('kelas_id', $data['kelas_id'])
                        ->where('hari', $data['hari']);
                    if ($data['guru_id']) {
                        $jadwal = $jadwal->where('guru_id', $data['guru_id']);
                    }
                    $jadwal = $jadwal->get();


                    // (Opsional: limit baris untuk debugging)
                    // $jadwal = $jadwal->take(50);

                    // Export pakai Laravel Excel
                    return Excel::download(
                        new \App\Exports\JadwalExport($jadwal),
                        'jadwal_kelas_' . $data['kelas_id'] . '_' . $data['hari'] . '.xlsx'
                    );
                }),
        ];
    }
}

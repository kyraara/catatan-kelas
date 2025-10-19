<?php

namespace App\Exports;

use App\Models\Jadwal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JadwalExport implements FromCollection, WithHeadings
{
    protected $jadwal;
    /**
     * @return \Illuminate\Support\Collection
     */

    public function __construct($jadwal)
    {
        $this->jadwal = $jadwal;
    }
    public function collection()
    {
        // Ambil relasi nama bukan ID
        return $this->jadwal->map(function ($item) {
            return [
                'Kelas'         => $item->kelas->nama ?? '',      // Kelas nama
                'Hari'          => $item->hari,
                'Jam Ke'        => $item->jam_ke,
                'Mapel'         => $item->mataPelajaran->nama ?? '',
                'Guru Pengampu' => $item->guru->name ?? '',
                'Kode Guru'     => $item->kode_guru ?? '',
            ];
        });
    }
    public function headings(): array
    {
        return [
            'Kelas',
            'Hari',
            'Jam Ke',
            'Mapel',
            'Guru Pengampu',
            'Kode Guru'
        ];
    }
}

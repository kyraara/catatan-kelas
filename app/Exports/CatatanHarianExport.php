<?php

namespace App\Exports;

use App\Models\CatatanHarian;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CatatanHarianExport implements FromCollection, WithHeadings
{
    public function __construct($filter)
    {
        $this->filter = $filter;
    }

    public function collection()
    {
        return CatatanHarian::with(['jadwal.kelas', 'jadwal.guru'])
            ->when($this->filter['kelasId'] ?? null, fn($q) => $q->whereHas('jadwal', fn($j) => $j->where('kelas_id', $this->filter['kelasId'])))
            ->when($this->filter['guruId'] ?? null, fn($q) => $q->whereHas('jadwal', fn($j) => $j->where('guru_id', $this->filter['guruId'])))
            ->when($this->filter['tanggalFrom'] ?? null, fn($q) => $q->where('tanggal', '>=', $this->filter['tanggalFrom']))
            ->when($this->filter['tanggalTo'] ?? null, fn($q) => $q->where('tanggal', '<=', $this->filter['tanggalTo']))
            ->get()
            ->map(function ($c) {
                return [
                    $c->tanggal,
                    $c->jadwal?->kelas?->nama ?? '-',
                    $c->jadwal?->guru?->name ?? '-',
                    $c->materi,
                    $c->murid_tidak_hadir,
                    $c->catatan,
                ];
            });
    }

    public function headings(): array
    {
        return ['Tanggal', 'Kelas', 'Guru', 'Materi', 'Murid Tidak Hadir', 'Catatan'];
    }
}

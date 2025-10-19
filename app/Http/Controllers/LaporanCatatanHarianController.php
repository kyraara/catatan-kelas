<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\CatatanHarianExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class LaporanCatatanHarianController extends Controller
{
    public function exportExcel(Request $request)
    {
        return Excel::download(new CatatanHarianExport($request->all()), 'CatatanHarian.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $catatan = \App\Models\CatatanHarian::with(['jadwal.kelas', 'jadwal.guru'])
            ->when($request->kelasId, fn($q) => $q->whereHas('jadwal', fn($j) => $j->where('kelas_id', $request->kelasId)))
            ->when($request->guruId, fn($q) => $q->whereHas('jadwal', fn($j) => $j->where('guru_id', $request->guruId)))
            ->when($request->tanggalFrom, fn($q) => $q->where('tanggal', '>=', $request->tanggalFrom))
            ->when($request->tanggalTo, fn($q) => $q->where('tanggal', '<=', $request->tanggalTo))
            ->get();

        $periode = ($request->tanggalFrom && $request->tanggalTo) ? "{$request->tanggalFrom} s/d {$request->tanggalTo}" : '-';
        $kelas   = $request->kelasId ? \App\Models\Kelas::find($request->kelasId)->nama : 'Semua';
        $guru    = $request->guruId ? \App\Models\User::find($request->guruId)->name : 'Semua';

        $pdf = PDF::loadView('laporan.laporan-catatan-harian-pdf', compact('catatan', 'periode', 'kelas', 'guru'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('LaporanCatatanHarian.pdf');
    }
}

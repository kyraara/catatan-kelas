<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; background: #fff; font-size: 11pt; }
        .header {display:flex;align-items:center;justify-content:center;margin-bottom:12px;}
        .logo {height:62px;margin-right:16px;}
        .title-block {text-align:center;}
        .report-title {font-size: 1.4em; font-weight: bold; margin-bottom: 3px;}
        .school-name {font-size: 1.1em; font-weight: 600;}
        hr {border:2px solid #333;}
        .info {margin-bottom:15px;}
        .info span {display:inline-block;min-width:120px;}
        table {width:100%; border-collapse:collapse;margin-top:10px;}
        th {background:#f0f0f6;color:#222;border:1px solid #333;padding:8px 5px; font-weight:bold;}
        td {border:1px solid #555;padding:7px 5px;vertical-align:top;}
        tr:nth-child(even) {background: #f9f9f9;}
        tr td {font-size:10.5pt;}
        .footer {margin-top:36px;}
        .ttd {width:280px;float:right;text-align:center;}
        .text-center {text-align:center;}
        .tgl-cetak {margin-top:10px;font-size:9.5pt;color:#444;}
    </style>
</head>
<body>
    <!-- HEADER -->
    <div class="header">
        <img src="{{ public_path('logo.png') }}" class="logo" alt="Logo sekolah">
        <div class="title-block">
            <div class="school-name">SMA / SMK NEGERI [NAMA SEKOLAH]</div>
            <div class="report-title">Laporan Catatan Harian Guru</div>
            <div style="font-size: 9.5pt;">Jl. Pendidikan No. 123 Kabupaten/Kota</div>
        </div>
    </div>
    <hr>
    <!-- Info filter -->
    <div class="info">
        <span>Periode :</span> {{ $periode ?? '-' }}<br>
        <span>Kelas :</span> {{ $kelas ?? 'Semua' }}<br>
        <span>Guru :</span> {{ $guru ?? 'Semua' }}<br>
    </div>
    <!-- Tabel -->
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kelas</th>
                <th>Guru</th>
                <th>Materi</th>
                <th>Murid Tidak Hadir</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($catatan as $c)
            <tr>
                <td class="text-center">{{ $c->tanggal }}</td>
                <td class="text-center">{{ $c->jadwal?->kelas?->nama ?? '-' }}</td>
                <td>{{ $c->jadwal?->guru?->name ?? '-' }}</td>
                <td>{{ $c->materi }}</td>
                <td>{{ $c->murid_tidak_hadir }}</td>
                <td>{{ $c->catatan }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="color:#a33;padding:20px;">Data tidak ditemukan</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <!-- Footer tanda tangan -->
    <div class="footer">
        <div class="tgl-cetak">Dicetak tanggal: {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}</div>
        <div class="ttd">
            Mengetahui,<br>
            Kepala Sekolah<br><br><br>
            <u>____________________</u>
        </div>
    </div>
</body>
</html>

<style>
:root {
  --f-section-bg: #232339;
  --f-table-header: #23233b;
  --f-table-alt: #25253d;
  --f-border: #31314d;
  --f-primary: #38bdf8;
  --f-filament: #fbbf24;
  --f-success: #22c55e;
  --f-text: #F3F6FA;
  --f-muted: #A0AEC0;
  --f-btn-pdf: #ef4444;
  --f-btn-pdf-hover: #eb2d2d;
}

.filament-section-combined {
  background: var(--f-section-bg);
  border-radius: 14px;
  box-shadow: 0 4px 22px #1f243660;
  padding: 30px 28px 22px 28px;
  max-width: 1080px;
  margin: 44px auto;
  width: 100%;
}

.section-header-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.section-title {
  font-size: 1.4em;
  font-weight: 700;
  color: var(--f-text);
  letter-spacing: .03em;
}

.filter-modal {position:fixed;top:0;left:0;z-index:99;width:100vw;height:100vh;display:none;align-items:center;justify-content:center;background:#18182099;}
.filter-modal.active {display:flex;}
.filter-modal-content {background:var(--f-section-bg);padding:30px 30px 20px 30px;border-radius:13px;box-shadow:0 2px 18px #1111;min-width:350px;}
.filter-modal-close {position:absolute;top:17px;right:17px;background:none;border:none;font-size:1.6em;color:#666;cursor:pointer;}
.filter-form {display:flex;flex-direction:column;gap:15px;}
.filter-form select, .filter-form input[type="date"] {padding:10px;border:1px solid var(--f-border);border-radius:7px;background:#26273a;color:var(--f-text);font-size:15px;}
.filter-form select:focus, .filter-form input[type="date"]:focus {outline:2px solid var(--f-primary);border-color:var(--f-primary);}
.filament-btn {display:inline-flex;align-items:center;gap:7px;padding:10px 18px;border-radius:8px;border:none;font-weight:600;font-size:15px;cursor:pointer;transition:background 0.12s;text-decoration:none;}
.filament-btn-primary {background:var(--f-filament);color:#232339;}
.filament-btn-primary:hover {background:#f59e42;}
.filament-btn-success {background:var(--f-success);color:#fff;}
.filament-btn-success:hover {background: #16a34a;}
.filament-btn-pdf {background:var(--f-btn-pdf);color:#fff;}
.filament-btn-pdf:hover {background: var(--f-btn-pdf-hover);}

.filament-table-container {
  margin-top: 18px;
  border-radius: 10px;
  box-shadow: 0 0 0 1.5px var(--f-border);
  background: var(--f-section-bg);
  width: 100%;
  overflow-x: auto;
}
.filament-table {
  width: 100%;
  min-width: 780px;
  border-collapse: separate;
  border-spacing: 0;
}
.filament-table th,
.filament-table td {
  padding: 16px 13px;
  border-bottom: 1.5px solid var(--f-border);
  text-align: left;
}
.filament-table th {
  background: var(--f-table-header);
  color: var(--f-text);
  font-weight: 650;
  font-size: 16px;
}
.filament-table td { color: var(--f-text);}
.filament-table tr:nth-child(even) {background: var(--f-table-alt);}
.filament-table tr:last-child td {border-bottom: none;}
.table-empty {
  text-align: center;
  color: var(--f-muted);
  padding: 32px;
  font-size: 16px;
}
@media (max-width: 900px) {
  .filament-section-combined { padding: 10px 2vw; max-width: 100vw; }
  .filament-table { min-width: 450px; }
}
</style>
<x-filament::page>

<div class="filament-section-combined">
  <div class="section-header-row">
    <div class="section-title">Laporan Catatan Harian</div>
    <div>
      <button type="button" class="filament-btn filament-btn-primary" onclick="document.getElementById('filterPop').classList.add('active')">
        <!-- Filter Icon -->
        <svg viewBox="0 0 20 20" fill="#232339" width="18" height="18" style="vertical-align:middle;"><path d="M3 5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v.586a2 2 0 0 1-.586 1.414l-4.293 4.293V14a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2v-2.707l-4.293-4.293A2 2 0 0 1 3 5z"/></svg>
        Filter
      </button>
            <a href="{{ route('export.laporan.catatan', request()->query()) }}" class="filament-btn filament-btn-success">
        <!-- Excel Icon -->
        <svg viewBox="0 0 20 20" fill="#fff" width="18" height="18" style="vertical-align:middle;">
          <rect x="3" y="3" width="14" height="14" rx="3"/>
          <path d="M8 7h4v2H8V7zm0 4h4v2H8v-2z" fill="#22c55e"/>
        </svg>
        Export Excel
      </a>
      <a href="{{ route('export.laporan.catatan.pdf', request()->query()) }}" class="filament-btn filament-btn-pdf" target="_blank">
        <!-- PDF Icon -->
        <svg viewBox="0 0 20 20" fill="#232339" width="18" height="18" style="vertical-align:middle;">
          <rect x="3" y="3" width="14" height="14" rx="3"/>
          <text x="6" y="15" font-size="8" font-family="Arial" fill="#fbbf24">PDF</text>
        </svg>
        Export PDF
      </a>
    </div>
  </div>

  <!-- Filter Modal -->
  <div class="filter-modal" id="filterPop">
    <div class="filter-modal-content">
      <button class="filter-modal-close" type="button" onclick="document.getElementById('filterPop').classList.remove('active')">&times;</button>
      <form method="get" class="filter-form">
  @if(filament()->auth()->user()->hasRole('admin') || filament()->auth()->user()->hasRole('wali_kelas'))
    <label>Kelas</label>
    <select name="kelasId">
      <option value="">Semua Kelas</option>
      @foreach(\App\Models\Kelas::all() as $k)
      <option value="{{ $k->id }}" {{ request('kelasId') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
      @endforeach
    </select>
  @endif

  @if(filament()->auth()->user()->hasRole('admin'))
    <label>Guru</label>
    <select name="guruId">
      <option value="">Semua Guru</option>
      @foreach(\App\Models\User::role('guru')->get() as $g)
      <option value="{{ $g->id }}" {{ request('guruId') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
      @endforeach
    </select>
  @endif

  <label>Mulai</label>
  <input type="date" name="tanggalFrom" value="{{ request('tanggalFrom') }}">
  <label>Sampai</label>
  <input type="date" name="tanggalTo" value="{{ request('tanggalTo') }}">

  <button type="submit" class="filament-btn filament-btn-primary">
    Terapkan Filter
  </button>
</form>
    </div>
  </div>

  <div class="filament-table-container">
    <table class="filament-table">
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
        @forelse($this->getCatatan() as $c)
        <tr>
          <td>{{ $c->tanggal }}</td>
          <td>{{ $c->jadwal?->kelas?->nama ?? '-' }}</td>
          <td>{{ $c->jadwal?->guru?->name ?? '-' }}</td>
          <td>{{ $c->materi }}</td>
          <td>{{ $c->murid_tidak_hadir }}</td>
          <td>{{ $c->catatan }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="table-empty">Data tidak ditemukan</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
</x-filament::page>

<script>
// Tutup modal filter dengan klik di luar popup, tetap responsif
document.addEventListener('mousedown', e=>{
  const pop = document.getElementById('filterPop');
  if(pop.classList.contains('active') && !pop.querySelector('.filter-modal-content').contains(e.target) && !e.target.closest('.filament-btn-primary')) {
    pop.classList.remove('active');
  }
});
</script>

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class CatatanHarian extends Model
{
    // LogsActivity dinonaktifkan sementara untuk performa
    // use LogsActivity;

    protected static $logAttributes = [
        'jadwal_id',
        'tanggal',
        'materi',
        'murid_tidak_hadir',
        'jam_kosong',
        'catatan',
        'user_id',
    ];
    protected $fillable = [
        'jadwal_id',
        'tanggal',
        'materi',
        'jam_kosong',
        'catatan',
        'guru_id',
        'user_id',
        'hadir',
        'izin',
        'sakit',
        'alpa',
        'status',
        'catatanreject',
        'kelas_id',
        'approved_at',
        'approved_by'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function activity()
    {
        return $this->morphMany(\Spatie\Activitylog\Models\Activity::class, 'subject');
    }


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['jadwal_id', 'tanggal', 'materi', 'murid_tidak_hadir', 'jam_kosong', 'catatan'])
            ->setDescriptionForEvent(fn(string $eventName) => "Catatan harian {$eventName}");
    }

    protected static function booted()
    {
        static::addGlobalScope('role_data_scope', function ($query) {
            $user = auth()->user();

            if (!$user) return;

            // Admin bisa lihat semua
            if ($user->hasRole('admin')) {
                return;
            }

            $isGuru = $user->hasRole('guru');
            $isWaliKelas = $user->hasRole('wali_kelas');

            // Guru hanya bisa lihat catatan dari jadwal yang dia ajar + yang dia input
            if ($isGuru && !$isWaliKelas) {
                $query->where(function($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhereHas('jadwal', fn($jadwal) => $jadwal->where('guru_id', $user->id));
                });
                return;
            }

            // Wali kelas bisa lihat catatan di kelasnya + (jika juga guru) jadwal yang diajar
            if ($isWaliKelas) {
                // Cache kelasIds untuk menghindari query berulang
                $kelasIds = cache()->remember(
                    "wali_kelas_ids_{$user->id}",
                    now()->addMinutes(5),
                    fn() => \App\Models\Kelas::where('wali_kelas_id', $user->id)->pluck('id')->toArray()
                );
                
                $query->where(function($q) use ($user, $kelasIds, $isGuru) {
                    // Catatan yang diinput sendiri
                    $q->where('user_id', $user->id);
                    
                    // Catatan di kelas yang diwalikan
                    if (!empty($kelasIds)) {
                        $q->orWhereHas('jadwal', fn($jadwal) => $jadwal->whereIn('kelas_id', $kelasIds));
                    }
                    
                    // Jika juga guru, lihat jadwal yang diajar
                    if ($isGuru) {
                        $q->orWhereHas('jadwal', fn($jadwal) => $jadwal->where('guru_id', $user->id));
                    }
                });
            }
        });
    }
    public function kelas()
    {
        return $this->belongsTo(\App\Models\Kelas::class, 'kelas_id');
    }

    public function guru()
    {
        return $this->belongsTo(\App\Models\User::class, 'guru_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }
    public function isApproved()
    {
        return !is_null($this->approved_at);
    }

    // Relasi ke presensi siswa
    public function presensis()
    {
        return $this->hasMany(\App\Models\Presensi::class);
    }

    // Helper: Ringkasan jumlah per status
    public function getRingkasanPresensi()
    {
        return [
            'hadir' => $this->presensis()->where('status', 'hadir')->count(),
            'izin' => $this->presensis()->where('status', 'izin')->count(),
            'sakit' => $this->presensis()->where('status', 'sakit')->count(),
            'alpa' => $this->presensis()->where('status', 'alpa')->count(),
        ];
    }

    // Helper: Daftar siswa per status
    public function getSiswaByStatus($status)
    {
        return $this->presensis()->with('siswa')->where('status', $status)->get()->pluck('siswa.nama');
    }
}

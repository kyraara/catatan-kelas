<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class CatatanHarian extends Model
{
    use LogsActivity;

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

            if ($user->hasRole('admin')) {
                return;
            }

            if ($user->hasRole('guru')) {
                $query->where('user_id', $user->id);
            }

            if ($user->hasRole('wali_kelas')) {
                $kelasIds = \App\Models\Kelas::where('wali_kelas_id', $user->id)->pluck('id');
                $query->whereHas('jadwal', function ($q) use ($kelasIds) {
                    $q->whereIn('kelas_id', $kelasIds);
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
}

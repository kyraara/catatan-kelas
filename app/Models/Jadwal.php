<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Jadwal extends Model
{
    // LogsActivity dinonaktifkan sementara untuk performa
    // use LogsActivity;
    protected static $logAttributes = [
        'kelas_id',
        'hari',
        'jam_ke',
        'mapel_id',
        'guru_id',
        'catatan'
    ];
    protected $fillable = ['kelas_id', 'jam_ke', 'mapel_id', 'guru_id', 'kode_guru', 'hari', 'catatan'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['kelas_id', 'hari', 'jam_ke', 'mapel_id', 'guru_id', 'catatan'])
            ->setDescriptionForEvent(fn(string $eventName) => "Jadwal {$eventName}");
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }
    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }
}

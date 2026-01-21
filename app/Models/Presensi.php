<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $fillable = [
        'catatan_harian_id',
        'siswa_id',
        'status',
        'keterangan',
    ];

    public function catatanHarian()
    {
        return $this->belongsTo(CatatanHarian::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}

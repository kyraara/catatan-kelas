<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $fillable = ['nama', 'jumlah_murid', 'wali_kelas_id'];

    public function waliKelas()
    {
        return $this->belongsTo(User::class, 'wali_kelas_id');
    }
    public function siswa()
    {
        return $this->hasMany(Siswa::class);
    }
    public function catatanHarian()
    {
        return $this->hasMany(CatatanHarian::class);
    }
    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }
}

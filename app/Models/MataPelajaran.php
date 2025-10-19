<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    protected $fillable = ['nama', 'kode_mapel'];


    public static function generateKodeMapel($nama)
    {
        // Ambil 3 huruf pertama dari nama mapel (tanpa spasi/tanda baca)
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $nama), 0, 3));

        // Cari nomor urut terakhir yang pakai prefix ini
        $lastNumber = self::where('kode_mapel', 'LIKE', $prefix . '%')
            ->orderByDesc('kode_mapel')
            ->value('kode_mapel');

        if ($lastNumber) {
            $number = intval(substr($lastNumber, 3)) + 1;
        } else {
            $number = 1;
        }

        return $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }
}

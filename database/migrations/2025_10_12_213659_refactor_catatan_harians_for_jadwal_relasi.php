<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('catatan_harians', function (Blueprint $table) {
            $table->unsignedBigInteger('jadwal_id')->nullable()->after('id');
            $table->foreign('jadwal_id')->references('id')->on('jadwals')->onDelete('set null');

            // Hapus kolom duplikat
            $table->dropColumn(['kelas_id', 'guru_id', 'mapel_id', 'jam_ke']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catatan_harians', function (Blueprint $table) {
            $table->unsignedBigInteger('kelas_id')->nullable();
            $table->unsignedBigInteger('guru_id')->nullable();
            $table->unsignedBigInteger('mapel_id')->nullable();
            $table->tinyInteger('jam_ke')->nullable();

            // Hapus jadwal_id
            $table->dropForeign(['jadwal_id']);
            $table->dropColumn('jadwal_id');
        });
    }
};

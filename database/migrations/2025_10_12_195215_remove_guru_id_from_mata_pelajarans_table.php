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
        Schema::table('mata_pelajarans', function (Blueprint $table) {
            if (Schema::hasColumn('mata_pelajarans', 'guru_id')) {
                $table->dropColumn('guru_id'); // Langsung drop, FK sudah tidak ada
            }
            if (Schema::hasColumn('mata_pelajarans', 'kode_guru')) {
                $table->dropColumn('kode_guru');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mata_pelajarans', function (Blueprint $table) {
            $table->unsignedBigInteger('guru_id')->nullable();
            $table->string('kode_guru')->nullable();
        });
    }
};

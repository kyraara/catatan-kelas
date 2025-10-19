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
        Schema::table('jadwals', function (Blueprint $table) {
            $table->string('catatan')->nullable()->after('kode_guru');
        });
    }
    public function down()
    {
        Schema::table('jadwals', function (Blueprint $table) {
            $table->dropColumn('catatan');
        });
    }
};

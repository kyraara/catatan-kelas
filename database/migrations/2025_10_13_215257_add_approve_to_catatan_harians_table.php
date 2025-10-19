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
            $table->timestamp('approved_at')->nullable()->after('updated_at');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');

            // Optional: index untuk approved_by
            $table->index('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catatan_harians', function (Blueprint $table) {
            $table->dropColumn(['approved_at', 'approved_by']);
        });
    }
};

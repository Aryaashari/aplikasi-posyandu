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
        Schema::table('trx_pengukuran', function (Blueprint $table) {
            $table->enum('cara_ukur', ['Berdiri', 'Telentang'])->after('lingkar_kepala');
            $table->text('catatan')->nullable()->after('status_stunting');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trx_pengukuran', function (Blueprint $table) {
            $table->dropColumn(['cara_ukur', 'catatan']);
        });
    }
};

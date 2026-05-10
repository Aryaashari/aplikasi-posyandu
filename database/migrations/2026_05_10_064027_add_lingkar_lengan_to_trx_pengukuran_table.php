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
            $table->decimal('lingkar_lengan', 8, 2)->nullable()->after('lingkar_kepala');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trx_pengukuran', function (Blueprint $table) {
            $table->dropColumn('lingkar_lengan');
        });
    }
};

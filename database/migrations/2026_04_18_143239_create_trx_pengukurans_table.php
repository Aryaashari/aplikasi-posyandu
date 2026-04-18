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
        Schema::create('trx_pengukuran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_anak')->constrained('md_anak');
            $table->date('tanggal_pengukuran');
            $table->decimal('berat_badan', 8, 2);
            $table->decimal('tinggi_badan', 8, 2);
            $table->decimal('lingkar_kepala', 8, 2);
            $table->decimal('zscore_bb_u', 8, 3);
            $table->decimal('zscore_tb_u', 8, 3);
            $table->decimal('zscore_bb_tb', 8, 3);
            $table->string('status_gizi');
            $table->string('status_stunting');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_pengukuran');
    }
};

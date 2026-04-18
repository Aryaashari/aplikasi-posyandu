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
        Schema::create('trx_kehadiran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_anak')->constrained('md_anak');
            $table->date('tanggal');
            $table->boolean('status_hadir');
            $table->text('keterangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_kehadiran');
    }
};

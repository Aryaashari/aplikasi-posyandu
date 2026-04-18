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
        Schema::create('trx_imunisasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_anak')->constrained('md_anak');
            $table->string('jenis_imunisasi');
            $table->date('tanggal_imunisasi');
            $table->text('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_imunisasi');
    }
};

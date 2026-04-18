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
        Schema::create('md_anak', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_posyandu')->constrained('md_posyandu');
            $table->string('nama');
            $table->string('nik')->unique();
            $table->string('no_kk');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('nama_ayah');
            $table->string('nama_ibu');
            $table->string('no_telp_ortu');
            $table->text('alamat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('md_anak');
    }
};

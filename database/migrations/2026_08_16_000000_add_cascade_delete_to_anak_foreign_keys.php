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
        Schema::table('trx_imunisasi', function (Blueprint $table) {
            $table->dropForeign(['id_anak']);
            $table->foreign('id_anak')
                  ->references('id')
                  ->on('md_anak')
                  ->onDelete('cascade');
        });

        Schema::table('trx_pengukuran', function (Blueprint $table) {
            $table->dropForeign(['id_anak']);
            $table->foreign('id_anak')
                  ->references('id')
                  ->on('md_anak')
                  ->onDelete('cascade');
        });

        Schema::table('trx_kehadiran', function (Blueprint $table) {
            $table->dropForeign(['id_anak']);
            $table->foreign('id_anak')
                  ->references('id')
                  ->on('md_anak')
                  ->onDelete('cascade');
        });

        Schema::table('trx_pmt', function (Blueprint $table) {
            $table->dropForeign(['id_anak']);
            $table->foreign('id_anak')
                  ->references('id')
                  ->on('md_anak')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trx_imunisasi', function (Blueprint $table) {
            $table->dropForeign(['id_anak']);
            $table->foreign('id_anak')
                  ->references('id')
                  ->on('md_anak');
        });

        Schema::table('trx_pengukuran', function (Blueprint $table) {
            $table->dropForeign(['id_anak']);
            $table->foreign('id_anak')
                  ->references('id')
                  ->on('md_anak');
        });

        Schema::table('trx_kehadiran', function (Blueprint $table) {
            $table->dropForeign(['id_anak']);
            $table->foreign('id_anak')
                  ->references('id')
                  ->on('md_anak');
        });

        Schema::table('trx_pmt', function (Blueprint $table) {
            $table->dropForeign(['id_anak']);
            $table->foreign('id_anak')
                  ->references('id')
                  ->on('md_anak');
        });
    }
};

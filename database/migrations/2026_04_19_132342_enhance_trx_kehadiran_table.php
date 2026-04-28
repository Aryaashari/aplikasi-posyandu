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
        Schema::table('trx_kehadiran', function (Blueprint $table) {
            $table->foreignId('id_posyandu')->after('id_anak')->constrained('md_posyandu');
            $table->time('waktu_checkin')->after('tanggal')->nullable();
            $table->string('status', 50)->after('status_hadir')->default('Hadir'); // Hadir, Izin, Sakit
            $table->timestamps();
            
            // Cleanup old column if needed, or keep for transition
            // $table->dropColumn('status_hadir');
        });
    }

    public function down(): void
    {
        Schema::table('trx_kehadiran', function (Blueprint $table) {
            $table->dropForeign(['id_posyandu']);
            $table->dropColumn(['id_posyandu', 'waktu_checkin', 'status', 'created_at', 'updated_at']);
        });
    }
};

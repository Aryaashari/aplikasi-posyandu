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
        Schema::create('ref_growth_standard', function (Blueprint $table) {
            $table->id();
            $table->integer('umur_bulan');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->decimal('median_tb', 8, 2);
            $table->decimal('sd_minus_2', 8, 2);
            $table->decimal('sd_minus_3', 8, 2);
            $table->decimal('median_bb', 8, 2);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_growth_standard');
    }
};

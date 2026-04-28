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
        Schema::table('ref_growth_standard', function (Blueprint $table) {
            // Rename generic sd columns if needed or just add specific ones
            $table->renameColumn('sd_minus_2', 'sd_minus_2_tb');
            $table->renameColumn('sd_minus_3', 'sd_minus_3_tb');
            $table->decimal('sd_minus_2_bb', 8, 2)->after('median_bb');
            $table->decimal('sd_minus_3_bb', 8, 2)->after('sd_minus_2_bb');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ref_growth_standard', function (Blueprint $table) {
            $table->renameColumn('sd_minus_2_tb', 'sd_minus_2');
            $table->renameColumn('sd_minus_3_tb', 'sd_minus_3');
            $table->dropColumn(['sd_minus_2_bb', 'sd_minus_3_bb']);
        });
    }
};

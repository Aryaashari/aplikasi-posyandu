<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GrowthStandardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample data for Boys (0-1 month)
        \App\Models\RefGrowthStandard::create([
            'umur_bulan' => 0,
            'jenis_kelamin' => 'L',
            'median_bb' => 3.3,
            'sd_minus_2_bb' => 2.5,
            'sd_minus_3_bb' => 2.1,
            'median_tb' => 49.9,
            'sd_minus_2_tb' => 46.1,
            'sd_minus_3_tb' => 44.2,
        ]);

        \App\Models\RefGrowthStandard::create([
            'umur_bulan' => 1,
            'jenis_kelamin' => 'L',
            'median_bb' => 4.5,
            'sd_minus_2_bb' => 3.4,
            'sd_minus_3_bb' => 2.9,
            'median_tb' => 54.7,
            'sd_minus_2_tb' => 50.8,
            'sd_minus_3_tb' => 48.9,
        ]);
        
        // Add more months as needed, or just these for test
    }
}

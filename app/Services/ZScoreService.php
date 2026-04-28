<?php

namespace App\Services;

use App\Models\RefGrowthStandard;
use App\Models\MdAnak;
use Carbon\Carbon;

class ZScoreService
{
    public function calculate(MdAnak $anak, $tanggal_pengukuran, $berat, $tinggi)
    {
        $tgl_lahir = Carbon::parse($anak->tanggal_lahir);
        $tgl_ukur = Carbon::parse($tanggal_pengukuran);
        $umur_bulan = (int)$tgl_lahir->diffInMonths($tgl_ukur);

        $standard = RefGrowthStandard::where('umur_bulan', $umur_bulan)
            ->where('jenis_kelamin', $anak->jenis_kelamin)
            ->first();

        if (!$standard) {
            return [
                'zscore_bb_u' => 0,
                'zscore_tb_u' => 0,
                'zscore_bb_tb' => 0,
                'status_gizi' => 'Data Standar Tidak Ditemukan',
                'status_stunting' => 'Data Standar Tidak Ditemukan',
            ];
        }

        // Simplistic Z-Score Calculation: (Measured - Median) / SD
        // Since we only have Median, SD-2, and SD-3 in the table:
        // SD = (Median - SD-2) / 2
        
        $sd_bb = ($standard->median_bb - $standard->sd_minus_2_bb) / 2;
        $zscore_bb_u = $sd_bb != 0 ? ($berat - $standard->median_bb) / $sd_bb : 0;

        $sd_tb = ($standard->median_tb - $standard->sd_minus_2_tb) / 2;
        $zscore_tb_u = $sd_tb != 0 ? ($tinggi - $standard->median_tb) / $sd_tb : 0;

        // BB/TB is more complex (needs weight-for-height table which we don't have yet)
        $zscore_bb_tb = 0; 

        return [
            'zscore_bb_u' => round($zscore_bb_u, 3),
            'zscore_tb_u' => round($zscore_tb_u, 3),
            'zscore_bb_tb' => round($zscore_bb_tb, 3),
            'status_gizi' => $this->determineStatusGizi($zscore_bb_u),
            'status_stunting' => $this->determineStatusStunting($zscore_tb_u),
        ];
    }

    private function determineStatusGizi($zscore)
    {
        if ($zscore < -3) return 'Gizi Buruk';
        if ($zscore < -2) return 'Gizi Kurang';
        if ($zscore <= 2) return 'Gizi Baik';
        return 'Gizi Lebih';
    }

    private function determineStatusStunting($zscore)
    {
        if ($zscore < -3) return 'Sangat Pendek (Severely Stunted)';
        if ($zscore < -2) return 'Pendek (Stunted)';
        if ($zscore <= 3) return 'Normal';
        return 'Tinggi';
    }
}

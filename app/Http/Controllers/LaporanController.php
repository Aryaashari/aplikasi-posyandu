<?php

namespace App\Http\Controllers;

use App\Models\MdAnak;
use App\Models\TrxKehadiran;
use App\Models\TrxPengukuran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Exports\LaporanExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    private function getAggregatedData($posyanduId, $month, $year)
    {
        $totalAnak = MdAnak::where('id_posyandu', $posyanduId)->count();

        // Get latest measurements for each child in the given month
        $pengukurans = TrxPengukuran::with('anak')
            ->whereHas('anak', function($query) use ($posyanduId) {
                $query->where('id_posyandu', $posyanduId);
            })
            ->whereMonth('tanggal_pengukuran', $month)
            ->whereYear('tanggal_pengukuran', $year)
            // Use subquery to get latest per anak in simpler way, but collection group by is easier for small datasets
            ->get()
            ->groupBy('id_anak')
            ->map(function ($items) {
                return $items->sortByDesc('tanggal_pengukuran')->first();
            });

        $giziStats = [
            'Gizi Buruk' => 0, 'Gizi Kurang' => 0, 'Gizi Baik' => 0, 'Gizi Lebih' => 0
        ];
        
        $stuntingStats = [
            'Sangat Pendek (Severely Stunted)' => 0, 'Pendek (Stunted)' => 0, 'Normal' => 0, 'Tinggi' => 0
        ];

        $weightTrends = [
            'all' => ['naik' => 0, 'turun' => 0, 'tetap' => 0],
            'L' => ['naik' => 0, 'turun' => 0, 'tetap' => 0],
            'P' => ['naik' => 0, 'turun' => 0, 'tetap' => 0],
        ];

        // Hitung tren berat badan untuk semua anak berdasarkan pengukuran paling terakhir mereka (all period)
        $allChildrenMeasurements = TrxPengukuran::with('anak')
            ->whereHas('anak', function($query) use ($posyanduId) {
                $query->where('id_posyandu', $posyanduId);
            })
            ->orderBy('tanggal_pengukuran', 'desc')
            ->get()
            ->groupBy('id_anak');

        foreach ($allChildrenMeasurements as $id_anak => $measurements) {
            if ($measurements->count() >= 2) {
                // $measurements sudah diurutkan desc, jadi first() adalah yang paling terakhir
                $latest = $measurements->first();
                $prev = $measurements->values()->get(1);

                $trend = 'tetap';
                if ($latest->berat_badan > $prev->berat_badan) $trend = 'naik';
                elseif ($latest->berat_badan < $prev->berat_badan) $trend = 'turun';

                $weightTrends['all'][$trend]++;
                if ($latest->anak) {
                    $jk = $latest->anak->jenis_kelamin;
                    if (isset($weightTrends[$jk])) {
                        $weightTrends[$jk][$trend]++;
                    }
                }
            }
        }

        $giziDetail = [
            'Gizi Buruk' => [], 'Gizi Kurang' => [], 'Gizi Baik' => [], 'Gizi Lebih' => []
        ];
        
        $stuntingDetail = [
            'Sangat Pendek (Severely Stunted)' => [], 'Pendek (Stunted)' => [], 'Normal' => [], 'Tinggi' => []
        ];

        foreach ($pengukurans as $id_anak => $p) {
            $anakData = [
                'id' => $p->id_anak,
                'nama' => $p->anak->nama,
                'jk' => $p->anak->jenis_kelamin,
                'bb' => $p->berat_badan,
                'tb' => $p->tinggi_badan,
                'tanggal' => $p->tanggal_pengukuran
            ];

            if (isset($giziStats[$p->status_gizi])) {
                $giziStats[$p->status_gizi]++;
                $giziDetail[$p->status_gizi][] = $anakData;
            }
            if (isset($stuntingStats[$p->status_stunting])) {
                $stuntingStats[$p->status_stunting]++;
                $stuntingDetail[$p->status_stunting][] = $anakData;
            }
        }

        $kehadiranQuery = TrxKehadiran::with('anak')
            ->where('id_posyandu', $posyanduId)
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->get()
            ->unique('id_anak');

        $totalHadir = $kehadiranQuery->count();
        $kehadiranDetail = [
            'gender' => ['L' => 0, 'P' => 0],
            'category' => [
                '0-5' => 0,
                '6-11' => 0,
                '12-59' => 0,
                'Lulus' => 0
            ]
        ];

        foreach ($kehadiranQuery as $k) {
            $anak = $k->anak;
            if ($anak) {
                // Gender
                $kehadiranDetail['gender'][$anak->jenis_kelamin]++;

                // Category (consistent with Svelte frontend logic)
                $birthDate = \Carbon\Carbon::parse($anak->tanggal_lahir);
                $measureDate = \Carbon\Carbon::parse($k->tanggal);
                $months = (int)$birthDate->diffInMonths($measureDate);

                if ($months <= 5) $kehadiranDetail['category']['0-5']++;
                elseif ($months <= 11) $kehadiranDetail['category']['6-11']++;
                elseif ($months < 59) $kehadiranDetail['category']['12-59']++;
                else $kehadiranDetail['category']['Lulus']++;
            }
        }

        return [
            'total_anak' => $totalAnak,
            'total_hadir' => $totalHadir,
            'hadir_detail' => $kehadiranDetail,
            'gizi' => $giziStats,
            'gizi_detail' => $giziDetail,
            'stunting' => $stuntingStats,
            'stunting_detail' => $stuntingDetail,
            'weight_trends' => $weightTrends,
            'month' => $month,
            'year' => $year
        ];
    }

    public function index(Request $request)
    {
        $posyanduId = Auth::user()->id_posyandu;
        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));

        $data = $this->getAggregatedData($posyanduId, $month, $year);

        return Inertia::render('Laporan/Index', [
            'stats' => $data,
            'filters' => [
                'month' => (int)$month,
                'year' => (int)$year,
            ]
        ]);
    }

    public function exportPdf(Request $request)
    {
        $posyanduId = Auth::user()->id_posyandu;
        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));

        $data = $this->getAggregatedData($posyanduId, $month, $year);
        $data['posyandu'] = \App\Models\MdPosyandu::find($posyanduId);
        
        $pdf = Pdf::loadView('exports.laporan-pdf', $data);
        return $pdf->download("Laporan_Posyandu_{$month}_{$year}.pdf");
    }

    public function exportExcel(Request $request)
    {
        $posyanduId = Auth::user()->id_posyandu;
        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));

        return Excel::download(new LaporanExport($posyanduId, $month, $year), "Rekap_Detail_Posyandu_{$month}_{$year}.xlsx");
    }
}
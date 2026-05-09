<?php

namespace App\Http\Controllers;

use App\Imports\DataPosyanduImport;
use App\Services\ZScoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    protected ZScoreService $zScoreService;

    public function __construct(ZScoreService $zScoreService)
    {
        $this->zScoreService = $zScoreService;
    }

    /**
     * Show the import page.
     */
    public function index()
    {
        return Inertia::render('Import/Index');
    }

    /**
     * Process the Excel import.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes' => 'Format file harus .xlsx, .xls, atau .csv.',
            'file.max' => 'Ukuran file maksimal 10MB.',
        ]);

        $force = $request->boolean('force', false);
        $posyanduId = Auth::user()->id_posyandu;

        $import = new DataPosyanduImport($posyanduId, $this->zScoreService, $force);

        try {
            Excel::import($import, $request->file('file'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses file: ' . $e->getMessage());
        }

        // If not forced and there are validation issues (errors or warnings), return them
        if (!$force && !empty($import->issues)) {
            return Inertia::render('Import/Index', [
                'importIssues' => $import->issues,
                'issueCount' => count($import->issues),
            ]);
        }

        // Build success summary
        $summary = [];
        if ($import->importedCount > 0) {
            $summary[] = "{$import->importedCount} data baru berhasil diimport";
        }
        if ($import->updatedCount > 0) {
            $summary[] = "{$import->updatedCount} data berhasil diperbarui/ditimpa";
        }

        $message = !empty($summary) ? implode(', ', $summary) . '.' : 'Tidak ada data baru yang berhasil diimport.';

        return redirect()->route('import.index')->with('success', $message);
    }

    /**
     * Download the import template.
     */
    public function template()
    {
        $headers = [
            'No', 'NIK', 'nama_anak', 'tgl_lahir', 'jk', 'umur_tahun', 'umur_bulan',
            'nm_ortu', 'PKM', 'KEL', 'POSY', 'RT', 'RW', 'ALAMAT', 'TANGGALUKUR',
            'BERAT', 'TINGGI', 'LILA', 'lingkar_kepala', 'Pitting_edema', 'CARAUKUR',
            'vita', 'asi_bulan_0', 'asi_bulan_1', 'asi_bulan_2', 'asi_bulan_3',
            'asi_bulan_4', 'asi_bulan_5', 'asi_bulan_6', 'kelas_ibu_balita', 'mbg',
        ];

        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');
            // Add BOM for Excel UTF-8 compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $headers);
            // Add example row
            fputcsv($file, [
                '1', '3201234567890001', 'Anak Contoh', '2023-01-15', 'L', '2', '24',
                'Ibu Contoh', 'PKM Contoh', 'Kelurahan Contoh', 'Posyandu Contoh',
                '001', '002', 'Jl. Contoh No. 1', '2025-05-01',
                '12.5', '85.3', '14.5', '45.2', 'Tidak', 'Berdiri',
                'Ya', 'Ya', 'Ya', 'Ya', 'Ya', 'Ya', 'Ya', 'Ya', 'Ya', 'Ya',
            ]);
            fclose($file);
        };

        return response()->streamDownload($callback, 'template_import_posyandu.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}

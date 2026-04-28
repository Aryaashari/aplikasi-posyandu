<?php

namespace App\Exports;

use App\Models\MdAnak;
use App\Models\TrxPengukuran;
use App\Models\TrxKehadiran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $posyanduId;
    protected $month;
    protected $year;

    public function __construct($posyanduId, $month, $year)
    {
        $this->posyanduId = $posyanduId;
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        return MdAnak::where('id_posyandu', $this->posyanduId)
            ->with(['pengukuran' => function($q) {
                // Get measurements for the given month
                $q->whereMonth('tanggal_pengukuran', $this->month)
                  ->whereYear('tanggal_pengukuran', $this->year)
                  ->latest('tanggal_pengukuran');
            }, 'kehadiran' => function($q) {
                $q->whereMonth('tanggal', $this->month)
                  ->whereYear('tanggal', $this->year);
            }])
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'NIK',
            'Nama Balita',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'Hadir Bulan Ini (Kali)',
            'Berat Badan (kg)',
            'Tinggi Badan (cm)',
            'Status Gizi (BB/U)',
            'Status Stunting (TB/U)',
        ];
    }

    public function map($anak): array
    {
        static $row = 1;
        
        $latestPengukuran = $anak->pengukuran->first();
        $kehadiranCount = $anak->kehadiran->count();

        return [
            $row++,
            $anak->nik,
            $anak->nama,
            $anak->tanggal_lahir,
            $anak->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
            $kehadiranCount,
            $latestPengukuran ? $latestPengukuran->berat_badan : '-',
            $latestPengukuran ? $latestPengukuran->tinggi_badan : '-',
            $latestPengukuran ? $latestPengukuran->status_gizi : '-',
            $latestPengukuran ? $latestPengukuran->status_stunting : '-',
        ];
    }
}

<?php

namespace App\Imports;

use App\Models\MdAnak;
use App\Models\TrxPengukuran;
use App\Models\TrxKehadiran;
use App\Services\ZScoreService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class DataPosyanduImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    protected int $posyanduId;
    protected ZScoreService $zScoreService;
    public bool $force;

    public int $importedCount = 0;
    public int $updatedCount = 0;

    /**
     * Structured errors for display in UI table.
     * Each entry: ['baris' => int, 'nik' => string, 'nama' => string, 'kolom' => string, 'pesan' => string, 'tipe' => 'error'|'warning']
     */
    public array $issues = [];

    public function __construct(int $posyanduId, ZScoreService $zScoreService, bool $force = false)
    {
        $this->posyanduId = $posyanduId;
        $this->zScoreService = $zScoreService;
        $this->force = $force;
    }

    public function collection(Collection $rows)
    {
        // ============================================================
        // PHASE 1: Validate ALL rows first. Collect errors & warnings.
        // ============================================================
        $validatedRows = [];

        // Pre-fetch all anak for this posyandu to optimize query inside loop
        $existingAnak = MdAnak::where('id_posyandu', $this->posyanduId)->get()->keyBy('nik');
        
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // heading row = 1, data starts at 2

            // Parse NIK carefully to handle Excel floats/scientific notation
            $rawNik = $row['nik'] ?? null;
            $nik = null;
            if ($rawNik !== null && $rawNik !== '') {
                if (is_numeric($rawNik)) {
                    // Prevent scientific notation (e.g. 3.2E15 -> 3200000000000000)
                    $nik = number_format((float)$rawNik, 0, '', '');
                } else {
                    $nik = preg_replace('/[^0-9]/', '', (string)$rawNik);
                }
            }

            $namaAnak = $this->cleanValue($row['nama_anak'] ?? null);

            // Skip completely empty rows silently
            if (empty($nik) && empty($namaAnak)) {
                continue;
            }

            $hasError = false;

            // --- Validate required fields ---
            if (empty($nik)) {
                $this->addIssue($rowNumber, $nik, $namaAnak, 'NIK', 'NIK tidak boleh kosong', 'error');
                $hasError = true;
            } else if (!preg_match('/^\d+$/', $nik)) {
                $this->addIssue($rowNumber, $nik, $namaAnak, 'NIK', "NIK harus berupa angka, ditemukan: \"{$nik}\"", 'error');
                $hasError = true;
            }

            if (empty($namaAnak)) {
                $this->addIssue($rowNumber, $nik, $namaAnak, 'nama_anak', 'Nama anak tidak boleh kosong', 'error');
                $hasError = true;
            }

            // --- Validate tanggal lahir ---
            $tanggalLahir = $this->parseDate($row['tgl_lahir'] ?? null);
            if (!empty($row['tgl_lahir']) && $tanggalLahir === null) {
                $this->addIssue($rowNumber, $nik, $namaAnak, 'tgl_lahir', "Format tanggal tidak valid: \"{$row['tgl_lahir']}\"", 'error');
                $hasError = true;
            }
            if (empty($row['tgl_lahir'])) {
                $this->addIssue($rowNumber, $nik, $namaAnak, 'tgl_lahir', 'Tanggal lahir tidak boleh kosong', 'error');
                $hasError = true;
            }

            // --- Validate jenis kelamin ---
            $jenisKelamin = $this->parseJenisKelamin($row['jk'] ?? null);
            if (!empty($row['jk']) && $jenisKelamin === null) {
                $this->addIssue($rowNumber, $nik, $namaAnak, 'jk', "Jenis kelamin tidak valid: \"{$row['jk']}\". Gunakan L atau P", 'error');
                $hasError = true;
            }
            if (empty($row['jk'])) {
                $this->addIssue($rowNumber, $nik, $namaAnak, 'jk', 'Jenis kelamin tidak boleh kosong. Gunakan L atau P', 'error');
                $hasError = true;
            }

            // --- Validate pengukuran fields if any measurement data is present ---
            $berat = $this->parseNumeric($row['berat'] ?? null);
            $tinggi = $this->parseNumeric($row['tinggi'] ?? null);
            $tanggalUkur = $this->parseDate($row['tanggalukur'] ?? null);
            $lingkarKepala = $this->parseNumeric($row['lingkar_kepala'] ?? null);
            $lingkarLengan = $this->parseNumeric($row['lila'] ?? null);

            $hasMeasurementData = !empty($row['berat']) || !empty($row['tinggi']) || !empty($row['tanggalukur']);

            if ($hasMeasurementData) {
                if (!empty($row['berat']) && $berat === null) {
                    $this->addIssue($rowNumber, $nik, $namaAnak, 'BERAT', "Berat badan bukan angka valid: \"{$row['berat']}\"", 'error');
                    $hasError = true;
                }
                if (empty($row['berat'])) {
                    $this->addIssue($rowNumber, $nik, $namaAnak, 'BERAT', 'Berat badan kosong. Isi berat atau kosongkan semua kolom pengukuran', 'error');
                    $hasError = true;
                }
                if (!empty($row['tinggi']) && $tinggi === null) {
                    $this->addIssue($rowNumber, $nik, $namaAnak, 'TINGGI', "Tinggi badan bukan angka valid: \"{$row['tinggi']}\"", 'error');
                    $hasError = true;
                }
                if (empty($row['tinggi'])) {
                    $this->addIssue($rowNumber, $nik, $namaAnak, 'TINGGI', 'Tinggi badan kosong. Isi tinggi atau kosongkan semua kolom pengukuran', 'error');
                    $hasError = true;
                }
                if (!empty($row['tanggalukur']) && $tanggalUkur === null) {
                    $this->addIssue($rowNumber, $nik, $namaAnak, 'TANGGALUKUR', "Format tanggal ukur tidak valid: \"{$row['tanggalukur']}\"", 'error');
                    $hasError = true;
                }
                if (empty($row['tanggalukur'])) {
                    $this->addIssue($rowNumber, $nik, $namaAnak, 'TANGGALUKUR', 'Tanggal ukur kosong. Isi tanggal atau kosongkan semua kolom pengukuran', 'error');
                    $hasError = true;
                }
                if (!empty($row['lingkar_kepala']) && $lingkarKepala === null) {
                    $this->addIssue($rowNumber, $nik, $namaAnak, 'lingkar_kepala', "Lingkar kepala bukan angka valid: \"{$row['lingkar_kepala']}\"", 'error');
                    $hasError = true;
                }
                if (!empty($row['lila']) && $lingkarLengan === null) {
                    $this->addIssue($rowNumber, $nik, $namaAnak, 'LILA', "Lingkar lengan bukan angka valid: \"{$row['lila']}\"", 'error');
                    $hasError = true;
                }

                // Validate reasonable ranges
                if ($berat !== null && ($berat < 0.5 || $berat > 50)) {
                    $this->addIssue($rowNumber, $nik, $namaAnak, 'BERAT', "Berat badan di luar rentang wajar (0.5-50 kg): {$berat}", 'error');
                    $hasError = true;
                }
                if ($tinggi !== null && ($tinggi < 30 || $tinggi > 200)) {
                    $this->addIssue($rowNumber, $nik, $namaAnak, 'TINGGI', "Tinggi badan di luar rentang wajar (30-200 cm): {$tinggi}", 'error');
                    $hasError = true;
                }
            }

            // Only proceed to check warnings if there are no hard errors for this row
            if (!$hasError) {
                // Check Overwrite Warning
                $anak = $existingAnak->get($nik);
                if ($anak && $tanggalUkur) {
                    // Query directly to avoid loading all measurements into memory
                    $existingPengukuran = TrxPengukuran::where('id_anak', $anak->id)
                        ->where('tanggal_pengukuran', $tanggalUkur)
                        ->exists();
                    
                    if ($existingPengukuran) {
                        $this->addIssue($rowNumber, $nik, $namaAnak, 'TANGGALUKUR', "Data pengukuran pada {$tanggalUkur} sudah ada. Jika dilanjutkan, data lama akan DITIMPA.", 'warning');
                    }
                }

                // Store parsed row data for phase 2
                $validatedRows[] = [
                    'row_number' => $rowNumber,
                    'nik' => $nik,
                    'nama_anak' => $namaAnak,
                    'tanggal_lahir' => $tanggalLahir,
                    'jenis_kelamin' => $jenisKelamin,
                    'nama_ortu' => $this->cleanValue($row['nm_ortu'] ?? null),
                    'alamat' => $this->buildAlamat($row),
                    'berat' => $berat,
                    'tinggi' => $tinggi,
                    'tanggal_ukur' => $tanggalUkur,
                    'lingkar_kepala' => $lingkarKepala,
                    'lingkar_lengan' => $lingkarLengan,
                    'cara_ukur' => $this->parseCaraUkur($row['caraukur'] ?? null),
                    'has_measurement' => $hasMeasurementData,
                ];
            }
        }

        // ============================================================
        // PHASE 1.5: Check for duplicate NIKs within the file
        // ============================================================
        $nikCounts = [];
        foreach ($validatedRows as $vr) {
            if (!empty($vr['nik'])) {
                $nikCounts[$vr['nik']][] = $vr['row_number'];
            }
        }
        $duplicateNiks = [];
        foreach ($nikCounts as $nik => $rowNumbers) {
            if (count($rowNumbers) > 1) {
                $rowsStr = implode(', ', $rowNumbers);
                foreach ($rowNumbers as $rn) {
                    $nama = collect($validatedRows)->firstWhere('row_number', $rn)['nama_anak'] ?? '-';
                    $this->addIssue($rn, $nik, $nama, 'NIK', "NIK duplikat ditemukan di dalam file Excel pada baris: {$rowsStr}. Baris ini akan dilewati.", 'error');
                }
                $duplicateNiks[] = $nik;
            }
        }

        // Filter out validatedRows that are duplicates
        if (!empty($duplicateNiks)) {
            $validatedRows = array_filter($validatedRows, function($vr) use ($duplicateNiks) {
                return !in_array($vr['nik'], $duplicateNiks);
            });
        }

        // ============================================================
        // If there are ANY issues (errors OR warnings) and NOT forced, STOP.
        // ============================================================
        if (!$this->force && !empty($this->issues)) {
            return; // Issues will be returned to controller
        }

        // ============================================================
        // PHASE 2: Forced or All Valid — insert the validatedRows
        // ============================================================
        DB::beginTransaction();

        try {
            foreach ($validatedRows as $vr) {
                // Upsert anak
                $anakData = [
                    'id_posyandu' => $this->posyanduId,
                    'nama' => $vr['nama_anak'] ?: '-',
                    'tanggal_lahir' => $vr['tanggal_lahir'] ?: now()->format('Y-m-d'),
                    'jenis_kelamin' => $vr['jenis_kelamin'] ?: 'L',
                    'nama_ibu' => $vr['nama_ortu'] ?: '-',
                    'nama_ayah' => '-',
                    'no_kk' => '-',
                    'no_telp_ortu' => '-',
                    'alamat' => $vr['alamat'] ?: '-',
                ];

                $anak = MdAnak::where('nik', $vr['nik'])->first();

                if ($anak) {
                    $anak->update(array_filter($anakData, fn($v) => $v !== '-' && $v !== null));
                    $this->updatedCount++;
                } else {
                    $anakData['nik'] = $vr['nik'];
                    $anak = MdAnak::create($anakData);
                    $this->importedCount++;
                }

                // Auto sync attendance if measurement date is present
                if ($vr['tanggal_ukur']) {
                    $birthDate = \Carbon\Carbon::parse($anak->tanggal_lahir);
                    $measureDate = \Carbon\Carbon::parse($vr['tanggal_ukur']);
                    $ageInMonths = (int)$birthDate->diffInMonths($measureDate);

                    if ($ageInMonths < 59) {
                        TrxKehadiran::updateOrCreate(
                            [
                                'id_anak' => $anak->id,
                                'id_posyandu' => $this->posyanduId,
                                'tanggal' => $vr['tanggal_ukur'],
                            ],
                            [
                                'status_hadir' => true,
                                'status' => 'Hadir',
                                'keterangan' => 'Hadir otomatis dari import data.',
                            ]
                        );
                    }
                }

                // Insert pengukuran if measurement data exists and is complete
                if ($vr['has_measurement'] && $vr['berat'] && $vr['tinggi'] && $vr['tanggal_ukur']) {
                    $zScoreResults = $this->zScoreService->calculate(
                        $anak,
                        $vr['tanggal_ukur'],
                        $vr['berat'],
                        $vr['tinggi']
                    );

                    $existingPengukuran = TrxPengukuran::where('id_anak', $anak->id)
                        ->where('tanggal_pengukuran', $vr['tanggal_ukur'])
                        ->first();

                    $pengukuranData = array_merge([
                        'id_anak' => $anak->id,
                        'tanggal_pengukuran' => $vr['tanggal_ukur'],
                        'berat_badan' => $vr['berat'],
                        'tinggi_badan' => $vr['tinggi'],
                        'lingkar_kepala' => $vr['lingkar_kepala'] ?: 0,
                        'lingkar_lengan' => $vr['lingkar_lengan'] ?: 0,
                        'cara_ukur' => $vr['cara_ukur'],
                    ], $zScoreResults);

                    if ($existingPengukuran) {
                        $existingPengukuran->update($pengukuranData);
                    } else {
                        TrxPengukuran::create($pengukuranData);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import transaction failed', ['error' => $e->getMessage()]);
            // Add as a general error
            $this->addIssue(0, '-', '-', 'SISTEM', 'Terjadi kesalahan sistem saat menyimpan data: ' . $e->getMessage(), 'error');
            $this->importedCount = 0;
            $this->updatedCount = 0;
        }
    }

    /**
     * Add a structured issue entry (error or warning).
     */
    protected function addIssue(int $row, ?string $nik, ?string $nama, string $kolom, string $pesan, string $tipe): void
    {
        $this->issues[] = [
            'baris' => $row,
            'nik' => $nik ?: '-',
            'nama' => $nama ?: '-',
            'kolom' => $kolom,
            'pesan' => $pesan,
            'tipe' => $tipe, // 'error' or 'warning'
        ];
    }

    // ... [rest of the parsing methods remain unchanged]

    protected function parseDate($value): ?string
    {
        if (empty($value)) return null;

        $value = trim($value);

        // Handle Excel numeric date serial
        if (is_numeric($value)) {
            try {
                return Carbon::instance(
                    \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
                )->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y', 'Y/m/d', 'd M Y', 'd F Y'];
        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->format('Y-m-d');
            } catch (\Exception $e) {
                continue;
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function parseJenisKelamin($value): ?string
    {
        if (empty($value)) return null;

        $value = strtoupper(trim($value));

        return match ($value) {
            'L', 'LAKI-LAKI', 'LAKI', '1' => 'L',
            'P', 'PEREMPUAN', '2' => 'P',
            default => null,
        };
    }

    protected function buildAlamat(Collection $row): string
    {
        $parts = [];

        $alamat = $this->cleanValue($row['alamat'] ?? null);
        if ($alamat) $parts[] = $alamat;

        $rt = $this->cleanValue($row['rt'] ?? null);
        $rw = $this->cleanValue($row['rw'] ?? null);
        if ($rt || $rw) {
            $rtRw = [];
            if ($rt) $rtRw[] = "RT {$rt}";
            if ($rw) $rtRw[] = "RW {$rw}";
            $parts[] = implode('/', $rtRw);
        }

        $kel = $this->cleanValue($row['kel'] ?? null);
        if ($kel) $parts[] = "Kel. {$kel}";

        return implode(', ', $parts);
    }

    protected function parseNumeric($value): ?float
    {
        if ($value === null || $value === '' || $value === '-') return null;

        $value = str_replace(',', '.', trim($value));
        return is_numeric($value) ? (float) $value : null;
    }

    protected function parseCaraUkur($value): string
    {
        if (empty($value)) return 'Berdiri';

        $value = strtolower(trim($value));

        return match (true) {
            str_contains($value, 'telentang') || str_contains($value, 'terlentang') || str_contains($value, 'tidur') => 'Telentang',
            default => 'Berdiri',
        };
    }

    protected function cleanValue($value): ?string
    {
        if ($value === null || $value === '') return null;
        return trim((string) $value);
    }
}

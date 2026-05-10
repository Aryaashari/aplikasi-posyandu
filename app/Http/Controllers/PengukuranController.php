<?php

namespace App\Http\Controllers;

use App\Models\MdAnak;
use App\Models\TrxPengukuran;
use App\Models\TrxKehadiran;
use App\Services\ZScoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PengukuranController extends Controller
{
    protected $zScoreService;

    public function __construct(ZScoreService $zScoreService)
    {
        $this->zScoreService = $zScoreService;
    }

    public function index()
    {
        $posyanduId = Auth::user()->id_posyandu;
        $pengukuran = TrxPengukuran::with('anak')
            ->whereHas('anak', function ($query) use ($posyanduId) {
                $query->where('id_posyandu', $posyanduId);
            })
            ->latest()
            ->paginate(10);

        return Inertia::render('Pengukuran/Index', [
            'pengukuran' => $pengukuran
        ]);
    }

    public function create()
    {
        $posyanduId = Auth::user()->id_posyandu;
        $children = MdAnak::where('id_posyandu', $posyanduId)->get(['id', 'nama', 'nik', 'tanggal_lahir', 'jenis_kelamin']);

        return Inertia::render('Pengukuran/Create', [
            'children' => $children
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_anak' => ['required', 'exists:md_anak,id'],
            'tanggal_pengukuran' => ['required', 'date'],
            'berat_badan' => ['required', 'numeric', 'min:0'],
            'tinggi_badan' => ['required', 'numeric', 'min:0'],
            'lingkar_kepala' => ['required', 'numeric', 'min:0'],
            'lingkar_lengan' => ['nullable', 'numeric', 'min:0'],
            'cara_ukur' => ['required', 'in:Berdiri,Telentang'],
            'catatan' => ['nullable', 'string'],
        ]);

        $anak = MdAnak::findOrFail($validated['id_anak']);
        
        // Automatic Z-Score Calculation
        $results = $this->zScoreService->calculate(
            $anak, 
            $validated['tanggal_pengukuran'], 
            $validated['berat_badan'], 
            $validated['tinggi_badan']
        );

        $data = array_merge($validated, $results);

        TrxPengukuran::create($data);

        // Auto sync attendance
        $this->syncKehadiran($anak, $validated['tanggal_pengukuran']);

        return redirect()->route('pengukuran.index')->with('success', 'Data pengukuran berhasil disimpan.');
    }

    public function edit(TrxPengukuran $pengukuran)
    {
        $posyanduId = Auth::user()->id_posyandu;
        $children = MdAnak::where('id_posyandu', $posyanduId)->get(['id', 'nama', 'nik', 'tanggal_lahir', 'jenis_kelamin']);

        return Inertia::render('Pengukuran/Edit', [
            'pengukuran' => $pengukuran,
            'children' => $children
        ]);
    }

    public function update(Request $request, TrxPengukuran $pengukuran)
    {
        $validated = $request->validate([
            'id_anak' => ['required', 'exists:md_anak,id'],
            'tanggal_pengukuran' => ['required', 'date'],
            'berat_badan' => ['required', 'numeric', 'min:0'],
            'tinggi_badan' => ['required', 'numeric', 'min:0'],
            'lingkar_kepala' => ['required', 'numeric', 'min:0'],
            'lingkar_lengan' => ['nullable', 'numeric', 'min:0'],
            'cara_ukur' => ['required', 'in:Berdiri,Telentang'],
            'catatan' => ['nullable', 'string'],
        ]);

        $anak = MdAnak::findOrFail($validated['id_anak']);
        
        // Automatic Re-calculation of Z-Score
        $results = $this->zScoreService->calculate(
            $anak, 
            $validated['tanggal_pengukuran'], 
            $validated['berat_badan'], 
            $validated['tinggi_badan']
        );

        $data = array_merge($validated, $results);

        $pengukuran->update($data);

        // Auto sync attendance
        $this->syncKehadiran($anak, $validated['tanggal_pengukuran']);

        return redirect()->route('pengukuran.index')->with('success', 'Data pengukuran berhasil diperbarui.');
    }

    public function destroy(TrxPengukuran $pengukuran)
    {
        $pengukuran->delete();

        return redirect()->route('pengukuran.index')->with('success', 'Data pengukuran berhasil dihapus.');
    }

    private function syncKehadiran($anak, $tanggal)
    {
        $birthDate = \Carbon\Carbon::parse($anak->tanggal_lahir);
        $measureDate = \Carbon\Carbon::parse($tanggal);
        $ageInMonths = (int)$birthDate->diffInMonths($measureDate);

        // Don't sync attendance if child is 59 months or older (Graduated)
        if ($ageInMonths >= 59) {
            return;
        }

        TrxKehadiran::updateOrCreate(
            [
                'id_anak' => $anak->id,
                'id_posyandu' => $anak->id_posyandu,
                'tanggal' => $tanggal,
            ],
            [
                'status_hadir' => true,
                'status' => 'Hadir',
                'keterangan' => 'Hadir otomatis dari pengisian data pengukuran.',
            ]
        );
    }
}

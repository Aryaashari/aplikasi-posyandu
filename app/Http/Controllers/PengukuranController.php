<?php

namespace App\Http\Controllers;

use App\Models\MdAnak;
use App\Models\TrxPengukuran;
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

        return redirect()->route('pengukuran.index')->with('success', 'Data pengukuran berhasil diperbarui.');
    }

    public function destroy(TrxPengukuran $pengukuran)
    {
        $pengukuran->delete();

        return redirect()->route('pengukuran.index')->with('success', 'Data pengukuran berhasil dihapus.');
    }
}

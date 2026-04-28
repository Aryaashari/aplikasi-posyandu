<?php

namespace App\Http\Controllers;

use App\Models\MdAnak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AnakController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $posyanduId = Auth::user()->id_posyandu;

        $anak = MdAnak::where('id_posyandu', $posyanduId)
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('nik', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Anak/Index', [
            'anak' => $anak,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Anak/Form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'string', 'max:16', 'unique:md_anak,nik'],
            'no_kk' => ['required', 'string', 'max:16'],
            'tanggal_lahir' => ['required', 'date'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'nama_ayah' => ['required', 'string', 'max:255'],
            'nama_ibu' => ['required', 'string', 'max:255'],
            'no_telp_ortu' => ['required', 'string', 'max:15'],
            'alamat' => ['required', 'string'],
        ]);

        $validated['id_posyandu'] = Auth::user()->id_posyandu;

        MdAnak::create($validated);

        return redirect()->route('anak.index')->with('success', 'Data anak berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MdAnak $anak)
    {
        $this->authorizeAccess($anak);

        $measurements = \App\Models\TrxPengukuran::where('id_anak', $anak->id)
            ->oldest('tanggal_pengukuran')
            ->get()
            ->map(function ($m) use ($anak) {
                $birthDate = \Carbon\Carbon::parse($anak->tanggal_lahir);
                $measureDate = \Carbon\Carbon::parse($m->tanggal_pengukuran);
                $m->umur_bulan = (int)$birthDate->diffInMonths($measureDate);
                return $m;
            });

        // Fetch WHO standards (BB/U and TB/U) for the child's gender
        $standards = \App\Models\RefGrowthStandard::where('jenis_kelamin', $anak->jenis_kelamin)
            ->orderBy('umur_bulan')
            ->get();

        // Fetch attendance history
        $attendance = \App\Models\TrxKehadiran::where('id_anak', $anak->id)
            ->latest('tanggal')
            ->get();

        // Fetch immunization history
        $imunisasi = \App\Models\TrxImunisasi::where('id_anak', $anak->id)
            ->get();

        return Inertia::render('Anak/Show', [
            'anak' => $anak,
            'measurements' => $measurements,
            'standards' => $standards,
            'attendance' => $attendance,
            'imunisasi' => $imunisasi
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MdAnak $anak)
    {
        $this->authorizeAccess($anak);

        return Inertia::render('Anak/Form', [
            'anak' => $anak,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MdAnak $anak)
    {
        $this->authorizeAccess($anak);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'string', 'max:16', "unique:md_anak,nik,{$anak->id}"],
            'no_kk' => ['required', 'string', 'max:16'],
            'tanggal_lahir' => ['required', 'date'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'nama_ayah' => ['required', 'string', 'max:255'],
            'nama_ibu' => ['required', 'string', 'max:255'],
            'no_telp_ortu' => ['required', 'string', 'max:15'],
            'alamat' => ['required', 'string'],
        ]);

        $anak->update($validated);

        return redirect()->route('anak.index')->with('success', 'Data anak berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MdAnak $anak)
    {
        $this->authorizeAccess($anak);

        $anak->delete();

        return redirect()->route('anak.index')->with('success', 'Data anak berhasil dihapus.');
    }

    /**
     * Ensure the user can only access children in their posyandu.
     */
    protected function authorizeAccess(MdAnak $anak)
    {
        if ($anak->id_posyandu !== Auth::user()->id_posyandu) {
            abort(403);
        }
    }
}

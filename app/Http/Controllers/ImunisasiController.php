<?php

namespace App\Http\Controllers;

use App\Models\TrxImunisasi;
use Illuminate\Http\Request;

class ImunisasiController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_anak' => 'required|exists:md_anak,id',
            'jenis_imunisasi' => 'required|string|max:100',
            'tanggal_imunisasi' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        // Check if already exists to prevent duplicates
        $exists = TrxImunisasi::where('id_anak', $validated['id_anak'])
            ->where('jenis_imunisasi', $validated['jenis_imunisasi'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Imunisasi ini sudah tercatat.');
        }

        TrxImunisasi::create($validated);

        return back()->with('success', 'Berhasil mencatat imunisasi.');
    }
}

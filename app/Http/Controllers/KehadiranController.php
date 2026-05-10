<?php

namespace App\Http\Controllers;

use App\Models\MdAnak;
use App\Models\TrxKehadiran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class KehadiranController extends Controller
{
    public function index(Request $request)
    {
        $posyanduId = Auth::user()->id_posyandu;
        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));

        // Monthly Recap: Summary total attendance per child
        $children = MdAnak::where('id_posyandu', $posyanduId)
            ->withCount(['kehadiran' => function ($query) use ($month, $year) {
                $query->whereMonth('tanggal', $month)
                      ->whereYear('tanggal', $year);
            }])
            ->get(['id', 'nama', 'nik']);

        return Inertia::render('Kehadiran/Index', [
            'recap' => $children,
            'filters' => [
                'month' => (int)$month,
                'year' => (int)$year,
            ]
        ]);
    }

    public function checkIn(Request $request)
    {
        $posyanduId = Auth::user()->id_posyandu;
        $today = date('Y-m-d');

        // Get children and check if they already checked in today
        $children = MdAnak::where('id_posyandu', $posyanduId)
            ->when($request->search, function ($query, $search) {
                $query->where('nama', 'like', "%{$search}%")
                      ->orWhere('nik', 'like', "%{$search}%");
            })
            ->with(['kehadiran' => function ($query) use ($today) {
                $query->where('tanggal', $today);
            }])
            ->get(['id', 'nama', 'nik', 'tanggal_lahir']);

        // Format to easily check status in frontend and filter graduated children
        $todayObj = Carbon::now();
        $children = $children->filter(function ($child) use ($todayObj) {
            $birthDate = Carbon::parse($child->tanggal_lahir);
            return $birthDate->diffInMonths($todayObj) < 59;
        })->values();

        $children->each(function ($child) {
            $child->is_present = $child->kehadiran->isNotEmpty();
            unset($child->kehadiran); // Clean up
        });

        return Inertia::render('Kehadiran/CheckIn', [
            'children' => $children,
            'filters' => $request->only(['search']),
            'today' => $today
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_anak' => 'required|exists:md_anak,id',
        ]);

        $posyanduId = Auth::user()->id_posyandu;
        $today = date('Y-m-d');

        // Check for duplicate
        $exists = TrxKehadiran::where('id_anak', $validated['id_anak'])
            ->where('tanggal', $today)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Anak sudah dicatat hadir hari ini.');
        }

        TrxKehadiran::create([
            'id_anak' => $validated['id_anak'],
            'id_posyandu' => $posyanduId,
            'tanggal' => $today,
            'waktu_checkin' => date('H:i:s'),
            'status_hadir' => true,
            'status' => 'Hadir',
            'keterangan' => 'Check-in cepat'
        ]);

        return back()->with('success', 'Berhasil mencatat kehadiran balita.');
    }
}

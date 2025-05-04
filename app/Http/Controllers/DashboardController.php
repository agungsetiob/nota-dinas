<?php

namespace App\Http\Controllers;

use App\Models\NotaDinas;
use App\Models\User;
use App\Models\Skpd;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $role = auth()->user()->role;
        $totalUsers = User::count();
        $totalSkpds = Skpd::count();
        $notaDinas = NotaDinas::count();
        $notaSelesai = NotaDinas::where('tahap_saat_ini', 'selesai')->count();
        //sleep(1);
        return match ($role) {
            'admin' => Inertia::render('Dashboard/Admin', [
                'totalUsers' => $totalUsers,
                'totalSkpds' => $totalSkpds,
                'notaDinas' => $notaDinas,
                'notaSelesai' => $notaSelesai,
            ]),
            'skpd' => Inertia::render('Dashboard/Skpd', [
                'totalUsers' => $totalUsers,
                'totalSkpds' => $totalSkpds,
                'notaDinas' => $notaDinas,
                'notaSelesai' => $notaSelesai,
            ]),
            'asisten' => Inertia::render('Dashboard/Asisten', [
                'totalUsers' => $totalUsers,
                'totalSkpds' => $totalSkpds,
                'notaDinas' => $notaDinas,
                'notaSelesai' => $notaSelesai,
            ]),
            'sekda' => Inertia::render('Dashboard/Sekda', [
                'totalUsers' => $totalUsers,
                'totalSkpds' => $totalSkpds,
                'notaDinas' => $notaDinas,
                'notaSelesai' => $notaSelesai,
            ]),
            'bupati' => Inertia::render('Dashboard/Bupati', [
                'totalUsers' => $totalUsers,
                'totalSkpds' => $totalSkpds,
                'notaDinas' => $notaDinas,
                'notaSelesai' => $notaSelesai,
            ]),
            default => abort(403),
        };
    }
    public function getNotaPerYear()
    {
        $startYear = Carbon::now()->subYears(4)->year;
    
        $data = DB::table('nota_dinas')
            ->selectRaw('YEAR(tanggal_pengajuan) as year, COUNT(*) as total')
            ->whereYear('tanggal_pengajuan', '>=', $startYear)
            ->groupBy('year')
            ->orderBy('year', 'asc')
            ->get();
    
        return response()->json($data);
    }
    public function getApprovedNotaDinasBySkpd():JsonResponse
    {
        $data = NotaDinas::where('status', 'disetujui')
            ->with('skpd:id,nama_skpd')
            ->selectRaw('skpd_id, COUNT(*) as total')
            ->groupBy('skpd_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($notaDinas) {
                return [
                    'nama_skpd' => $notaDinas->skpd->nama_skpd,
                    'total' => $notaDinas->total
                ];
            });

        return response()->json($data);
    }
    public function getNotaDinasByStage()
    {
        $data = DB::table('nota_dinas')
            ->selectRaw('tahap_saat_ini, COUNT(*) as total')
            ->groupBy('tahap_saat_ini')
            ->orderBy('total', 'desc')
            ->get();
    
        return response()->json($data);
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\NotaDinas;
use App\Models\User;
use App\Models\Skpd;
use Inertia\Inertia;
use Inertia\Response;

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
}

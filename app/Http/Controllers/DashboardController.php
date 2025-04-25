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

        return match ($role) {
            'admin' => Inertia::render('Dashboard/Admin', [
                'totalUsers' => $totalUsers,
                'totalSkpds' => $totalSkpds,
                'notaDinas' => $notaDinas,
                'notaSelesai' => $notaSelesai,
            ]),
            'skpd' => Inertia::render('Dashboard/Skpd'),
            'asisten' => Inertia::render('Dashboard/Asisten'),
            'sekda' => Inertia::render('Dashboard/Sekda'),
            'bupati' => Inertia::render('Dashboard/Bupati'),
            default => abort(403),
        };
    }
}

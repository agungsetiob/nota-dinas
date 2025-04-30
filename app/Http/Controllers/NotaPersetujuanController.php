<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\NotaDinas;
use Inertia\Inertia;

class NotaPersetujuanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
    
        if ($user->role == 'skpd') {
            $notas = NotaDinas::whereHas('persetujuans')
                ->where('skpd_id', $user->skpd_id)
                ->with(['persetujuans', 'skpd'])
                ->paginate(10);
        } elseif ($user->role == 'asisten') {
            $notas = NotaDinas::whereHas('persetujuans')
                ->where('asisten_id', $user->id)
                ->with(['persetujuans', 'skpd'])
                ->paginate(10);
        } else {
            $notas = NotaDinas::whereHas('persetujuans')
                ->with(['persetujuans', 'skpd'])
                ->paginate(10);
        }
    
        return Inertia::render('NotaDinas/Approval', [
            'notas' => $notas,
            'userRole' => $user->role
        ]);
    }
    
    public function approvalHistories($notaId)
    {
        try {
            $nota = NotaDinas::findOrFail($notaId);
            $persetujuanHistories = $nota->persetujuans()
                ->with(['skpd', 'approver'])
                ->latest('tanggal_update')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mendapat data persetujuan',
                'data' => $persetujuanHistories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
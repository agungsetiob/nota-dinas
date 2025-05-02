<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\NotaDinas;
use Inertia\Inertia;

class NotaPersetujuanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->query('search');
    
        $query = NotaDinas::latest()->whereHas('persetujuans');
    
        if ($user->role === 'skpd') {
            $query->where('skpd_id', $user->skpd_id);
        } elseif ($user->role === 'asisten') {
            $query->where('asisten_id', $user->id);
        }
    
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('perihal', 'like', "%{$search}%")
                  ->orWhere('nomor_nota', 'like', "%{$search}%");
            });
        }
    
        $notas = $query->with(['persetujuans', 'skpd'])->paginate(10);
    
        return Inertia::render('NotaDinas/Approval', [
            'notas'    => $notas,
            'userRole' => $user->role,
            'search' => $search,
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
<?php

namespace App\Http\Controllers;

use App\Models\Skpd;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SkpdController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Skpd::with('asisten');
    
        if ($search = $request->search) {
            $query->where('nama_skpd', 'like', '%' . $search . '%');
        }
    
        $skpds = $query->paginate(10);
        $asistens = User::where('role', 'asisten')->where('status', 1)->get();
    
        return Inertia::render('Skpds/Index', [
            'skpds' => $skpds,
            'asistens' => $asistens
        ]);
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_skpd' => 'required|string|max:255',
            'asisten_id' => 'nullable|exists:users,id',
        ]);

        Skpd::create([
            'nama_skpd' => $validated['nama_skpd'],
            'status' => true,
            'asisten_id' => $validated['asisten_id'] ?? null,
        ]);

        return back()->with('success', 'SKPD berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Skpd $skpd)
    {
        $validated = $request->validate([
            'nama_skpd' => 'required|string|max:255',
            'asisten_id' => 'nullable|exists:users,id',
        ]);

        $skpd->update([
            'nama_skpd' => $validated['nama_skpd'],
            'asisten_id' => $validated['asisten_id'] ?? null,
        ]);

        return back()->with('success', 'SKPD berhasil diupdate.');
    }

    /**
     * Toggle the status of a SKPD.
     */
    public function toggleStatus(Skpd $skpd)
    {
        $skpd->update(['status' => !$skpd->status]);

        return back()->with('success', 'Status SKPD berhasil diubah.');
    }
}

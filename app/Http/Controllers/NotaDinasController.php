<?php

namespace App\Http\Controllers;

use App\Models\NotaDinas;
use App\Models\NotaPengiriman;
use App\Models\NotaPersetujuan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotaDinasController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        $query = NotaDinas::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_nota', 'like', "%$search%")
                ->orWhere('perihal', 'like', "%$search%")
                ->orWhere('tahap_saat_ini', 'like', "%$search%");
            });
        }

        switch ($user->role) {
            case 'skpd':
                $query->where('skpd_id', $user->skpd_id)
                    ->whereIn('status', ['draft', 'dikembalikan', 'proses']);
                break;
            case 'asisten':
                $query->whereHas('skpd', function ($q) use ($user) {
                    $q->where('asisten_id', $user->id);
                })->where('status', 'proses')
                ->where('tahap_saat_ini', 'asisten');
                break;
            case 'sekda':
                $query->where('tahap_saat_ini', 'sekda')
                    ->where('status', 'proses');
                break;
            case 'bupati':
                $query->where('tahap_saat_ini', 'bupati')
                    ->where('status', 'proses');
                break;
            case 'admin':
                // Admin can see all data
                break;
            default:
                return abort(403, 'Akses tidak diizinkan');
        }

        $notas = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('NotaDinas/Index', [
            'notas' => $notas,
            'search' => $search,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_nota' => 'required|string|max:255',
            'perihal' => 'required|string|max:255',
            'anggaran' => 'nullable|numeric',
            'tanggal_pengajuan' => 'required|date',
            //'lampiran.*' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $nota = NotaDinas::create([
            'skpd_id' => auth()->user()->skpd_id,
            'nomor_nota' => $validated['nomor_nota'],
            'perihal' => $validated['perihal'],
            'anggaran' => $validated['anggaran'],
            'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
            'status' => 'draft',
            'tahap_saat_ini' => 'skpd',
            'asisten_id' => auth()->user()->skpd->asisten_id ?? null,
        ]);

        return redirect()->route('nota-dinas.index')
                         ->with('success', 'Nota berhasil dibuat.');
    }

    public function update(Request $request, NotaDinas $notaDina)
    {
        if (!in_array($notaDina->status, ['draft', 'dikembalikan'])) {
            return redirect()->route('nota-dinas.index')
                             ->with('error', 'Nota hanya bisa diperbarui jika berstatus draft atau dikembalikan.');
        }
    
        $validated = $request->validate([
            'nomor_nota' => 'required|string|max:255',
            'perihal' => 'required|string|max:255',
            'anggaran' => 'nullable|numeric',
            'tanggal_pengajuan' => 'required|date',
            //'lampiran.*' => 'nullable|file|mimes:pdf|max:2048',
        ]);
    
        DB::transaction(function () use ($notaDina, $validated, $request) {
            $notaDina->update([
                'nomor_nota' => $validated['nomor_nota'],
                'perihal' => $validated['perihal'],
                'anggaran' => $validated['anggaran'],
                'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
                'asisten_id' => auth()->user()->skpd->asisten_id ?? null,
            ]);
    
            /* Uncomment and update if handling lampiran files in update
            if ($request->hasFile('lampiran')) {
                foreach ($request->file('lampiran') as $file) {
                    $path = $file->store('lampiran_nota', 'public');
    
                    NotaLampiran::create([
                        'nota_dinas_id' => $notaDina->id,
                        'nama_file' => $file->getClientOriginalName(),
                        'path' => $path,
                    ]);
                }
            }
            */
        });
        sleep(1);
        return redirect()->route('nota-dinas.index')
                         ->with('success', 'Nota berhasil diperbarui.');
    }    

    public function destroy(NotaDinas $notaDina)
    {
        if (!in_array($notaDina->status, ['draft', 'dikembalikan'])) {
            return redirect()->route('nota-dinas.index')
                             ->with('error', 'Nota hanya bisa dihapus jika berstatus draft atau dikembalikan.');
        }
        foreach ($notaDina->lampirans as $lampiran) {
            Storage::delete('storage/' . $lampiran->path);
        }
        $notaDina->delete();

        return redirect()->route('nota-dinas.index')
                         ->with('success', 'Nota berhasil dihapus');
    }

    public function getLampiran($id)
    {
        $notaDinas = NotaDinas::with('lampirans')->findOrFail($id);

        $lampirans = $notaDinas->lampirans
            ->sortByDesc('created_at')
            ->map(function ($lampiran) {
                return [
                    'name' => $lampiran->nama_file,
                    'url' => asset('storage/' . $lampiran->path),
                    'created_at' => $lampiran->created_at,
                ];
            })->values();

        return response()->json([
            'success' => true,
            'data' => $lampirans
        ]);
    }


    public function getLampiranHistori($tipe, $id)
    {
        $pengiriman = NotaPengiriman::findOrFail($id);
        $lampirans = $pengiriman->lampirans->map(function($lampiran){
            return [
                'name' => $lampiran->nama_file,
                'url'  => asset('storage/' . $lampiran->path),
                'created_at' => $lampiran->created_at,
            ];
        })->values();
        return response()->json([
            'success' => true,
            'data' => $lampirans
        ]);
    }
    
    public function approveOrRejectNota(Request $request, $notaId)
    {
        if (auth()->user()->role !== 'bupati') {
            return redirect()->route('nota-dinas.index')
                             ->with('error', 'Hanya Bupati yang dapat menyetujui atau menolak Nota Dinas.');
        }
        //sleep(5);
        $notaDinas = NotaDinas::findOrFail($notaId);
        $status = $request->status === 'disetujui' ? 'disetujui' : 'ditolak';

        $catatan = $request->filled('catatan')
            ? $request->catatan
            : "Nota telah {$status} oleh Bupati.";

        $notaDinas->update([
            'status' => $status,
            'tahap_saat_ini' => 'selesai',
        ]);

        NotaPengiriman::create([
            'nota_dinas_id' => $notaDinas->id,
            'dikirim_dari' => 'bupati',
            'dikirim_ke' => 'selesai',
            'pengirim_id' => auth()->user()->id,
            'tanggal_kirim' => now(),
            'catatan' => $catatan,
        ]);

        NotaPersetujuan::create([
            'nota_dinas_id' => $notaDinas->id,
            'approver_id' => auth()->user()->id,
            'skpd_id' => $notaDinas->skpd_id,
            'role_approver' => auth()->user()->role,
            'urutan' => 3,
            'status' => $status,
            'catatan_terakhir' => $catatan,
            'tanggal_update' => now(),
        ]);

        return redirect()->route('nota-dinas.index')
                         ->with('success', "Nota telah {$status} dan diperbarui.");
    }
}

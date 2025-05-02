<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Skpd;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the list of users.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $query = User::with('skpd');
    
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            })
            ->orWhereHas('skpd', function ($q) use ($search) {
                $q->where('nama_skpd', 'like', "%$search%");
            });
        }
    
        $users = $query->latest()->paginate(10);
    
        return Inertia::render('Users/Index', [
            'users' => $users,
            'search' => $search,
        ]);
    }
    

    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        $skpds = Skpd::all();
        return Inertia::render('Auth/Register', [
            'skpds' => $skpds,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'digits:16', 'unique:users'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', Rule::in(['skpd', 'asisten', 'sekda', 'bupati', 'admin'])],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'skpd_id' => ['nullable', 'exists:skpds,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'jabatan' => $validated['jabatan'] ?? null,
            'skpd_id' => $validated['skpd_id'] ?? null,
            'nik' => $validated['nik'],
        ]);

        event(new Registered($user));

        return redirect()->route('users.index')->with('success', 'Berhasil simpan pengguna baru.');
    }

    /**
     * Toggle the user's status.
     */
    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'boolean'],
        ]);

        $user->update([
            'status' => $validated['status'],
        ]);

        return redirect()->back()->with('success', 'Status pengguna berhasil diperbarui.');
    }
}

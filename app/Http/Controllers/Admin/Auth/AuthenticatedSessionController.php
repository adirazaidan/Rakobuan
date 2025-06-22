<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function create()
    {
        return view('admin.auth.login');
    }

    /**
     * Menangani permintaan login yang masuk.
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Coba untuk mengautentikasi pengguna
        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            // 3. Jika berhasil, regenerate session
            $request->session()->regenerate();
            
            // 4. Redirect ke halaman yang dimaksud sebelumnya, atau ke dashboard admin
            return redirect()->intended(route('admin.dashboard'));
        }

        // 5. Jika gagal, kembali ke halaman login dengan pesan error
        return back()->withErrors([
            'email' => 'Email atau Password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/'); // Redirect ke landing page setelah logout
    }
}
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'username' => ['required', 'string'],
                'password' => ['required', 'string'],
            ], [
                'username.required' => 'Username wajib diisi.',
                'password.required' => 'Password wajib diisi.',
            ]);

            if (Auth::attempt(
                ['username' => $credentials['username'], 'password' => $credentials['password'], 'is_active' => true],
                $request->boolean('remember')
            )) {
                $request->session()->regenerate();
                return redirect()->intended(route('dashboard'));
            }

            return back()->withErrors([
                'username' => 'Username atau password salah, atau akun tidak aktif.',
            ])->onlyInput('username');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Login error: ' . $e->getMessage());
            return response('Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(), 500);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }
}


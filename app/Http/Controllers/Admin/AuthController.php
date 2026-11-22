<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private const MAX_ATTEMPTS = 5;

    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email belum benar.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        // Batasi percobaan: 5 kali gagal per email + IP, lalu tunggu 1 menit
        $key = Str::transliterate(Str::lower($data['email']) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withInput($request->only('email'))
                ->withErrors(['email' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik."]);
        }

        // is_admin ikut diperiksa, jadi akun biasa mendapat pesan gagal yang sama
        $ok = Auth::attempt(
            ['email' => $data['email'], 'password' => $data['password'], 'is_admin' => true],
            $request->boolean('remember')
        );

        if (! $ok) {
            RateLimiter::hit($key, 60);

            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau kata sandi tidak cocok.']);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'productCount' => Product::query()->count(),
            'availableProductCount' => Product::query()->where('is_available', true)->count(),
            'categoryCount' => Category::query()->count(),
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}

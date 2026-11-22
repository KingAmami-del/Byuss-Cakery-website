<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/** Hanya pengguna yang sudah login DAN bertanda is_admin yang boleh masuk halaman admin. */
class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user || ! $user->is_admin) {
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}

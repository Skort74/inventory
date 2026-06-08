<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Cek apakah user sudah login dan punya role yang sesuai
        if ($request->user() && in_array($request->user()->role, $roles)) {
            return $next($request);
        }

        // Jika tidak punya akses, lempar error 403
        return response()->json(['message' => 'Forbidden: Lu gak punya akses ke sini, bos!'], 403);
    }
}
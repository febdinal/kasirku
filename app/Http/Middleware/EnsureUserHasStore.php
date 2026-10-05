<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasStore
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->currentStoreId()) {
            return $next($request);
        }

        // Admin murni (tanpa toko) hanya mengelola user.
        if ($user?->is_admin) {
            return redirect()->route('admin.users.index');
        }

        abort(403, 'Akun Anda belum memiliki toko.');
    }
}

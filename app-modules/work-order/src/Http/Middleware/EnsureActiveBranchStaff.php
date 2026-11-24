<?php

namespace Modules\WorkOrder\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveBranchStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active) {
            abort(403, 'Akun Anda tidak aktif.');
        }

        if ($user->branch_id === null && ! $user->hasRole('super_admin')) {
            abort(403, 'Akun staf operasional wajib terdaftar pada salah satu cabang.');
        }

        return $next($request);
    }
}

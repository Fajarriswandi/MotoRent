<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $module, $action = 'read'): Response
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        $permission = $user->userPermissions()
            ->whereHas('permission', fn($q) => $q->where('module', $module))
            ->first();

        $can = match ($action) {
            'read' => $permission?->can_read ?? false,
            'create' => $permission?->can_create ?? false,
            'edit' => $permission?->can_edit ?? false,
            'delete' => $permission?->can_delete ?? false,
            default => false,
        };

        if (!$can) {
            abort(403, 'Anda tidak memiliki izin untuk ' . $action . ' modul ini.');
        }

        return $next($request);
    }
}

<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('canAccess')) {
    function canAccess(string $module, string $action = 'read'): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        $permission = $user->userPermissions()
            ->whereHas('permission', fn($q) => $q->where('module', $module))
            ->first();

        return match ($action) {
            'read' => $permission?->can_read ?? false,
            'create' => $permission?->can_create ?? false,
            'edit' => $permission?->can_edit ?? false,
            'delete' => $permission?->can_delete ?? false,
            default => false,
        };
    }
}

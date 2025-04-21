<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Permission;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::whereIn('role', ['admin', 'manager'])->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $permissions = Permission::all();
        $user = new User(); // user kosong untuk create form
        return view('users.create', compact('user', 'permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users',
            'password'       => 'required|min:6|confirmed',
            'role'           => ['required', Rule::in(['admin', 'manager'])],
            'phone'          => 'nullable|string|max:255',
            'address'        => 'nullable|string',
            'profile_photo'  => 'nullable|image|max:2048',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('profile_photos', 'public');
        }

        $user = User::create($validated);

        $this->syncPermissions($request, $user, $validated['role']);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $permissions = Permission::all();
        $user->load('userPermissions');

        return view('users.edit', compact('user', 'permissions'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'           => ['required', Rule::in(['admin', 'manager'])],
            'phone'          => 'nullable|string|max:255',
            'address'        => 'nullable|string',
            'password'       => 'nullable|min:6|confirmed',
            'profile_photo'  => 'nullable|image|max:2048',
        ]);

        // ⛔ Cegah siapapun menurunkan role user admin (termasuk user admin lainnya)
        if (
            $user->role === 'admin' &&
            $request->role !== 'admin' &&
            auth()->user()->role === 'admin'
        ) {
            return back()->with('error', 'Anda tidak diizinkan mengubah role user admin.');
        }

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('profile_photos', 'public');
        }

        // ⛔ Paksa tetap pakai role asli kalau user mengedit dirinya sendiri
        if ($user->id === auth()->id()) {
            $validated['role'] = $user->role;
        }

        $user->update($validated);

        $this->syncPermissions($request, $user, $validated['role']);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    /**
     * Sinkronisasi permission user berdasarkan role
     */
    private function syncPermissions(Request $request, User $user, $role)
    {
        // Hapus semua permission lama
        $user->userPermissions()->delete();

        if ($role === 'admin') {
            $permissions = Permission::all();
            foreach ($permissions as $permission) {
                UserPermission::create([
                    'user_id'       => $user->id,
                    'permission_id' => $permission->id,
                    'can_read'      => true,
                    'can_create'    => true,
                    'can_edit'      => true,
                    'can_delete'    => true,
                ]);
            }
        } else {
            $submittedPermissions = $request->input('permissions', []);
            foreach ($submittedPermissions as $permissionId => $actions) {
                UserPermission::create([
                    'user_id'       => $user->id,
                    'permission_id' => $permissionId,
                    'can_read'      => isset($actions['can_read']),
                    'can_create'    => isset($actions['can_create']),
                    'can_edit'      => isset($actions['can_edit']),
                    'can_delete'    => isset($actions['can_delete']),
                ]);
            }
        }
    }
}

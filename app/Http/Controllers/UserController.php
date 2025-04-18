<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\UserPermission;
use App\Models\Permission;

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
    $user = new User(); // kosongkan model
    return view('users.create', compact('user', 'permissions'));
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role' => ['required', Rule::in(['admin', 'manager'])],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $validated['profile_photo'] = $file->store('profile_photos', 'public');
        }



        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $permissions = Permission::all();
        $user->load('userPermissions'); // supaya data hak akses terload

        return view('users.edit', compact('user', 'permissions'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'manager'])],
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'password' => 'nullable|min:6|confirmed',
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('profile_photos', 'public');
        }

        $user->update($validated);

        // ✅ Simpan permissions
        $submittedPermissions = $request->input('permissions', []);

        // Hapus permission lama user
        $user->userPermissions()->delete();

        // Simpan ulang
        foreach ($submittedPermissions as $permissionId => $actions) {
            UserPermission::create([
                'user_id' => $user->id,
                'permission_id' => $permissionId,
                'can_read' => isset($actions['can_read']),
                'can_create' => isset($actions['can_create']),
                'can_edit' => isset($actions['can_edit']),
                'can_delete' => isset($actions['can_delete']),
            ]);
        }

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }


    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}

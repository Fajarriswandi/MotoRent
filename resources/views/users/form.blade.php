@csrf
<div class="mb-3">
    <label>Nama</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}">
</div>

<div class="mb-3">
    <label>Email</label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}">
</div>

<div class="mb-3">
    <label>No. HP</label>
    <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone ?? '') }}">
</div>

<div class="mb-3">
    <label>Alamat</label>
    <textarea name="address" class="form-control">{{ old('address', $user->address ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label>Role</label>
    <select name="role" class="form-control">
        <option value="admin" {{ old('role', $user->role ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
        <option value="manager" {{ old('role', $user->role ?? '') == 'manager' ? 'selected' : '' }}>Manager</option>
    </select>
</div>

<div class="mb-3">
    <label>Foto Profil</label><br>
    @if (isset($user) && $user->profile_photo)
        <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Foto Profil" width="100" class="mb-2"><br>
    @endif
    <input type="file" name="profile_photo" class="form-control">
</div>

<div class="mb-3">
    <label>Password {{ isset($user) ? '(Kosongkan jika tidak ganti)' : '' }}</label>
    <input type="password" name="password" class="form-control">
</div>

<div class="mb-3">
    <label>Konfirmasi Password</label>
    <input type="password" name="password_confirmation" class="form-control">
</div>

{{-- Permission Matrix --}}
<div class="card mt-4">
    <div class="card-header"><strong>Hak Akses (Permissions)</strong></div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-sm">
            <thead class="table-light">
                <tr>
                    <th>Modul</th>
                    <th class="text-center">Read</th>
                    <th class="text-center">Create</th>
                    <th class="text-center">Edit</th>
                    <th class="text-center">Delete</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($permissions as $permission)
                    @php
                        $userPermission = $user->userPermissions->firstWhere('permission_id', $permission->id);
                    @endphp
                    <tr>
                        <td>{{ $permission->name }}</td>
                        <td class="text-center">
                            <input type="checkbox" name="permissions[{{ $permission->id }}][can_read]" value="1"
                                {{ $userPermission && $userPermission->can_read ? 'checked' : '' }}>
                        </td>
                        <td class="text-center">
                            <input type="checkbox" name="permissions[{{ $permission->id }}][can_create]" value="1"
                                {{ $userPermission && $userPermission->can_create ? 'checked' : '' }}>
                        </td>
                        <td class="text-center">
                            <input type="checkbox" name="permissions[{{ $permission->id }}][can_edit]" value="1"
                                {{ $userPermission && $userPermission->can_edit ? 'checked' : '' }}>
                        </td>
                        <td class="text-center">
                            <input type="checkbox" name="permissions[{{ $permission->id }}][can_delete]" value="1"
                                {{ $userPermission && $userPermission->can_delete ? 'checked' : '' }}>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4 d-flex justify-content-between">
    <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
    <button type="submit" class="btn btn-success">
        Simpan
    </button>
</div>

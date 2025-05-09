@php
$isEditingSelf = isset($user) && $user->id && auth()->check() && auth()->id() === $user->id;
$isEditingOtherAdmin = isset($user) && $user->role === 'admin' && !$isEditingSelf;
$isEditingSelfAdmin = $isEditingSelf && ($user->role === 'admin');
$selectedRole = old('role', $user->role ?? '');

$isEdit = isset($user) && $user->exists;
@endphp

@if (session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

@csrf


<div class="headerForm">
    <div class="content container">
        <div>
            <h4>{{ $isEdit ? 'Edit data' : 'Add new' }} User</h4>
            <p>Please fill in all the details.</p>
        </div>
        <div>
            <div class="mt-4 d-flex justify-content-between">
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary me-3">Cancel</a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <span class="spinner-border spinner-border-sm d-none" id="submitSpinner" role="status" aria-hidden="true"></span>
                    <span id="submitText">{{ $isEdit ? 'Confirm & Update' : 'Confirm & Submit' }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Form User -->
<div class="container mt-5">
    <div class="card widgetCard">
        <div class="card-body">
            <!-- Form -->

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Nama</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>No. HP</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone ?? '') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Alamat</label>
                    <textarea name="address" class="form-control">{{ old('address', $user->address ?? '') }}</textarea>
                </div>

                <div class="col-md-6 mb-3">
                    <select name="role" class="form-control" id="select-role"
                        {{ ($isEditingSelfAdmin || $isEditingOtherAdmin) ? 'disabled' : '' }}>
                        <option value="admin" {{ $selectedRole === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="manager" {{ $selectedRole === 'manager' ? 'selected' : '' }}>Manager</option>
                    </select>

                    @if ($isEditingSelfAdmin || $isEditingOtherAdmin)
                    <input type="hidden" name="role" value="admin">
                    @endif
                </div>

                <div class="col-md-6 mb-3">
                    <label>Foto Profil</label><br>
                    @if (isset($user) && $user->profile_photo)
                    <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Foto Profil" width="100" class="mb-2"><br>
                    @endif
                    <input type="file" name="profile_photo" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Password {{ isset($user) ? '(Kosongkan jika tidak ganti)' : '' }}</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>            
            </div>
            <!-- End Form -->
        </div>
    </div>
</div>
<!-- Form User -->

{{-- Permission Matrix --}}
<div class="container mt-5">
    <div class="card widgetCard">
        <div class="card-body">
            <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                    <thead class="tableHeader">
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
                        $isCheckedRead = ($userPermission && $userPermission->can_read) || $selectedRole === 'admin';
                        $isCheckedCreate = ($userPermission && $userPermission->can_create) || $selectedRole === 'admin';
                        $isCheckedEdit = ($userPermission && $userPermission->can_edit) || $selectedRole === 'admin';
                        $isCheckedDelete = ($userPermission && $userPermission->can_delete) || $selectedRole === 'admin';
                        $isPermissionDisabled = $selectedRole === 'admin' || $isEditingOtherAdmin;
                        @endphp
                        <tr>
                            <td>{{ $permission->name }}</td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[{{ $permission->id }}][can_read]" value="1"
                                    {{ $isCheckedRead ? 'checked' : '' }}
                                    {{ $isPermissionDisabled ? 'disabled' : '' }}>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[{{ $permission->id }}][can_create]" value="1"
                                    {{ $isCheckedCreate ? 'checked' : '' }}
                                    {{ $isPermissionDisabled ? 'disabled' : '' }}>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[{{ $permission->id }}][can_edit]" value="1"
                                    {{ $isCheckedEdit ? 'checked' : '' }}
                                    {{ $isPermissionDisabled ? 'disabled' : '' }}>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[{{ $permission->id }}][can_delete]" value="1"
                                    {{ $isCheckedDelete ? 'checked' : '' }}
                                    {{ $isPermissionDisabled ? 'disabled' : '' }}>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('select-role');
        const checkboxes = document.querySelectorAll('input[type="checkbox"][name^="permissions"]');

        // 🔄 Simpan status awal checked masing-masing checkbox
        const originalStates = {};
        checkboxes.forEach(cb => {
            originalStates[cb.name] = cb.checked;
        });

        function togglePermissionCheckboxes(role) {
            checkboxes.forEach(cb => {
                if (role === 'admin') {
                    cb.checked = true;
                    cb.disabled = true;
                } else {
                    cb.checked = originalStates[cb.name]; // ✅ Kembalikan ke status awal
                    cb.disabled = false;
                }
            });
        }

        if (roleSelect) {
            togglePermissionCheckboxes(roleSelect.value);

            roleSelect.addEventListener('change', function() {
                togglePermissionCheckboxes(this.value);
            });
        }
    });
</script>
@endpush
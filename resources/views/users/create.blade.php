@extends('layouts.app')

@section('content')

<div class="headerContent">
    <h3>Tambah User</h3>
</div>

<div class="mainContent">
    <form id="userForm" action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
        @include('users.form', ['user' => $user, 'permissions' => $permissions])

        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const roleSelect = document.getElementById('select-role');

                function updatePermissionsBasedOnRole(role) {
                    const checkboxes = document.querySelectorAll('input[type="checkbox"][name^="permissions"]');
                    checkboxes.forEach(cb => {
                        cb.disabled = (role === 'admin');
                        cb.checked = (role === 'admin');
                    });
                }

                // Cek awal saat halaman load
                updatePermissionsBasedOnRole(roleSelect.value);

                // Saat role berubah
                roleSelect.addEventListener('change', function() {
                    updatePermissionsBasedOnRole(this.value);
                });
            });
        </script>
        @endpush
    </form>
</div>



@endsection

@push('scripts')
<script>
    document.getElementById('userForm').addEventListener('submit', function() {
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = 'Loading...';
    });
</script>
@endpush
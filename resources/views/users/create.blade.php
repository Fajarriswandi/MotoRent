@extends('layouts.app')

@section('content')
    <h3>Tambah User</h3>
    <form id="userForm" action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
        @include('users.form', ['user' => $user, 'permissions' => $permissions])
    </form>
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

@extends('layouts.app')

@section('content')

    <div class="container-fluid p-0">
        <form id="userForm" action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')
            @include('users.form', ['user' => $user, 'permissions' => $permissions])
        </form>
    </div>



@endsection

@push('scripts')
    <script>
        document.getElementById('userForm').addEventListener('submit', function () {
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = 'Loading...';
        });
    </script>
@endpush
@extends('layouts.app')

@section('content')

<div class="headerContent">
    <h3>Manajemen User</h3>
    @if (canAccess('users', 'create'))
    <a href="{{ route('users.create') }}" class="btn btn-primary">+ Tambah User</a>
    @endif
</div>

<div class="mainContent">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Foto</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($users as $user)
            <tr>
                <td>
                    @if ($user->profile_photo)
                    <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Foto Profil" width="50" height="50"
                        style="object-fit: cover; border-radius: 50%;">
                    @else
                    <span class="text-muted">-</span>
                    @endif
                </td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ ucfirst($user->role) }}</td>
                <td>
                    @if (canAccess('users', 'edit'))
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning">Edit</a>
                    @endif
                    {{-- Tombol hapus dengan konfirmasi SweetAlert --}}

                    {{-- Tombol hapus dengan konfirmasi SweetAlert --}}
                    @if (canAccess('users', 'delete'))
                    <form method="POST" action="{{ route('users.destroy', $user) }}">
                        @csrf @method('DELETE')
                        <button type="button" class="btn btn-sm btn-danger btn-delete" data-name="{{ $user->name }}"
                            data-url="{{ route('users.destroy', $user) }}">
                            Hapus
                        </button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>






@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tombol delete
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {
                const userName = this.dataset.name;
                const deleteUrl = this.dataset.url;

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: `User "${userName}" akan dihapus permanen.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = deleteUrl;

                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = '{{ csrf_token() }}';

                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';

                        form.appendChild(csrfInput);
                        form.appendChild(methodInput);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });
    });
</script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: "{{ session('success') }}",
        showConfirmButton: true,
        timer: 4000
    });
</script>
@endif
@endpush
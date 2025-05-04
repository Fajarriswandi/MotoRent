@extends('layouts.app')

@section('content')

    <div class="container-fluid  mt-5 pt-5">
        <div class="card widgetCard">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">

                    <!-- Title -->
                    <div>
                        <h3 class="titlePage">Users</h3>
                    </div>

                    <!-- Right Button -->
                    @if (canAccess('users', 'create'))
                        <a href="{{ route('users.create') }}" class="btn btn-primary">+ Tambah User</a>
                    @endif

                </div>

                <table class="table table-striped table-hover align-middle">
                    <thead class="tableHeader">
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
                                        <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Foto Profil" width="50"
                                            height="50" style="object-fit: cover; border-radius: 50%;">
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ ucfirst($user->role) }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border" type="button"
                                            id="dropdownUser{{ $user->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownUser{{ $user->id }}">
                                            @if (canAccess('users', 'edit'))
                                                <li>
                                                    <a href="{{ route('users.edit', $user) }}" class="dropdown-item">
                                                        <i class="bi bi-pencil-square me-1"></i> Edit
                                                    </a>
                                                </li>
                                            @endif

                                            @if (canAccess('users', 'delete'))
                                                <li>
                                                    <button type="button" class="dropdown-item text-danger btn-delete"
                                                        data-name="{{ $user->name }}"
                                                        data-url="{{ route('users.destroy', $user) }}">
                                                        <i class="bi bi-trash me-1"></i> Hapus
                                                    </button>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>




@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tombol delete
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function () {
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
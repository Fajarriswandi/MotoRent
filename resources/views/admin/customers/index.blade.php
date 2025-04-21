@extends('layouts.app')

@section('content')

<div class="headerContent">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Manajemen Customer</h3>

        @if (canAccess('customers', 'create'))
        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">+ Tambah Customer</a>

        @endif

    </div>
</div>

<div class="mainContent">
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td>{{ $customer->address }}</td>
                    <td>
                        <a href="{{ route('admin.customers.show', $customer->id) }}"
                            class="btn btn-sm btn-info">Detail</a>

                        @if (canAccess('customers', 'edit'))
                        <a href="{{ route('admin.customers.edit', $customer->id) }}"
                            class="btn btn-sm btn-warning">Edit</a>

                        @endif
                        @if (canAccess('customers', 'delete'))

                        <button class="btn btn-sm btn-danger btn-delete"
                            data-url="{{ route('admin.customers.destroy', $customer->id) }}"
                            data-name="{{ $customer->name }}">
                            Hapus
                        </button>
                        @endif

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">Belum ada data customer.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            const url = this.dataset.url;
            const name = this.dataset.name;

            Swal.fire({
                title: `Yakin ingin menghapus ${name}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        }).then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Berhasil!', 'Customer telah dihapus.', 'success')
                                    .then(() => location.reload());
                            }
                        });
                }
            });
        });
    });
</script>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            toast: true,
            position: 'bottom-end',
            icon: 'success',
            title: '{{ session("success") }}',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    });
</script>
@endif
@endpush
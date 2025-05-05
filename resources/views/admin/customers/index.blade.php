@extends('layouts.app')

@section('content')

    <div class="container-fluid  mt-5 pt-5">
        <div class="card widgetCard">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="titlePage">Customers</h3>
                    </div>
                    <div>
                        @if (canAccess('customers', 'create'))
                            <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">+ Add new customer</a>
                        @endif
                    </div>
                </div>

                <!-- Table data -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="tableHeader">
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
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light border" type="button"
                                                id="dropdownCustomer{{ $customer->id }}" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownCustomer{{ $customer->id }}">
                                                <li>
                                                    <a href="{{ route('admin.customers.show', $customer->id) }}"
                                                        class="dropdown-item">
                                                        <i class="bi bi-eye me-1"></i> Detail
                                                    </a>
                                                </li>

                                                @if (canAccess('customers', 'edit'))
                                                    <li>
                                                        <a href="{{ route('admin.customers.edit', $customer->id) }}"
                                                            class="dropdown-item text-warning">
                                                            <i class="bi bi-pencil me-1"></i> Edit
                                                        </a>
                                                    </li>
                                                @endif

                                                @if (canAccess('customers', 'delete'))
                                                    <li>
                                                        <button class="dropdown-item text-danger btn-delete"
                                                            data-url="{{ route('admin.customers.destroy', $customer->id) }}"
                                                            data-name="{{ $customer->name }}">
                                                            <i class="bi bi-trash me-1"></i> Hapus
                                                        </button>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">There is no customer data yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Table data -->

            </div>
        </div>
    </div>


@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function () {
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
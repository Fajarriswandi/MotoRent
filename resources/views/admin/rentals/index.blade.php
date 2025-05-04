@extends('layouts.app')

@section('content')


    <div class="container-fluid  mt-5 pt-5">
        <div class="card widgetCard">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <!-- Title -->
                    <div>
                        <h3 class="titlePage">Rental Management</h3>
                    </div>

                    <!-- Button Right -->
                    @if (canAccess('rentals', 'create'))
                        <div>
                            <a href="{{ route('admin.rentals.create') }}" class="btn btn-primary mb-3">+ Add Rental</a>
                        </div>
                    @endif
                </div>

                <form method="GET" class="mb-4">
                    <div class="input-group">
                        {{-- Search Brand / Model --}}
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="form-control inputGroupResponsive" placeholder="Cari Brand / Model">

                        {{-- Tanggal Mulai --}}
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                            class="form-control inputGroupResponsive">

                        {{-- Tanggal Akhir --}}
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                            class="form-control inputGroupResponsive">

                        {{-- Status Penyewaan --}}
                        <select name="status" class="form-select inputGroupResponsive">
                            <option value="">Semua Status</option>
                            <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Berjalan</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai
                            </option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan
                            </option>
                        </select>

                        {{-- Button Filter --}}
                        <button class="btn btn-outline-secondary" type="submit">Filter</button>

                        {{-- Button Clear --}}
                        <a href="{{ route('admin.rentals.index') }}" class="btn btn-outline-secondary"><i
                                class="bi bi-arrow-clockwise"></i></a>
                    </div>
                </form>

            </div>

            <!-- List Table -->
            <div class="table-responsive ps-3 pe-3">
                <table class="table table-striped table-hover align-middle">
                    <thead class="tableHeader">
                        <tr>
                            <th>Gambar</th>
                            <th>Motor</th>
                            <th>Penyewa</th>
                            <th>Tanggal</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rentals as $rental)
                            <tr>
                                <td>
                                    @if($rental->motorbike && $rental->motorbike->image)
                                        <img src="{{ asset('storage/' . $rental->motorbike->image) }}" alt="Gambar Motor" width="80"
                                            class="rounded">
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $rental->motorbike->brand }} {{ $rental->motorbike->model }}</td>
                                <td>{{ $rental->customer?->name ?? '-' }}</td>
                                <td>Start {{ $rental->start_date }} <br> End {{ $rental->end_date }}</td>
                                <td>Rp{{ number_format($rental->total_price, 0, ',', '.') }}</td>
                                <td>
                                    @if($rental->is_completed)
                                        <span class="badge bg-success">Selesai</span>
                                    @elseif($rental->is_cancelled)
                                        <span class="badge bg-danger">Dibatalkan</span>
                                    @else
                                        <span class="badge bg-secondary">Berlangsung</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown dropdown-menu-end">
                                        <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>

                                        <ul class="dropdown-menu shadow-sm">
                                            {{-- Detail --}}
                                            <li>
                                                <a href="{{ route('admin.rentals.show', $rental->id) }}" class="dropdown-item">
                                                    <i class="bi bi-eye me-1"></i> Detail
                                                </a>
                                            </li>

                                            <!-- Edit -->
                                            @if (canAccess('rentals', 'edit') && !$rental->is_completed && !$rental->is_cancelled && now()->lt($rental->start_date))
                                                <li>
                                                    <a href="{{ route('admin.rentals.edit', $rental->id) }}"
                                                        class="dropdown-item text-primary">
                                                        <i class="bi bi-pencil-square me-1"></i> Edit
                                                    </a>
                                                </li>
                                            @endif



                                            {{-- Tindakan Edit --}}
                                            @if (canAccess('rentals', 'edit') && !$rental->is_completed && !$rental->is_cancelled)
                                                <li>
                                                    <button type="button" class="dropdown-item text-success"
                                                        onclick="event.preventDefault(); confirmComplete({{ $rental->id }})">
                                                        <i class="bi bi-check-circle me-1"></i> Tandai Selesai
                                                    </button>
                                                </li>
                                                <li>
                                                    <button type="button" class="dropdown-item text-warning"
                                                        onclick="event.preventDefault(); confirmCancel({{ $rental->id }})">
                                                        <i class="bi bi-x-circle me-1"></i> Batalkan
                                                    </button>
                                                </li>
                                            @endif

                                            {{-- Cetak Invoice --}}
                                            <li>
                                                <a href="{{ route('admin.rentals.invoice', $rental->id) }}" target="_blank"
                                                    class="dropdown-item">
                                                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Cetak Invoice
                                                </a>
                                            </li>

                                            {{-- Delete --}}
                                            @if (canAccess('rentals', 'delete'))
                                                <li>
                                                    <button type="button" class="dropdown-item text-danger"
                                                        onclick="event.preventDefault(); confirmDelete({{ $rental->id }})">
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
                                <td colspan="7" class="text-center">
                                    <x-icon name="hugeicons:file-not-found" class="sm" />Data Not found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $rentals->links() }}
                </div>
            </div>

        </div>
    </div>



    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        <script>
            function confirmDelete(rentalId) {
                Swal.fire({
                    title: 'Yakin ingin menghapus data ini?',
                    text: 'Data penyewaan akan dihapus secara permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/admin/rentals/${rentalId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Berhasil!', 'Data telah dihapus.', 'success')
                                        .then(() => location.reload());
                                }
                            });
                    }
                });
            }

            function confirmComplete(rentalId) {
                Swal.fire({
                    title: 'Selesaikan Penyewaan?',
                    text: 'Status motor akan dikembalikan ke tersedia.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Selesaikan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/admin/rentals/${rentalId}/complete`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                            .then(response => {
                                if (response.ok) {
                                    // Tampilkan SweetAlert sukses
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: 'Penyewaan telah diselesaikan.',
                                        confirmButtonColor: '#3085d6',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    // Handle error jika response bukan 200 OK
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal!',
                                        text: 'Terjadi kesalahan saat menyelesaikan penyewaan.',
                                    });
                                }
                            })
                            .catch(() => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Tidak dapat terhubung ke server.',
                                });
                            });
                    }
                });
            }

            function confirmCancel(rentalId) {
                Swal.fire({
                    title: 'Batalkan Penyewaan?',
                    text: 'Motor akan tersedia kembali dan transaksi dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Batalkan',
                    cancelButtonText: 'Kembali'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/admin/rentals/${rentalId}/cancel`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                            .then(() => location.reload());
                    }
                });
            }
        </script>
    @endpush

@endsection
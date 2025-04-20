@extends('layouts.app')

@section('content')
    <div class="container">
        <h3 class="mb-4">Manajemen Penyewaan</h3>

        <!-- @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif -->

        @if (canAccess('rentals', 'create'))
            <a href="{{ route('admin.rentals.create') }}" class="btn btn-primary mb-3">+ Tambah Penyewaan</a>

        @endif


        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
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
                            <td>{{ $rental->start_date }} s/d {{ $rental->end_date }}</td>
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
                                <div class="d-flex flex-wrap gap-2">

                                    @if (canAccess('rentals', 'edit'))
                                        @if(!$rental->is_completed && !$rental->is_cancelled)
                                            <form onsubmit="event.preventDefault(); confirmComplete({{ $rental->id }})">
                                                <button type="submit" class="btn btn-sm btn-success">Selesai</button>
                                            </form>
                                            <form onsubmit="event.preventDefault(); confirmCancel({{ $rental->id }})">
                                                <button type="submit" class="btn btn-sm btn-warning">Batal</button>
                                            </form>
                                        @endif

                                    @endif

                                    @if (canAccess('rentals', 'delete'))
                                        <form onsubmit="event.preventDefault(); confirmDelete({{ $rental->id }})">
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>

                                    @endif


                                    <a href="{{ route('admin.rentals.invoice', $rental->id) }}"
                                        class="btn btn-sm btn-outline-secondary" target="_blank">
                                        <i class="bi bi-file-earmark-pdf-fill"></i> Cetak
                                    </a>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada data penyewaan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $rentals->links() }}
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                            .then(() => location.reload());
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

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Toast Notifikasi Jika Ada Session "success" --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if (session('success'))
                Swal.fire({
                    toast: true,
                    position: 'bottom-end',
                    icon: 'success',
                    title: '{{ session("success") }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif
                        });
    </script>

    {{-- Konfirmasi Hapus --}}
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
                        .then(() => location.reload());
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
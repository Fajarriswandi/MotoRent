@extends('layouts.app')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Daftar Motor</h2>
        @if (canAccess('motorbikes', 'create'))
            <a href="{{ route('motorbikes.create') }}" class="btn btn-primary shadow-sm">+ Tambah Motor</a>
        @endif

    </div>

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2000
            });
        </script>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Merek</label>
                    <input type="text" name="brand" value="{{ request('brand') }}" class="form-control"
                        placeholder="Cari Merek">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Model</label>
                    <input type="text" name="model" value="{{ request('model') }}" class="form-control"
                        placeholder="Cari Model">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status Teknis</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                        <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Perawatan
                        </option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-outline-primary w-100" type="submit">Filter</button>
                    <a href="{{ route('motorbikes.index') }}" class="btn btn-outline-secondary w-100">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Gambar</th>
                    <th>Merek</th>
                    <th>Model</th>
                    <th>Tahun</th>
                    <th>Plat</th>
                    <th>Harga/hari</th>
                    <th>Status Sewa</th>
                    <th>Status Teknis</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($motorbikes as $motorbike)
                            @php
                                $today = now()->toDateString();
                                $isRented = $motorbike->rentals->where('start_date', '<=', $today)
                                    ->where('end_date', '>=', $today)
                                    ->where('is_cancelled', false)
                                    ->count() > 0;
                            @endphp
                            <tr>
                                <td>
                                    @if($motorbike->image)
                                        <img src="{{ asset('storage/' . $motorbike->image) }}" width="80" class="rounded shadow-sm">
                                    @endif
                                </td>
                                <td>{{ $motorbike->brand }}</td>
                                <td>{{ $motorbike->model }}</td>
                                <td>{{ $motorbike->year }}</td>
                                <td>{{ $motorbike->license_plate }}</td>
                                <td>Rp{{ number_format($motorbike->rental_price_day, 0, ',', '.') }}</td>

                                <td>
                                    @php
                                        $isRented = $motorbike->rentals()
                                            ->whereDate('start_date', '<=', now())
                                            ->whereDate('end_date', '>=', now())
                                            ->where('is_cancelled', false)
                                            ->exists();

                                        $isAvailable = !$isRented && $motorbike->technical_status === 'active';
                                    @endphp

                                    @if ($isRented)
                                        <span class="badge bg-warning text-dark">Rented</span>
                                    @elseif ($motorbike->technical_status !== 'active')
                                        <span class="badge bg-secondary">Unavailable</span>
                                    @else
                                        <span class="badge bg-success">Available</span>
                                    @endif

                                    @if (!$isRented)
                                                    @php
                                                        $upcoming = $motorbike->rentals
                                                            ->where('start_date', '>', now()->toDateString())
                                                            ->where('is_cancelled', false)
                                                            ->sortBy('start_date')
                                                            ->first();
                                                    @endphp

                                                    @if ($upcoming)
                                                        <div class="small text-muted mt-1">
                                                            🕓 Booked {{ \Carbon\Carbon::parse($upcoming->start_date)->translatedFormat('d M') }}
                                                            –
                                                            {{ \Carbon\Carbon::parse($upcoming->end_date)->translatedFormat('d M') }}
                                                        </div>
                                                    @endif
                                    @endif
                                </td>


                                <td>
                                    @php
                                        $techStatus = $motorbike->technical_status;
                                        $techLabel = ucfirst($techStatus);
                                        $techColor = match ($techStatus) {
                                            'active' => 'success',
                                            'inactive' => 'danger',
                                            'maintenance' => 'secondary',
                                            default => 'dark'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $techColor }}">
                                        {{ $techLabel }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('motorbikes.admin.show', $motorbike) }}" class="btn btn-sm btn-info">Detail</a>

                                    @if (canAccess('motorbikes', 'edit'))
                                        <a href="{{ route('motorbikes.edit', $motorbike) }}" class="btn btn-sm btn-warning">Edit</a>

                                    @endif

                                    @if (canAccess('motorbikes', 'delete'))
                                        <form action="{{ route('motorbikes.destroy', $motorbike) }}" method="POST"
                                            class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger delete-btn">Hapus</button>
                                        </form>

                                    @endif


                                </td>
                            </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">Data tidak ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $motorbikes->links() }}
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Yakin ingin menghapus?',
                        text: "Data motor ini akan dihapus secara permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, hapus!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if (session('success'))
                Swal.fire({
                    toast: true,
                    position: 'bottom-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif
                                });
    </script>
@endpush
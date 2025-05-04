@extends('layouts.app')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')


    <div class="container-fluid  mt-5 pt-5">
        <div class="card widgetCard">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">

                    <!-- Title -->
                    <div>
                        <h3 class="titlePage">List Vehicle</h3>
                    </div>
                    <!-- Filter -->
                    <div class="d-flex align-items-center gap-2">

                        <form method="GET" class="d-flex gap-2">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Search"
                                    value="{{ request('search') }}">

                                <select name="status" class="form-select" style="max-width: 200px;">
                                    <option value="">Semua Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif
                                    </option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak
                                        Aktif
                                    </option>
                                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>
                                        Perawatan</option>
                                    <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>Rented
                                    </option>
                                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>
                                        Available
                                    </option>
                                </select>
                            </div>

                            <div class="btn-group" role="group" aria-label="Basic example">
                                <button type="submit" class="btn btn-outline-secondary">Filter</button>
                                <a href="{{ route('motorbikes.index') }}" class="btn btn-outline-secondary"><i
                                        class="bi bi-arrow-clockwise"></i></a>
                            </div>

                        </form>

                        @if (canAccess('motorbikes', 'create'))
                            <a href="{{ route('motorbikes.create') }}" class="btn btn-primary shadow-sm"><i
                                    class="bi bi-plus"></i>
                                Add</a>
                        @endif
                    </div>

                </div>


                <table class="table table-striped table-hover align-middle">
                    <thead class="tableHeader">
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
                                $isRented = $motorbike->rentals->filter(function ($rental) use ($today) {
                                    return $rental->start_date <= $today
                                        && $rental->end_date >= $today
                                        && !$rental->is_cancelled
                                        && !$rental->is_completed;
                                })->isNotEmpty();
                            @endphp
                            <tr>
                                <td>
                                    @if($motorbike->image)
                                        <img src="{{ asset('storage/' . $motorbike->image) }}" class="rounded imageThumbTable"
                                            alt="{{ $motorbike->brand }}">
                                    @endif
                                </td>
                                <td>{{ $motorbike->brand }}</td>
                                <td>{{ $motorbike->model }}</td>
                                <td>{{ $motorbike->year }}</td>
                                <td>{{ $motorbike->license_plate }}</td>
                                <td>Rp{{ number_format($motorbike->rental_price_day, 0, ',', '.') }}</td>

                                <td>
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
                                                ->where('is_completed', false)
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
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a href="{{ route('motorbikes.admin.show', $motorbike) }}"
                                                    class="dropdown-item">
                                                    <i class="bi bi-eye me-1"></i> Detail
                                                </a>
                                            </li>

                                            @if (canAccess('motorbikes', 'edit'))
                                                <li>
                                                    <a href="{{ route('motorbikes.edit', $motorbike) }}" class="dropdown-item">
                                                        <i class="bi bi-pencil-square me-1"></i> Edit
                                                    </a>
                                                </li>
                                            @endif

                                            @if (canAccess('motorbikes', 'delete'))
                                                <li>
                                                    <form action="{{ route('motorbikes.destroy', $motorbike) }}" method="POST"
                                                        class="d-inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bi bi-trash me-1"></i> Hapus
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">
                                    <x-icon name="hugeicons:file-not-found" class="sm" />Data Not found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $motorbikes->links() }}
                </div>


            </div>
        </div>
    </div>



    @push('scripts')

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
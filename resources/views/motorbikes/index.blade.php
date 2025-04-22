@extends('layouts.app')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')

<div class="headerContent">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="titlePage">Daftar Motor</h3>
            @if (canAccess('motorbikes', 'create'))
            <a href="{{ route('motorbikes.create') }}" class="btn btn-primary shadow-sm">+ Tambah Motor</a>
            @endif

        </div>

        <form method="GET" class="mb-4">
            <div class="input-group">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Cari motor berdasarkan merk atau model"
                    value="{{ request('search') }}">

                <select name="status" class="form-select" style="max-width: 200px;">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Perawatan</option>
                    <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>Rented</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                </select>

                <button class="btn btn-outline-primary" type="submit">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <a href="{{ route('motorbikes.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i> Clear
                </a>
            </div>
        </form>
    </div>
</div>

<div class="mainContent">
    <div class="container-fluid">
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
                            <img src="{{ asset('storage/' . $motorbike->image) }}" width="80" class="rounded shadow-sm">
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
                            <a href="{{ route('motorbikes.admin.show', $motorbike) }}"
                                class="btn btn-sm btn-info">Detail</a>

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

        <div class="mt-3">
            {{ $motorbikes->links() }}
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
        showConfirmButton: false,
        timer: 2000
    });
</script>
@endif
<script>
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
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
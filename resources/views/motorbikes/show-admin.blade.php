@extends('layouts.app')

@section('content')
    <div class="container mt-5 pt-5">
        <a href="{{ route('motorbikes.index') }}" class="btn mb-3"><i class="bi bi-arrow-left"></i> </a>


        <div class="card widgetCard">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="titleCardIcon">
                        <span><x-icon name="solar:calendar-broken" class="sm" /></span>
                        <h4>Detail Motor</h4>
                    </div>
                    <div>
                        @if (canAccess('motorbikes', 'edit'))
                            <a href="{{ route('motorbikes.edit', $motorbike->id) }}" class="btn"><i
                                    class="bi bi-pencil-square me-1"></i> Edit</a>

                        @endif
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card bg-white widgetCard h-100">
                        <div class="card-body">
                            <h5 class="mb-4">{{ $motorbike->brand }} {{ $motorbike->model }}</h5>

                            @if($motorbike && $motorbike->image)
                                <img src="{{ asset('storage/' . $motorbike->image) }}" alt="" class="img-fluid">
                            @else
                                <span class="text-muted">-</span>
                            @endif

                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light widgetCard h-100">
                        <div class="card-body">
                            <h5 class="mb-4">Detail Motor</h5>
                            <div class="mb-2">
                                <small>License Plate</small>
                                <p>{{ $motorbike->license_plate }}</p>
                            </div>
                            <div class="mb-2">
                                <small>Technical Status</small>
                                <p>
                                    <span
                                        class="badge bg-{{ 
                                                                                 $motorbike->technical_status === 'active' ? 'success' : ($motorbike->technical_status === 'maintenance' ? 'secondary' : 'danger') }}">
                                        {{ ucfirst($motorbike->technical_status) }}
                                    </span>
                                </p>
                            </div>
                            <div class="mb-2">
                                <small>Year</small>
                                <p>{{ $motorbike->year }}</p>
                            </div>
                            <div class="mb-2">
                                <small>Color</small>
                                <p>{{ $motorbike->color }}</p>
                            </div>
                            <div class="mb-2">
                                <small>Price Per Day</small>
                                <p>Rp{{ number_format($motorbike->rental_price_day, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>



        <div class="card widgetCard mb-5">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="titleCardIcon">
                        <span><x-icon name="solar:calendar-broken" class="sm" /></span>
                        <h4>Jadwal Penyewaan</h4>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table  table-striped table-hover align-middle">
                        <thead class="tableHeader">
                            <tr>
                                <th>Customer</th>
                                <th>Tanggal Sewa</th>
                                <th>Status</th>
                                <th>Aksi</th> <!-- Tambahkan kolom aksi -->
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($motorbike->rentals->sortByDesc('start_date') as $rental)
                                @php
                                    $today = \Carbon\Carbon::today();
                                    $start = \Carbon\Carbon::parse($rental->start_date);
                                    $end = \Carbon\Carbon::parse($rental->end_date);

                                    if ($rental->is_cancelled) {
                                        $status = 'Dibatalkan';
                                        $badge = 'danger';
                                    } elseif ($rental->is_completed) {
                                        $status = 'Selesai';
                                        $badge = 'secondary';
                                    } elseif ($start->gt($today)) {
                                        $status = 'Mendatang';
                                        $badge = 'info';
                                    } else {
                                        $status = 'Aktif';
                                        $badge = 'success';
                                    }
                                @endphp

                                @php
                                    $icons = [
                                        'Aktif' => 'bi-check-circle-fill',
                                        'Mendatang' => 'bi-clock-fill',
                                        'Selesai' => 'bi-flag-fill',
                                        'Dibatalkan' => 'bi-x-circle-fill'
                                    ];
                                @endphp

                                <tr>
                                    <td>{{ $rental->customer->name ?? '-' }}</td>
                                    <td>{{ $start->translatedFormat('d M Y') }} - {{ $end->translatedFormat('d M Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $badge }}">
                                            <i class="bi {{ $icons[$status] ?? 'bi-question-circle-fill' }} me-1"></i>
                                            {{ $status }}
                                        </span>
                                    </td>
                                    <td>
                                        @if (!$rental->is_cancelled && !$rental->is_completed)
                                            <form method="POST" action="{{ route('admin.rentals.cancel', $rental->id) }}"
                                                class="cancel-form" data-rental-id="{{ $rental->id }}">
                                                @csrf
                                                <button class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>

                                            </form>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada penyewaan.</td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    </div>
    </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.cancel-form').forEach(form => {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        const rentalId = this.dataset.rentalId;

                        Swal.fire({
                            title: 'Batalkan Penyewaan?',
                            text: 'Penyewaan ini akan ditandai sebagai dibatalkan permanen.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, Batalkan!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.submit();
                            }
                        });
                    });
                });
            });
        </script>
    @endpush



@endsection
@extends('layouts.app')

@section('content')
    <div class="container mt-5 pt-5">
        <a href="{{ route('admin.rentals.index') }}" class="btn mb-3"><i class="bi bi-arrow-left"></i> </a>

        <div class="card mb-4 widgetCard">

            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="titleCardIcon">
                        <span><x-icon name="solar:calendar-broken" class="sm" /></span>
                        <h4>Detail Penyewaan Motor</h4>
                    </div>
                    <div>
                        @if (canAccess('rentals', 'edit') && !$rental->is_completed && !$rental->is_cancelled && now()->lt($rental->start_date))
                            <a href="{{ route('admin.rentals.edit', $rental->id) }}" class="btn"><i
                                    class="bi bi-pencil-square me-1"></i> Edit</a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-4">
                        <div class="mb-2">
                            <small>Customer Name</small>
                            <p>{{ $rental->customer->name }}</p>
                        </div>
                        <div class="mb-2">
                            <small>Email</small>
                            <p>({{ $rental->customer->email }})</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-2">
                            <small>Start Time</small>
                            <p>{{ \Carbon\Carbon::parse($rental->start_date)->translatedFormat('d F Y') }}</p>
                        </div>
                        <div class="mb-2">
                            <small>End time</small>
                            <p>{{ \Carbon\Carbon::parse($rental->end_date)->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-2">
                            <small>Total</small>
                            <p>Rp {{ number_format($rental->total_price, 0, ',', '.') }}</p>
                        </div>
                        <div class="mb-2">
                            <small>Status</small>
                            <p>
                                @if ($rental->is_cancelled)
                                    <span class="badge bg-danger">Dibatalkan</span>
                                @elseif ($rental->is_completed)
                                    <span class="badge bg-success">Selesai</span>
                                @else
                                    <span class="badge bg-warning">Berlangsung</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="card widgetCard">
            <div class="card-header">
                <div class="titleCardIcon">
                    <span><x-icon name="solar:calendar-broken" class="sm" /></span>
                    <h4>Detail Vehicle</h4>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card bg-white widgetCard h-100">
                        <div class="card-body">
                            <h5 class="mb-4">{{ $rental->motorbike->brand }} {{ $rental->motorbike->model }}</h5>

                            @if($rental->motorbike && $rental->motorbike->image)
                                <img src="{{ asset('storage/' . $rental->motorbike->image) }}" alt="" class="img-fluid">
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
                                <p>{{ $rental->motorbike->license_plate }}</p>
                            </div>
                            <div class="mb-2">
                                <small>Technical Status</small>
                                <p>
                                    <span
                                        class="badge bg-{{ 
                                                         $rental->motorbike->technical_status === 'active' ? 'success' : ($motorbike->technical_status === 'maintenance' ? 'secondary' : 'danger') }}">
                                        {{ ucfirst($rental->motorbike->technical_status) }}
                                    </span>
                                </p>
                            </div>
                            <div class="mb-2">
                                <small>Year</small>
                                <p>{{ $rental->motorbike->year }}</p>
                            </div>
                            <div class="mb-2">
                                <small>Color</small>
                                <p>{{ $rental->motorbike->color }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- @if($rental->motorbike && $rental->motorbike->image)
                                                <img src="{{ asset('storage/' . $rental->motorbike->image) }}" alt="Gambar Motor" width="80" class="rounded">
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif

                                            {{ $rental->motorbike->brand }} {{ $rental->motorbike->model }} -
                                            {{ $rental->motorbike->license_plate }} -->
            </div>

        </div>

        <div class="card widgetCard">
            <div class="card-header">
                <div class="titleCardIcon">
                    <span><x-icon name="solar:calendar-broken" class="sm" /></span>
                    <h4>History Customers</h4>
                </div>
            </div>
            <div class="card-body">
                @if ($rentalHistory->isEmpty())
                    <p class="text-muted">Belum ada penyewa lain untuk motor ini.</p>
                @else
                    <ul class="list-group">
                        @foreach ($rentalHistory as $history)
                            <li class="list-group-item">
                                {{ $history->customer->name }} ({{ $history->customer->email }})
                                <br>
                                <small>Sewa: {{ $history->start_date }} s.d. {{ $history->end_date }}</small>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
@endsection
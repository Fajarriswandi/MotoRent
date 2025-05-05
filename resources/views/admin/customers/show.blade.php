@extends('layouts.app')

@section('content')
    <div class="container mt-5 pt-5">
        <a href="{{ route('admin.customers.index') }}" class="btn mb-3"><i class="bi bi-arrow-left"></i> </a>

        <div class="card widgetCard">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="titleCardIcon">
                        <span><x-icon name="solar:calendar-broken" class="sm" /></span>
                        <h4>Detail Customer</h4>
                    </div>

                    <div>
                        @if (canAccess('customers', 'edit'))
                            <a href="{{ route('admin.customers.edit', $customer->id) }}" class="dropdown-item">
                                <i class="bi bi-pencil me-1"></i> Edit
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        @if($customer->photo)
                            <img src="{{ asset('storage/' . $customer->photo) }}" class="rounded img-fluid" alt="Foto Customer">
                        @else
                            <div class="text-muted d-flex align-items-center justify-content-center" style="height: 80px;">
                                <i class="bi bi-image" style="font-size: 2rem;"></i>
                            </div>
                        @endif

                    </div>
                    <div class="col-md-4">
                        <div class="mb-2">
                            <small>Customer Name</small>
                            <p>{{ $customer->name }}</p>
                        </div>
                        <div class="mb-2">
                            <small>Email</small>
                            <p>({{ $customer->email }})</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-2">
                            <small>Customer Phone</small>
                            <p>{{ $customer->phone ?? '-' }}</p>
                        </div>
                        <div class="mb-2">
                            <small>Address</small>
                            <p>{{ $customer->address }}</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="card widgetCard">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="titleCardIcon">
                        <span><x-icon name="solar:calendar-broken" class="sm" /></span>
                        <h4>Riwayat Penyewaan</h4>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="tableHeader">
                            <tr>
                                <th>Motor</th>
                                <th>Tanggal Sewa</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rentals as $rental)
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
                                <tr>
                                    <td>{{ $rental->motorbike->brand }} {{ $rental->motorbike->model }}</td>
                                    <td>{{ $start->format('d M Y') }} - {{ $end->format('d M Y') }}</td>
                                    <td>Rp{{ number_format($rental->total_price, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $badge }}">
                                            <i
                                                class="bi bi-{{ $status == 'Dibatalkan' ? 'x-circle-fill' : ($status == 'Selesai' ? 'flag-fill' : ($status == 'Aktif' ? 'check-circle-fill' : 'clock-fill')) }} me-1"></i>
                                            {{ $status }}
                                        </span>
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


@endsection
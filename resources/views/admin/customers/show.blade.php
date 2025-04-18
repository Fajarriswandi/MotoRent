@extends('layouts.app')

@section('content')
    <h3 class="mb-3">Detail Customer</h3>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5>{{ $customer->name }}</h5>
            <p>Email: {{ $customer->email }}</p>
            <p>Nomor HP: {{ $customer->phone ?? '-' }}</p>
        </div>
    </div>

    <h5>Riwayat Penyewaan</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
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
@endsection
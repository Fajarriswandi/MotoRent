@extends('layouts.app')

@section('content')
    <div class="container">
        <h3 class="mb-4">Riwayat Penyewaan Saya</h3>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Gambar</th>
                        <th>Motor</th>
                        <th>Tanggal</th>
                        <th>Total Harga</th>
                        <th>Status Penyewaan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rentals as $rental)
                        <tr>
                            <td>
                                @if($rental->motorbike && $rental->motorbike->image)
                                    <img src="{{ asset('storage/' . $rental->motorbike->image) }}" width="80"
                                        class="rounded shadow-sm">
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $rental->motorbike->brand }} {{ $rental->motorbike->model }}</td>
                            <td>{{ $rental->start_date }} s/d {{ $rental->end_date }}</td>
                            <td>Rp{{ number_format($rental->total_price, 0, ',', '.') }}</td>
                            <td>
                                @if($rental->is_completed)
                                    <span class="badge bg-success">Selesai</span>
                                @elseif(!$rental->is_approved)
                                    <span class="badge bg-secondary">Menunggu Persetujuan</span>
                                @else
                                    <span class="badge bg-warning text-dark">Berlangsung</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada data penyewaan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
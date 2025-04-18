@extends('layouts.app')

@section('content')
    <div class="container">
        <h3 class="mb-4">Daftar Motor Tersedia</h3>

        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="text" name="brand" value="{{ request('brand') }}" class="form-control"
                    placeholder="Cari Merek">
            </div>
            <div class="col-md-3">
                <input type="text" name="model" value="{{ request('model') }}" class="form-control"
                    placeholder="Cari Model">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Tersedia</option>
                    <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>Disewa</option>
                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Perawatan</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary" type="submit">Filter</button>
                <a href="{{ route('motorbikes.public') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="row">
            @forelse($motorbikes as $motorbike)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        @if($motorbike->image)
                            <img src="{{ asset('storage/' . $motorbike->image) }}" class="card-img-top" alt="Gambar Motor">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $motorbike->brand }} {{ $motorbike->model }}</h5>
                            <p class="card-text">
                                <strong>Tahun:</strong> {{ $motorbike->year }}<br>
                                <strong>Warna:</strong> {{ $motorbike->color }}<br>
                                <strong>Plat:</strong> {{ $motorbike->license_plate }}<br>
                                <strong>Harga/Hari:</strong>
                                Rp{{ number_format($motorbike->rental_price_day, 0, ',', '.') }}<br>
                                <strong>Status:</strong>
                                <span
                                    class="badge bg-{{ $motorbike->status == 'available' ? 'success' : ($motorbike->status == 'rented' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($motorbike->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="card-footer bg-white d-flex justify-content-between">
                            <a href="{{ route('motorbikes.show', $motorbike) }}" class="btn btn-outline-info btn-sm">Detail</a>
                            @if($motorbike->status === 'available')
                                            @php
                                                $rentalUrl = route('rentals.create', ['motorbike' => $motorbike->id]);
                                              @endphp
                                            <a href="{{ auth()->check() && auth()->user()->role === 'customer' ? $rentalUrl : route('login') . '?redirect=' . urlencode($rentalUrl) }}"
                                                class="btn btn-success btn-sm">
                                                Sewa
                                            </a>
                            @else
                                <button class="btn btn-outline-secondary btn-sm" disabled>{{ ucfirst($motorbike->status) }}</button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted text-center">Tidak ada motor ditemukan.</p>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $motorbikes->withQueryString()->links() }}
        </div>
    </div>
@endsection
@extends('layouts.app')

@section('content')
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Detail Penyewaan</h4>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item">
                        <strong>Motor:</strong> {{ $rental->motorbike->brand }} {{ $rental->motorbike->model }}
                        ({{ $rental->motorbike->license_plate }})
                    </li>
                    <li class="list-group-item">
                        <strong>Tanggal Sewa:</strong> {{ $rental->start_date }} - {{ $rental->end_date }}
                    </li>
                    <li class="list-group-item">
                        <strong>Total Harga:</strong> Rp{{ number_format($rental->total_price, 0, ',', '.') }}
                    </li>
                    <li class="list-group-item">
                        <strong>Status:</strong> <span class="badge bg-secondary">{{ ucfirst($rental->status) }}</span>
                    </li>
                </ul>
                <a href="{{ route('rentals.index') }}" class="btn btn-secondary mt-3">Kembali</a>
            </div>
        </div>
    </div>
@endsection
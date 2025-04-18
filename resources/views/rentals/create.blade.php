@extends('layouts.app')

@section('content')
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Form Penyewaan Motor</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('rentals.store') }}">
                    @csrf
                    <input type="hidden" name="motorbike_id" value="{{ $motorbike->id }}">

                    <div class="mb-3">
                        <label class="form-label">Motor</label>
                        <input type="text" class="form-control"
                            value="{{ $motorbike->brand }} {{ $motorbike->model }} ({{ $motorbike->license_plate }})"
                            disabled>
                    </div>

                    <div class="mb-3">
                        <label for="start_date" class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="end_date" class="form-label">Tanggal Selesai</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-success">Sewa Sekarang</button>
                    <a href="/" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
@endsection
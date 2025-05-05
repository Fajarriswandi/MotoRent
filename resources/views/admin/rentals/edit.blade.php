@extends('layouts.app')

@section('content')
    <div class="container mt-5 pt-5">
        <div class="card widgetCard">
            <div class="card-header">
                <h4 class="titlePage">Edit Penyewaan</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.rentals.update', $rental->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Pilih Customer</label>
                        <select name="customer_id" class="form-select" required>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ $rental->customer_id == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} ({{ $customer->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pilih Motor</label>
                        <select name="motorbike_id" class="form-select" required>
                            @foreach($motorbikes as $motorbike)
                                <option value="{{ $motorbike->id }}" {{ $rental->motorbike_id == $motorbike->id ? 'selected' : '' }}>
                                    {{ $motorbike->brand }} {{ $motorbike->model }} - {{ $motorbike->license_plate }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Mulai Sewa</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $rental->start_date }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Akhir Sewa</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $rental->end_date }}" required>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.rentals.index') }}" class="btn btn-secondary ms-2">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('rental_conflict'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'error',
            title: 'Gagal Mengupdate',
            text: '{{ session("rental_conflict") }}',
            confirmButtonText: 'Mengerti'
        });
    });
@endif
</script>
@endpush

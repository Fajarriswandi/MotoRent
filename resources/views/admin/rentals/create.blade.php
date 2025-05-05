@extends('layouts.app')

@section('content')
    <form action="{{ route('admin.rentals.store') }}" method="POST">
        <div class="headerForm">
            <div class="content container">
                <div>
                    <h4>Rental Form</h4>
                    <p>Please fill in all the details.</p>
                </div>
                <div>
                    <div class="mt-4 d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <span class="spinner-border spinner-border-sm d-none" id="submitSpinner" role="status" aria-hidden="true"></span>
                            <span id="submitText">Confirm & Save</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-5">
            <div class="card widgetCard">
                <div class="card-body">

                    @csrf

                    {{-- Customer --}}
                    <div class="mb-3">
                        <label class="form-label">Pilih Customer</label>
                        <select name="customer_id" class="form-select" required>
                            <option value="" disabled {{ old('customer_id') ? '' : 'selected' }}>-- Pilih Customer --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} ({{ $customer->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Motorbike --}}
                    <div class="mb-3">
                        <label class="form-label">Pilih Motor</label>
                        <select name="motorbike_id" class="form-select" required>
                            <option value="" disabled {{ old('motorbike_id') ? '' : 'selected' }}>-- Pilih Motor --</option>
                            @foreach($motorbikes as $motorbike)
                                <option value="{{ $motorbike->id }}" {{ old('motorbike_id') == $motorbike->id ? 'selected' : '' }}>
                                    {{ $motorbike->brand }} {{ $motorbike->model }} - {{ $motorbike->license_plate }}
                                    ({{ ucfirst($motorbike->technical_status) }})
                                </option>
                            @endforeach
                        </select>
                        @error('motorbike_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Start Date --}}
                    <div class="mb-3">
                        <label class="form-label">Tanggal Mulai Sewa</label>
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
                        @error('start_date')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- End Date --}}
                    <div class="mb-3">
                        <label class="form-label">Tanggal Akhir Sewa</label>
                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
                        @error('end_date')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Loading spinner saat klik simpan
        document.getElementById('submitBtn')?.addEventListener('click', function () {
            const spinner = document.getElementById('submitSpinner');
            const text = document.getElementById('submitText');
            spinner.classList.remove('d-none');
            text.textContent = 'Memproses...';
        });

        // Tampilkan alert jika ada konflik sewa
        @if(session('rental_conflict'))
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan',
                    text: '{{ session("rental_conflict") }}',
                    confirmButtonText: 'Mengerti'
                });
            });
        @endif

        // Tampilkan toast success jika berhasil simpan
        @if(session('success'))
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    toast: true,
                    position: 'bottom-end',
                    icon: 'success',
                    title: '{{ session("success") }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            });
        @endif
    </script>
@endpush

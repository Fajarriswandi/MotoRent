@extends('layouts.app')

@section('content')
    <div class="container">
        <h3 class="mb-4">Form Penyewaan Manual oleh Admin</h3>

        <!-- @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif -->

        <form action="{{ route('admin.rentals.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Pilih Customer</label>
                <select name="customer_id" class="form-select" required>
                    <option value="" disabled selected>-- Pilih Customer --</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->email }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Pilih Motor</label>
                <select name="motorbike_id" class="form-select" required>
                    <option value="" disabled selected>-- Pilih Motor --</option>
                    @foreach($motorbikes as $motorbike)
                        <option value="{{ $motorbike->id }}">
                            {{ $motorbike->brand }} {{ $motorbike->model }} - {{ $motorbike->license_plate }}
                            ({{ ucfirst($motorbike->technical_status) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal Mulai Sewa</label>
                <input type="date" name="start_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal Akhir Sewa</label>
                <input type="date" name="end_date" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary" id="submitBtn">
                <span class="spinner-border spinner-border-sm d-none" id="submitSpinner" role="status"
                    aria-hidden="true"></span>
                <span id="submitText">Simpan Penyewaan</span>
            </button>
        </form>
    </div>
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
        @if (session('rental_conflict'))
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
        @if (session('success'))
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

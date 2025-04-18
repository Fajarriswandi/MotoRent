@csrf
<div class="card shadow-sm">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Merek</label>
                <input type="text" name="brand" value="{{ old('brand', $motorbike->brand ?? '') }}" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Model</label>
                <input type="text" name="model" value="{{ old('model', $motorbike->model ?? '') }}" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Tahun</label>
                <input type="number" name="year" value="{{ old('year', $motorbike->year ?? '') }}" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Warna</label>
                <input type="text" name="color" value="{{ old('color', $motorbike->color ?? '') }}" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Nomor Plat</label>
                <input type="text" name="license_plate" value="{{ old('license_plate', $motorbike->license_plate ?? '') }}" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Harga per Jam</label>
                <input type="text" name="rental_price_hour" id="rental_price_hour"
                    value="{{ old('rental_price_hour', number_format($motorbike->rental_price_hour ?? 0, 0, ',', '.')) }}"
                    class="form-control format-rupiah">
            </div>

            <div class="col-md-4">
                <label class="form-label">Harga per Hari</label>
                <input type="text" name="rental_price_day" id="rental_price_day"
                    value="{{ old('rental_price_day', number_format($motorbike->rental_price_day ?? 0, 0, ',', '.')) }}"
                    class="form-control format-rupiah">
            </div>

            <div class="col-md-4">
                <label class="form-label">Harga per Minggu</label>
                <input type="text" name="rental_price_week" id="rental_price_week"
                    value="{{ old('rental_price_week', number_format($motorbike->rental_price_week ?? 0, 0, ',', '.')) }}"
                    class="form-control format-rupiah">
            </div>

            <div class="col-md-6">
                <label class="form-label">Status Teknis</label>
                <select name="technical_status" class="form-select" required>
                    <option value="active" {{ old('technical_status', $motorbike->technical_status ?? '') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ old('technical_status', $motorbike->technical_status ?? '') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    <option value="maintenance" {{ old('technical_status', $motorbike->technical_status ?? '') == 'maintenance' ? 'selected' : '' }}>Perawatan</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Foto Motor</label>
                @if(isset($motorbike) && $motorbike->image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $motorbike->image) }}" width="120" class="rounded shadow-sm">
                    </div>
                @endif
                <input type="file" name="image" class="form-control">
            </div>
        </div>
    </div>
</div>

<div class="mt-4 d-flex justify-content-between">
    <a href="{{ route('motorbikes.index') }}" class="btn btn-secondary">Batal</a>
    <button type="submit" class="btn btn-success" id="submitBtn">
        <span class="spinner-border spinner-border-sm d-none" id="submitSpinner" role="status" aria-hidden="true"></span>
        <span id="submitText">{{ isset($motorbike) ? 'Perbarui' : 'Simpan' }}</span>
    </button>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Format input ke rupiah
        document.querySelectorAll('.format-rupiah').forEach(function (input) {
            input.addEventListener('input', function () {
                let value = this.value.replace(/[^\d]/g, '');
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                this.value = value;
            });
        });

        // Tampilkan spinner saat submit
        document.getElementById('submitBtn')?.addEventListener('click', function () {
            const spinner = document.getElementById('submitSpinner');
            const text = document.getElementById('submitText');
            spinner.classList.remove('d-none');
            text.textContent = 'Memproses...';
        });

        // Toast success jika ada session
        @if (session('success'))
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            });
        @endif
    </script>
@endpush

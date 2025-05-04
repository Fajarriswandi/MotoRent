@extends('layouts.app')

@section('content')
    <form method="POST" action="{{ route('admin.customers.update', $customer->id) }}" enctype="multipart/form-data"
        id="editCustomerForm">
        @csrf
        @method('PUT')
        <div class="container-fluid p-0">
            <div class="headerForm">
                <div class="content container">
                    <div>
                        <h4>Edit data Customer</h4>
                        <p>Please fill in all the details.</p>
                    </div>
                    <div>
                        <div class="mt-4 d-flex justify-content-between">
                            <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary me-3">Cancel</a>

                            <button type="submit" class="btn btn-primary" id="updateBtn">
                                <span class="spinner-border spinner-border-sm d-none" id="spinner" role="status"
                                    aria-hidden="true"></span>
                                <span id="btnText">Update</span>
                            </button>



                        </div>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

        </div>

        <div class="container mt-4">
            <div class="card widgetCard">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name) }}"
                            required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $customer->email) }}"
                            required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" class="form-control">{{ old('address', $customer->address) }}</textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Foto</label>
                        <input type="file" name="photo" class="form-control">
                        @if ($customer->photo)
                            <div class="mt-2">
                                <small>Foto lama:</small><br>
                                <img src="{{ asset('storage/' . $customer->photo) }}" alt="Foto Customer" width="80"
                                    class="rounded shadow-sm">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>


    </form>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const form = document.getElementById('editCustomerForm');
        const btn = document.getElementById('updateBtn');
        const spinner = document.getElementById('spinner');
        const btnText = document.getElementById('btnText');

        form?.addEventListener('submit', function () {
            btn.disabled = true;
            spinner.classList.remove('d-none');
            btnText.textContent = 'Menyimpan...';
        });

        @if(session('success'))
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    toast: true,
                    position: 'bottom-end',
                    icon: 'success',
                    title: '{{ session("success") }}',
                    showConfirmButton: true,
                    timer: 3000,
                    timerProgressBar: true
                });
            });
        @endif
    </script>
@endpush
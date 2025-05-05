@extends('layouts.app')

@section('content')
    <form method="POST" action="{{ route('admin.customers.store') }}" enctype="multipart/form-data" id="customerForm">
        @csrf

        <div class="container-fluid p-0">
            <div class="headerForm">
                <div class="content container">
                    <div>
                        <h4>Add new Customer</h4>
                        <p>Please fill in all the details.</p>
                    </div>
                    <div>
                        <div class="mt-4 d-flex justify-content-between">
                            <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary me-3">Cancel</a>

                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span class="spinner-border spinner-border-sm d-none" id="spinner" role="status"
                                    aria-hidden="true"></span>
                                <span id="btnText">Submit</span>
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
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">No. Telepon</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="address" class="form-label">Alamat</label>
                        <textarea name="address" class="form-control"></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="photo" class="form-label">Foto</label>
                        <input type="file" name="photo" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password Default</label>
                        <input type="text" class="form-control" value="P4sMotoCust@" readonly>
                    </div>
                </div>

            </div>
        </div>

    </form>

@endsection

@push('scripts')
    <script>
        const form = document.getElementById('customerForm');
        const btn = document.getElementById('submitBtn');
        const spinner = document.getElementById('spinner');
        const btnText = document.getElementById('btnText');

        form.addEventListener('submit', function () {
            btn.disabled = true;
            spinner.classList.remove('d-none');
            btnText.textContent = 'Menyimpan...';
        });
    </script>
@endpush
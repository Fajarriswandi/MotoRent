<!-- resources/views/customers/create.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
  <h3 class="mb-4">Form Registrasi Customer</h3>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('customers.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
      <label for="name" class="form-label">Nama</label>
      <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="password" class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="phone" class="form-label">No. Telepon</label>
      <input type="text" name="phone" class="form-control">
    </div>
    <div class="mb-3">
      <label for="address" class="form-label">Alamat</label>
      <textarea name="address" class="form-control"></textarea>
    </div>
    <div class="mb-3">
      <label for="photo" class="form-label">Foto</label>
      <input type="file" name="photo" class="form-control">
    </div>
    <button type="submit" class="btn btn-success">Daftar</button>
  </form>
</div>
@endsection

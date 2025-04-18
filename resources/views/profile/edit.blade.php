@extends('layouts.app')

@section('content')
  <div class="row justify-content-center">
    <div class="col-md-8">
    <div class="card shadow-sm">
      <div class="card-header bg-primary text-white">
      <h4 class="mb-0">Edit Profil</h4>
      </div>
      <div class="card-body">
      @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">

        @csrf
        <div class="mb-3">
        <label for="name" class="form-label">Nama Lengkap</label>
        <input type="text" class="form-control" id="name" name="name"
          value="{{ old('name', auth()->user()->name) }}" required>
        </div>
        <div class="mb-3">
        <label for="email" class="form-label">Email (tidak bisa diubah)</label>
        <input type="email" class="form-control" id="email" value="{{ auth()->user()->email }}" disabled>
        </div>
        <div class="mb-3">
        <label for="phone" class="form-label">Nomor Telepon</label>
        <input type="text" class="form-control" id="phone" name="phone"
          value="{{ old('phone', auth()->user()->phone) }}">
        </div>
        <div class="mb-3">
        <label for="profile_photo" class="form-label">Foto Profil</label><br>
        @if(auth()->user()->profile_photo)
      <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="Foto Profil"
        class="rounded-circle mb-2" width="100" height="100">
    @endif
        <input type="file" class="form-control" id="profile_photo" name="profile_photo">
        </div>

        <div class="mb-3">
        <label for="address" class="form-label">Alamat</label>
        <textarea class="form-control" id="address" name="address"
          rows="3">{{ old('address', auth()->user()->address) }}</textarea>
        </div>
        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
      </form>
      </div>
    </div>
    </div>
  </div>
@endsection
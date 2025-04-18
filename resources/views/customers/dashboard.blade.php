@extends('layouts.app')

@section('content')
<div class="text-center">
    <h1 class="mb-4">Customer Dashboard</h1>
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
            <h5 class="card-title">Selamat datang, {{ Auth::user()->name }}!</h5>
            <p class="card-text">Anda login sebagai <strong>Customer</strong>.</p>
            <p class="card-text">Silakan mulai menjelajahi motor yang tersedia untuk disewa.</p>
            <a href="#" class="btn btn-primary">Lihat Motor</a>
        </div>
    </div>
</div>
@endsection

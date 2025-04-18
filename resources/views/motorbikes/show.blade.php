@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow-sm">
        <div class="card-header">
          <h5 class="mb-0">Detail Motor 123</h5>
        </div>
        <div class="card-body">
          <div class="text-center mb-3">
            @if($motorbike->image)
              <img src="{{ asset('storage/' . $motorbike->image) }}" width="300" class="img-fluid rounded">
            @else
              <p class="text-muted">Tidak ada gambar</p>
            @endif
          </div>

          <table class="table table-bordered">
            <tr><th>Merek</th><td>{{ $motorbike->brand }}</td></tr>
            <tr><th>Model</th><td>{{ $motorbike->model }}</td></tr>
            <tr><th>Tahun</th><td>{{ $motorbike->year }}</td></tr>
            <tr><th>Warna</th><td>{{ $motorbike->color }}</td></tr>
            <tr><th>No. Plat</th><td>{{ $motorbike->license_plate }}</td></tr>
            <tr><th>Harga Sewa / Hari</th><td>Rp{{ number_format($motorbike->rental_price_day, 0, ',', '.') }}</td></tr>
            <tr>
              <th>Status</th>
              <td>
                @if($motorbike->status === 'available')
                  <span class="badge bg-success">Tersedia</span>
                @elseif($motorbike->status === 'rented')
                  <span class="badge bg-warning text-dark">Disewa</span>
                @else
                  <span class="badge bg-secondary">Perawatan</span>
                @endif
              </td>
            </tr>
          </table>

          <!-- @if ($motorbike->status === 'available')
            <a href="{{ route('rentals.create', ['motorbike' => $motorbike->id]) }}" class="btn btn-primary">
              Sewa Sekarang
            </a>
          @else
            <div class="alert alert-warning mt-3">
              Motor ini sedang {{ $motorbike->status === 'rented' ? 'disewa' : 'dalam perawatan' }}.
            </div>
          @endif -->
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

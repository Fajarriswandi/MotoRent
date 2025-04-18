@extends('layouts.app')

@section('content')
    <div class="container">
        <h3 class="mb-4">Detail Motor (Admin)</h3>

        <div class="row">
            <div class="col-md-5">
                @if($motorbike->image)
                    <img src="{{ asset('storage/' . $motorbike->image) }}" class="img-fluid rounded shadow-sm mb-3"
                        alt="Foto Motor">
                @else
                    <div class="bg-light text-center py-5 border rounded">Tidak ada gambar</div>
                @endif
            </div>
            <div class="col-md-7">
                <table class="table table-bordered">
                    <tr>
                        <th>Merek</th>
                        <td>{{ $motorbike->brand }}</td>
                    </tr>
                    <tr>
                        <th>Model</th>
                        <td>{{ $motorbike->model }}</td>
                    </tr>
                    <tr>
                        <th>Tahun</th>
                        <td>{{ $motorbike->year }}</td>
                    </tr>
                    <tr>
                        <th>Warna</th>
                        <td>{{ $motorbike->color }}</td>
                    </tr>
                    <tr>
                        <th>Nomor Plat</th>
                        <td>{{ $motorbike->license_plate }}</td>
                    </tr>
                    <tr>
                        <th>Status Teknis</th>
                        <td>
                            <span class="badge bg-{{ 
                                            $motorbike->technical_status === 'active' ? 'success' :
        ($motorbike->technical_status === 'maintenance' ? 'secondary' : 'danger') 
                                        }}">
                                {{ ucfirst($motorbike->technical_status) }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th>Harga per Jam</th>
                        <td>Rp{{ number_format($motorbike->rental_price_hour, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Harga per Hari</th>
                        <td>Rp{{ number_format($motorbike->rental_price_day, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Harga per Minggu</th>
                        <td>Rp{{ number_format($motorbike->rental_price_week, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Dibuat pada</th>
                        <td>{{ $motorbike->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Terakhir diperbarui</th>
                        <td>{{ $motorbike->updated_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>

                <h4 class="mt-5">Jadwal Penyewaan</h4>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Customer</th>
                                <th>Tanggal Sewa</th>
                                <th>Status</th>
                                <th>Aksi</th> <!-- Tambahkan kolom aksi -->
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($motorbike->rentals->sortByDesc('start_date') as $rental)
                                                    @php
                                                        $today = \Carbon\Carbon::today();
                                                        $start = \Carbon\Carbon::parse($rental->start_date);
                                                        $end = \Carbon\Carbon::parse($rental->end_date);

                                                        if ($rental->is_cancelled) {
                                                            $status = 'Dibatalkan';
                                                            $badge = 'danger';
                                                        } elseif ($rental->is_completed) {
                                                            $status = 'Selesai';
                                                            $badge = 'secondary';
                                                        } elseif ($start->gt($today)) {
                                                            $status = 'Mendatang';
                                                            $badge = 'info';
                                                        } else {
                                                            $status = 'Aktif';
                                                            $badge = 'success';
                                                        }
                                                    @endphp

                                                    @php
                                                        $icons = [
                                                            'Aktif' => 'bi-check-circle-fill',
                                                            'Mendatang' => 'bi-clock-fill',
                                                            'Selesai' => 'bi-flag-fill',
                                                            'Dibatalkan' => 'bi-x-circle-fill'
                                                        ];
                                                    @endphp

                                                    <tr>
                                                        <td>{{ $rental->customer->name ?? '-' }}</td>
                                                        <td>{{ $start->translatedFormat('d M Y') }} - {{ $end->translatedFormat('d M Y') }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $badge }}">
                                                                <i class="bi {{ $icons[$status] ?? 'bi-question-circle-fill' }} me-1"></i>
                                                                {{ $status }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if (!$rental->is_cancelled && !$rental->is_completed)
                                                                <form method="POST" action="{{ route('admin.rentals.cancel', $rental->id) }}"
                                                                    class="cancel-form" data-rental-id="{{ $rental->id }}">
                                                                    @csrf
                                                                    <button class="btn btn-sm btn-danger">
                                                                        <i class="bi bi-trash-fill"></i>
                                                                    </button>

                                                                </form>
                                                            @else
                                                                <span class="text-muted">—</span>
                                                            @endif
                                                        </td>

                                                    </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada penyewaan.</td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>


                <a href="{{ route('motorbikes.edit', $motorbike->id) }}" class="btn btn-warning">Edit Motor</a>
                <a href="{{ route('motorbikes.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.cancel-form').forEach(form => {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        const rentalId = this.dataset.rentalId;

                        Swal.fire({
                            title: 'Batalkan Penyewaan?',
                            text: 'Penyewaan ini akan ditandai sebagai dibatalkan permanen.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, Batalkan!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.submit();
                            }
                        });
                    });
                });
            });
        </script>
    @endpush



@endsection
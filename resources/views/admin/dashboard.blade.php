@extends('layouts.app')

@section('content')

<div class="headerContent">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <div class="bg-primary1 text-white rounded p-2">
                <h3 class="titlePage">Dashboard Admin</h3>
            </div>
            <div class="p-2">

                <!-- <div class="input-group">
                    <input type="text" aria-label="First name" class="form-control">
                    <input type="text" aria-label="Last name" class="form-control">
                    <button class="btn btn-outline-secondary" type="button">Filter</button>
                    <button class="btn btn-outline-secondary" type="button">Clear</button>
                </div> -->

                {{-- Filter Tanggal --}}
                <form method="GET" class="mb-4">
                    <div class="input-group">
                        <input
                            type="date"
                            name="start_date"
                            class="form-control"
                            placeholder="Tanggal Mulai"
                            value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                        <input
                            type="date"
                            name="end_date"
                            class="form-control"
                            placeholder="Tanggal Akhir"
                            value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Clear
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<div class="mainContent">
    <div class="container-fluid">

        <div class="row mb-1">
            <div class="col-md-3">
                <!-- Total Penyewa -->
                <div class="card widgetCard position-relative">
                    <div
                        class="position-absolute top-0 end-0 mt-3 me-3 bg-primary1 px-2 py-1 rounded small m-2"
                        style="z-index: 10;"
                        data-bs-toggle="popover"
                        data-bs-trigger="hover"
                        data-bs-placement="left"
                        data-bs-html="true"
                        data-bs-custom-class="custom-popover"
                        data-bs-title="Penjelasan"
                        data-bs-content="
                        <strong>Total Penyewa</strong> <br/>
                        Jumlah total ini mengikuti tanggal filter yang di pilih. <br/>
                        ">
                        info <x-icon name="uil:info-circle" class="sm ms-1" />
                    </div>

                    <div class="card-header">
                        <h5><span><x-icon name="si:dashboard-fill" class="sm me-1" /></span> Total Penyewa</h5>
                    </div>
                    <div class="card-body">
                        <small>Periode ini</small>
                        <h2>{{ $rentalsThisMonth }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <!-- Total Pendapatan -->
                <div class="card widgetCard position-relative">
                    <div
                        class="position-absolute top-0 end-0 mt-3 me-3 bg-primary1 px-2 py-1 rounded small m-2"
                        style="z-index: 10;"
                        data-bs-toggle="popover"
                        data-bs-trigger="hover"
                        data-bs-placement="left"
                        data-bs-html="true"
                        data-bs-custom-class="custom-popover"
                        data-bs-title="Penjelasan"
                        data-bs-content="
                        <strong>Total Pendapatan Saat ini</strong> <br/>
                        Default totalnya di ambil data per minggu ini, dan total ini juga menyesuaikan hasil filter tanggal. <br/><br/>

                        <strong>Total Pendapatan Bulan ini</strong> <br/>
                        Ini adalah total pendapatanper bulan ini.
                        ">
                        info <x-icon name="uil:info-circle" class="sm ms-1" />
                    </div>

                    <div class="card-header">
                        <h5><span><x-icon name="si:dashboard-fill" class="sm me-1" /></span> Total Pendapatan</h5>

                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small>Saat ini</small>
                                <h2>Rp{{ number_format($revenueInRange, 0, ',', '.') }}</h2>
                            </div>
                            <div>
                                <small>Bulan ini</small>
                                <h2>Rp{{ number_format($revenueMonth, 0, ',', '.') }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Status Motor -->
                <div class="card widgetCard">
                    <div class="card-header">
                        <h5><span><x-icon name="si:dashboard-fill" class="sm me-1" /></span> Status Motor Saat ini</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small>Tersedia</small>
                                <h2>{{ $motorCounts['active'] }}</h2>
                            </div>
                            <div>
                                <small>Tidak tersedia</small>
                                <h2>{{ $motorCounts['inactive'] }}</h2>
                            </div>
                            <div>
                                <small>Perawatan</small>
                                <h2>{{ $motorCounts['maintenance'] }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4" style="min-height: 300px;">
            <div class="col-md-8 mb-3">
                <!-- Insight Statistik -->
                <div class="card widgetCard h-100 position-relative">
                    <div
                        class="position-absolute top-0 end-0 mt-3 me-3 bg-primary1 px-2 py-1 rounded small m-2"
                        style="z-index: 10;"
                        data-bs-toggle="popover"
                        data-bs-trigger="hover"
                        data-bs-placement="left"
                        data-bs-html="true"
                        data-bs-custom-class="custom-popover"
                        data-bs-title="Penjelasan"
                        data-bs-content="
                        <strong>Pendapatan</strong> <br/>
                        Data yang berasal dari total transaksi penyewaan motor yang telah selesai (is_completed)<br/>
                        <strong>Pendapatan Hilang</strong> <br/>
                        Menunjukkan nilai transaksi yang dibatalkan (is_cancelled) dalam periode tertentu.<br/>
                        ">
                        info <x-icon name="uil:info-circle" class="sm ms-1" />
                    </div>

                    <div class="card-header">
                        <h5><span><x-icon name="si:dashboard-fill" class="sm me-1" /></span> Insight Data</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="weeklyChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-1">
                <!-- Penyewa habis hari ini -->
                <div class="card widgetCard widgetCardBodyNone h-100">
                    <div class="card-header">
                        <h5><span><x-icon name="si:dashboard-fill" class="sm me-1" /></span> Penyewa Habis Hari ini</h5>
                    </div>
                    <div class="card-body p-0">

                        <table class="table table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer</th>
                                    <th>Motor</th>
                                    <th>Tanggal Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rentalsEndingToday as $rental)
                                <tr>
                                    <td>{{ $rental->customer->name }}</td>
                                    <td>{{ $rental->motorbike->brand }} {{ $rental->motorbike->model }}</td>
                                    <td>{{ \Carbon\Carbon::parse($rental->end_date)->format('d M Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada penyewaan yang berakhir hari ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <a href="{{ route('motorbikes.index') }}" class="ps-4 ms-2 btn btn-link"><x-icon name="mynaui:external-link" class="sm me-1" /> Selengkapnya</a>

                    </div>
                </div>
            </div>
        </div>


        <div class="row mb-3" style="min-height: 300px;">
            <div class="col-md-6 mb-4">
                <!-- Jadwal Motor Akan Disewa Besok -->
                <div class="card widgetCard widgetCardBodyNone h-100">
                    <div class="card-header">
                        <h4>Jadwal Motor Akan Disewa Besok</h4>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer</th>
                                    <th>Motor</th>
                                    <th>Tanggal Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rentalsTomorrow as $rental)
                                <tr>
                                    <td>{{ $rental->customer->name }}</td>
                                    <td>{{ $rental->motorbike->brand }} {{ $rental->motorbike->model }}</td>
                                    <td>{{ \Carbon\Carbon::parse($rental->start_date)->format('d M Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada jadwal sewa besok.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <!-- Penyewa yang habis besok -->
                <div class="card widgetCard widgetCardBodyNone h-100">
                    <div class="card-header">
                        <h4>Penyewa yang Habis Besok</h4>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer</th>
                                    <th>Motor</th>
                                    <th>Tanggal Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rentalsEndingTomorrow as $rental)
                                <tr>
                                    <td>{{ $rental->customer->name }}</td>
                                    <td>{{ $rental->motorbike->brand }} {{ $rental->motorbike->model }}</td>
                                    <td>{{ \Carbon\Carbon::parse($rental->end_date)->format('d M Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Tidak ada penyewaan yang berakhir besok.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5 Motor paling sering di sewa -->
    <div class="row mb-3 p-3">
        <h3><x-icon name="noto:fire" class="lg me-1 mb-2" /></span>Motor Paling Sering Disewa</h3>

        @forelse ($topMotorbikes as $item)
        <div class="col-md-3">
            <a href="{{ route('motorbikes.admin.show', $item->motorbike) }}" style="text-decoration: none;">
                <div class="card widgetCard widgetCardBodyNone widgetCardProduct position-relative">

                    <small class="position-absolute top-0 start-0 bg-primary1 text-white1 px-2 py-1 mt-3 ms-3 rounded small text-warning" style="z-index: 10;">
                        @php
                        $rank = $loop->iteration;
                        $suffix = match($rank) {
                        1 => 'st',
                        2 => 'nd',
                        3 => 'rd',
                        default => 'th'
                        };
                        @endphp
                        <x-icon name="solar:cup-star-broken" class="sm" />{{ $rank }}{{ $suffix }}
                    </small>
                    {{-- Gambar motor --}}


                    <div class="card-body text-center">
                        <div class="p-3 text-center">
                            @if ($item->motorbike->image)
                            <img
                                src="{{ asset('storage/' . $item->motorbike->image) }}"
                                alt="{{ $item->motorbike->brand }} {{ $item->motorbike->model }}"
                                class="img-fluid"
                                style="height: 180px; object-fit: cover;">
                            @else
                            <div class="text-muted" style="font-size: 40px;">
                                <i class="bi bi-image" title="Gambar tidak tersedia"></i>
                            </div>
                            @endif
                        </div>
                        <small class="text-muted position-absolute top-0 end-0 bg-primary1 text-white1 px-3 py-3 rounded-end small" style="z-index: 10;"><i class="bi bi-clock-history"></i> {{ $item->total }}x disewa</small>
                    </div>

                    <div class="card-footer text-center pt-3 pb-3 d-flex flex-row align-items-center justify-content-between">
                        <div class="text-truncate text-start">
                            {{ $item->motorbike->brand }} {{ $item->motorbike->model }} <br>
                            <small><span class="text-muted">{{ $item->motorbike->license_plate }}</span></small>
                        </div>

                        <div class="fw-bold text-end">
                            Rp. {{ number_format($item->motorbike->rental_price_day, 0, ',', '.') }} <br>
                            <smal class="fw-normal">Per Day</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12 text-center text-muted">
            Tidak ada data penyewaan.
        </div>
        @endforelse


    </div>

</div>
</div>






@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('weeklyChart')?.getContext('2d');

    if (ctx) {
        const weeklyStats = @json($weeklyStats);

        const labels = weeklyStats.map(item => item.date);
        const revenues = weeklyStats.map(item => item.revenue);
        const lostRevenues = weeklyStats.map(item => item.lost_revenue);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: revenues,
                        borderWidth: 2,
                        borderColor: '#01958A',
                        tension: 0.3,
                        fill: false
                    },
                    {
                        label: 'Pendapatan Hilang (Rp)',
                        data: lostRevenues,
                        borderWidth: 2,
                        borderDash: [5, 5],
                        borderColor: '#DC3545',
                        tension: 0.3,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (context.dataset.label.includes('Pendapatan')) {
                                    return context.dataset.label + ': Rp' + context.formattedValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                }
                                return context.dataset.label + ': ' + context.formattedValue;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            }
                        }
                    }
                }
            }
        });
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl)
        })
    });
</script>
@endpush
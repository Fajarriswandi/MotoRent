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

        <div class="row mb-3">
            <div class="col-md-3">
                <!-- Total Penyewa -->
                <div class="card widgetCard">
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
                <div class="card widgetCard">
                    <div class="card-header">
                        <h5><span><x-icon name="si:dashboard-fill" class="sm me-1" /></span> Total Pendapatan</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small>Hari ini</small>
                                <h2>Rp{{ number_format($revenueToday, 0, ',', '.') }}</h2>
                            </div>
                            <div>
                                <small>Periode ini</small>
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

        <div class="row mb-3" style="min-height: 300px;">
            <div class="col-md-8">
                <!-- Insight Statistik -->
                <div class="card widgetCard h-100">
                    <div class="card-header">
                        <h5><span><x-icon name="si:dashboard-fill" class="sm me-1" /></span> Insight Data</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="weeklyChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
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
            <div class="col-md-6">
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

            <div class="col-md-6">
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
            <div class="card widgetCard widgetCardBodyNone">
                {{-- Gambar motor --}}
                <div class="p-3 text-center">
                    @if ($item->motorbike->image)
                    <img
                        src="{{ asset('storage/' . $item->motorbike->image) }}"
                        alt="{{ $item->motorbike->brand }} {{ $item->motorbike->model }}"
                        class="img-fluid"
                        style="height: 100px; object-fit: cover;">
                    @else
                    <div class="text-muted" style="font-size: 40px;">
                        <i class="bi bi-image" title="Gambar tidak tersedia"></i>
                    </div>
                    @endif
                </div>

                <div class="card-body text-center">
                    <strong>{{ $item->motorbike->brand }}</strong><br>
                    {{ $item->motorbike->model }}<br>
                    <span class="text-muted">{{ $item->motorbike->license_plate }}</span>
                </div>

                <div class="card-footer text-center">
                    <div>{{ $item->total }} penyewaan</div>
                    <div class="fw-bold">Rp. {{ number_format($item->motorbike->rental_price_day, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center text-muted">
            Tidak ada data penyewaan.
        </div>
        @endforelse


    </div>




    {{-- Statistik Motor --}}
    <div class="row g-3 mt-2 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-success">
                <div class="card-body">
                    <h5 class="card-title">Motor Aktif</h5>
                    <h2 class="text-success">{{ $motorCounts['active'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-warning">
                <div class="card-body">
                    <h5 class="card-title">Motor Maintenance</h5>
                    <h2 class="text-warning">{{ $motorCounts['maintenance'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-secondary">
                <div class="card-body">
                    <h5 class="card-title">Motor Tidak Aktif</h5>
                    <h2 class="text-muted">{{ $motorCounts['inactive'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik Penyewaan dan Pendapatan --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-info">
                <div class="card-body">
                    <h5 class="card-title">Total Penyewaan</h5>
                    <h2 class="text-info">{{ $rentalsThisMonth }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-primary">
                <div class="card-body">
                    <h5 class="card-title">Pendapatan</h5>
                    <p class="mb-1">Hari Ini: <strong>Rp{{ number_format($revenueToday, 0, ',', '.') }}</strong></p>
                    <p>Periode Ini: <strong>Rp{{ number_format($revenueMonth, 0, ',', '.') }}</strong></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik Performa Mingguan --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Grafik Performa Mingguan</h5>
            <canvas id="weeklyChart" height="100"></canvas>
        </div>
    </div>

    {{-- Top 5 Motor Paling Sering Disewa --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Top 5 Motor Paling Sering Disewa</h5>
            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Motor</th>
                        <th>Jumlah Sewa</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($topMotorbikes as $item)
                    <tr>
                        <td>{{ $item->motorbike->brand }} {{ $item->motorbike->model }} -
                            {{ $item->motorbike->license_plate }}
                        </td>
                        <td>{{ $item->total }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-center">Tidak ada data penyewaan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Jumlah Customer yang Menyewa --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Jumlah Customer Menyewa Sesuai filter</h5>
            <h2 class="text-primary">{{ $uniqueCustomerCount }}</h2>
        </div>
    </div>

    {{-- Notifikasi Penyewaan yang Berakhir Besok --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Notifikasi: Penyewaan Berakhir Besok</h5>
            <table class="table table-bordered table-sm">
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

    {{-- Jadwal Motor Akan Disewa Besok --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Jadwal Motor Akan Disewa Besok</h5>
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Customer</th>
                        <th>Motor</th>
                        <th>Tanggal</th>
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

    {{-- Penyewaan Berakhir Hari Ini --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Penyewaan Berakhir Hari Ini</h5>
            <table class="table table-bordered table-sm">
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
        </div>
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
@endpush
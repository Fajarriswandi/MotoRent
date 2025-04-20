@extends('layouts.app')

@section('content')

    <div class="headerContent">
        <div class="container-fluid">
            <div class="d-flex flex-wrap w-100 justify-content-between align-items-center">
                <div class="bg-primary1 text-white rounded p-2">
                    <h3 class="titlePage">Dashboard Admin</h3>
                </div>
                <div class="bg-secondary1 text-white rounded p-2">

                    {{-- Filter Tanggal --}}
                    <form method="GET" class="row g-3 mb-4 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control"
                                value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" name="end_date" class="form-control"
                                value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-funnel"></i> Filter
                            </button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary w-100">
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
            {{-- Statistik Motor --}}
            <div class="row g-3 mb-4">
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
            const rentals = weeklyStats.map(item => item.rentals);
            const revenues = weeklyStats.map(item => item.revenue);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Jumlah Penyewaan',
                            data: rentals,
                            borderWidth: 2,
                            borderColor: '#0d6efd',
                            fill: false
                        },
                        {
                            label: 'Pendapatan (Rp)',
                            data: revenues,
                            borderWidth: 2,
                            borderColor: '#198754',
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    </script>
@endpush
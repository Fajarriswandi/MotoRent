@extends('layouts.app')

@section('content')
<h3 class="mb-4">Laporan Penyewaan</h3>

<div class="d-flex justify-content-end mb-3 gap-2">
    <a href="{{ route('reports.export.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
        class="btn btn-outline-danger" target="_blank">
        <i class="bi bi-file-earmark-pdf"></i> Export PDF
    </a>
    <a href="{{ route('reports.export.excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
        class="btn btn-outline-success">
        <i class="bi bi-file-earmark-excel"></i> Export Excel
    </a>
</div>


{{-- Form Filter --}}
<form method="GET" class="mb-4">
    <div class="input-group">
        <input
            type="date"
            name="start_date"
            class="form-control"
            value="{{ $startDate }}"
            placeholder="Tanggal Mulai">
        <input
            type="date"
            name="end_date"
            class="form-control"
            value="{{ $endDate }}"
            placeholder="Tanggal Akhir">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-funnel"></i> Filter
        </button>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-clockwise"></i> Reset
        </a>
    </div>
</form>


{{-- Ringkasan --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-primary shadow-sm">
            <div class="card-body">
                <h6>Total Sewa</h6>
                <h4>{{ $summary['total_rentals'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-success shadow-sm">
            <div class="card-body">
                <h6>Total Pendapatan</h6>
                <h4>Rp{{ number_format($summary['total_revenue'], 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-warning shadow-sm">
            <div class="card-body">
                <h6>Customer Unik</h6>
                <h4>{{ $summary['unique_customers'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-info shadow-sm">
            <div class="card-body">
                <h6>Motor Terfavorit</h6>
                @if($summary['top_motorbike'])
                <h6 class="mb-0">{{ $summary['top_motorbike']->brand }} {{ $summary['top_motorbike']->model }}</h6>
                <small class="text-muted">{{ $summary['top_motorbike']->license_plate }}</small>
                @else
                <p class="text-muted mb-0">-</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Grafik Harian--}}
<div class="row g-4 mb-4">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Grafik Jumlah Penyewaan (Per Hari)</h5>
                <canvas id="dailyChart" height="120"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Status Penyewaan</h5>
                <canvas id="statusPie" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Grafik Bulanan --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Grafik Penyewaan 12 Bulan Terakhir</h5>
        <canvas id="monthlyChart" height="100"></canvas>
    </div>
</div>

{{-- Grafik Mingguan --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Penyewaan 7 Hari Terakhir</h5>
        <canvas id="weeklyChart" height="100"></canvas>
    </div>
</div>



{{-- Tabel Hasil --}}
<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title">Data Penyewaan</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Motor</th>
                        <th>Customer</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rentals as $rental)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $rental->start_date }} - {{ $rental->end_date }}</td>
                        <td>{{ $rental->motorbike->brand }} {{ $rental->motorbike->model }}</td>
                        <td>{{ $rental->customer->name }}</td>
                        <td>Rp{{ number_format($rental->total_price, 0, ',', '.') }}</td>
                        <td>
                            @if($rental->is_completed)
                            <span class="badge bg-success">Selesai</span>
                            @elseif($rental->is_cancelled)
                            <span class="badge bg-danger">Dibatalkan</span>
                            @else
                            <span class="badge bg-secondary">Berlangsung</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data penyewaan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dailyStats = @json($dailyStats);
        const statusCounts = @json($statusCounts);

        // Line Chart - Daily Rentals
        const dailyCtx = document.getElementById('dailyChart')?.getContext('2d');
        if (dailyCtx) {
            new Chart(dailyCtx, {
                type: 'line',
                data: {
                    labels: dailyStats.map(item => item.date),
                    datasets: [{
                        label: 'Jumlah Sewa',
                        data: dailyStats.map(item => item.total),
                        backgroundColor: '#0d6efd33',
                        borderColor: '#0d6efd',
                        borderWidth: 2,
                        tension: 0.3,
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            stepSize: 1
                        }
                    }
                }
            });
        }

        // Pie Chart - Rental Status
        const pieCtx = document.getElementById('statusPie')?.getContext('2d');
        if (pieCtx) {
            new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: ['Selesai', 'Dibatalkan'],
                    datasets: [{
                        data: [
                            statusCounts.completed ?? 0,
                            statusCounts.cancelled ?? 0
                        ],
                        backgroundColor: ['#198754', '#dc3545']
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    });
</script>


<script>
    const monthlyStats = @json($monthlyStats);
    const weeklyStats = @json($weeklyStats);

    // Grafik Bulanan
    const monthlyCtx = document.getElementById('monthlyChart')?.getContext('2d');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: monthlyStats.map(item => item.month),
                datasets: [{
                    label: 'Jumlah Sewa',
                    data: monthlyStats.map(item => item.total),
                    backgroundColor: '#0dcaf0'
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        stepSize: 1
                    }
                }
            }
        });
    }

    // Grafik Mingguan
    const weeklyCtx = document.getElementById('weeklyChart')?.getContext('2d');
    if (weeklyCtx) {
        new Chart(weeklyCtx, {
            type: 'line',
            data: {
                labels: weeklyStats.map(item => item.label),
                datasets: [{
                    label: 'Jumlah Sewa',
                    data: weeklyStats.map(item => item.value),
                    borderColor: '#ffc107',
                    backgroundColor: '#ffc10733',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
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
@extends('layouts.app')

@section('content')
<br><br><br><br>
<div class="container py-4">
    <!-- Date Filter -->
    <div class="card widgetCard mb-4 shadow-sm">
        <div class="card-body d-flex flex-wrapx gap-2x align-items-center justify-content-between">
            <form method="GET" class="d-flex flex-wrap gap-2">
                <div class="form-floating">
                    <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $startDate }}">
                    <label for="start_date">Start Date</label>
                </div>
                <div class="form-floating">
                    <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $endDate }}">
                    <label for="end_date">End Date</label>
                </div>
                <button type="submit" class="btn btn-outline-secondary ps-4 pe-4">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary d-flex align-items-center ps-4 pe-4 ">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>

            </form>
            <div>
                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                    <a type="button"
                        href="{{ route('reports.export.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                        class="
                                btn btn-outline-secondary pt-3 pb-3 ps-3 pe-3"><i class="bi bi-file-earmark-pdf"></i> PDF</a>

                    <a type="button"
                        href="{{ route('reports.export.excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                        class="
                                btn btn-outline-secondary pt-3 pb-3 ps-3 pe-3"><i class="bi bi-file-earmark-excel"></i> Excel</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Performance -->
    <div class="card widgetCard mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="fw-semibold mb-3">Financial Performance</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <h6>Total Revenue</h6>
                        <h4 class="fw-bold">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <h6>Average Revenue Per Transaction</h6>
                        <h4 class="fw-bold">Rp {{ number_format($summary['average_revenue'], 0, ',', '.') }}</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <h6>Highest Daily Revenue</h6>
                        <h4 class="fw-bold">Rp {{ number_format($summary['highest_daily_revenue'], 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
            <h6 class="mb-3">Daily Revenue</h6>
            <canvas id="chartRevenue" height="120"></canvas>
        </div>
    </div>

    <!-- Business Performance and Top Motor -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card widgetCard h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">Business Performance</h5>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <h6>Total Rentals</h6>
                                <h4 class="fw-bold">{{ $summary['total_rentals'] }}</h4>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <h6>Unique Customers</h6>
                                <h4 class="fw-bold">{{ $summary['unique_customers'] }}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <h6>Repeat Customer Rate</h6>
                                <h4 class="fw-bold">{{ $summary['repeat_customer_rate'] }}%</h4>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <h6>Cancellation Rate</h6>
                                <h4 class="fw-bold">{{ $summary['cancellation_rate'] }}%</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-12">
                            <div class="border rounded p-3 h-100">
                                <h6>Fleet Utilization</h6>
                                <h4 class="fw-bold">{{ $summary['fleet_utilization'] }}%</h4>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card widgetCard h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">Top Rented Motorcycles</h5>
                    <div class="p-2">
                        <canvas id="chartTopMotor" height="180"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bonus Insight -->
    <div class="card widgetCard shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-row align-items-center justify-content-between mb-3">
                <h5 class="fw-semibold">Anual Income</h5>
                <form method="GET" class="mb-4">
                    <div class="d-flex gap-2 align-items-center">
                        <label for="year" class="form-label mb-0">Select Year:</label>
                        <select id="yearSelector" class="form-select form-select-sm w-auto">
                            @for ($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </form>
            </div>
            <canvas id="monthlyChart" height="100"></canvas>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // const chartTopMotor = document.getElementById('chartTopMotor');

    const dailyStats = @json($dailyStats);

    new Chart(document.getElementById('chartRevenue'), {
        type: 'line',
        data: {
            labels: dailyStats.map(item => item.date),
            datasets: [{
                label: 'Daily Revenue',
                data: dailyStats.map(item => item.total),
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderColor: '#0d6efd',
                tension: 0.3,
                fill: true,
            }]
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

    const topMotorStats = @json($topMotorStats);

    new Chart(document.getElementById('chartTopMotor'), {
        type: 'bar',
        data: {
            labels: topMotorStats.map(item => item.motorbike),
            datasets: [{
                label: 'Total Rentals',
                data: topMotorStats.map(item => item.total),
                backgroundColor: '#0d6efd'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    let monthlyChart;
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');

    function renderMonthlyChart(data) {
        if (monthlyChart) monthlyChart.destroy();

        monthlyChart = new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: data.map(item => item.month),
                datasets: [{
                    label: 'Annual income ',
                    data: data.map(item => item.total),
                    backgroundColor: '#0d6efd'
                }]
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

    // Initial render
    renderMonthlyChart(@json($monthlyStats));

    document.getElementById('yearSelector').addEventListener('change', function() {
        const year = this.value;

        fetch(`{{ route('reports.monthlyData') }}?year=${year}`)
            .then(res => res.json())
            .then(data => renderMonthlyChart(data));
    });
</script>



@endsection
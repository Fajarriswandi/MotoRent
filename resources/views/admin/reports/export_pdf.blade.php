<!DOCTYPE html>
<html>

<head>
    <title>Laporan Penyewaan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        h3,
        h4 {
            margin: 0;
            padding: 0;
        }

        .summary {
            margin-top: 20px;
        }

        .summary-table td {
            padding: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 6px;
            text-align: left;
        }
    </style>
</head>

<body>
    <h3>Laporan Penyewaan</h3>
    <p>Periode: {{ $startDate }} s/d {{ $endDate }}</p>

    {{-- Ringkasan Statistik --}}
    <div class="summary">
        <table class="summary-table">
            <tr>
                <td><strong>Total Penyewaan</strong></td>
                <td>: {{ $summary['total_rentals'] }}</td>
            </tr>
            <tr>
                <td><strong>Total Pendapatan</strong></td>
                <td>: Rp{{ number_format($summary['total_revenue'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Customer Unik</strong></td>
                <td>: {{ $summary['unique_customers'] }}</td>
            </tr>
            <tr>
                <td><strong>Motor Terfavorit</strong></td>
                <td>:
                    @if ($summary['top_motorbike'])
                        {{ $summary['top_motorbike']->brand }} {{ $summary['top_motorbike']->model }}
                        ({{ $summary['top_motorbike']->license_plate }})
                    @else
                        -
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- Tabel Data Penyewaan --}}
    <h4 style="margin-top: 30px;">Detail Penyewaan</h4>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Motor</th>
                <th>Customer</th>
                <th>Tanggal</th>
                <th>Total Harga</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rentals as $rental)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $rental->motorbike->brand }} {{ $rental->motorbike->model }}</td>
                    <td>{{ $rental->customer->name }}</td>
                    <td>{{ $rental->start_date }} - {{ $rental->end_date }}</td>
                    <td>Rp{{ number_format($rental->total_price, 0, ',', '.') }}</td>
                    <td>
                        @if($rental->is_completed)
                            Selesai
                        @elseif($rental->is_cancelled)
                            Dibatalkan
                        @else
                            Berlangsung
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
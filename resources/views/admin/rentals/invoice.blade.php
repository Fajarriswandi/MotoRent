<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice Rental</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 14px;
        }

        h2 {
            margin-bottom: 0;
        }

        .invoice-box {
            border: 1px solid #eee;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        td,
        th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .text-end {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <h2>Invoice Penyewaan</h2>
        <p>No. Invoice: #{{ $rental->id }}</p>
        <p>Tanggal Cetak: {{ now()->format('d M Y') }}</p>

        <hr>

        <p><strong>Customer:</strong> {{ $rental->customer->name }}<br>
            <strong>Email:</strong> {{ $rental->customer->email }}
        </p>

        <p><strong>Motor:</strong> {{ $rental->motorbike->brand }} {{ $rental->motorbike->model }}<br>
            <strong>Plat Nomor:</strong> {{ $rental->motorbike->license_plate }}
        </p>

        <table>
            <thead>
                <tr>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Akhir</th>
                    <th>Harga / Hari</th>
                    <th>Durasi</th>
                    <th>Total Harga</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ \Carbon\Carbon::parse($rental->start_date)->format('d M Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($rental->end_date)->format('d M Y') }}</td>
                    <td>Rp{{ number_format($rental->motorbike->rental_price_day, 0, ',', '.') }}</td>
                    <td>
                        {{ \Carbon\Carbon::parse($rental->start_date)->diffInDays($rental->end_date) + 1 }} hari
                    </td>
                    <td><strong>Rp{{ number_format($rental->total_price, 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>

        <p class="text-end mt-3">Terima kasih telah menggunakan layanan kami!</p>
    </div>
</body>

</html>
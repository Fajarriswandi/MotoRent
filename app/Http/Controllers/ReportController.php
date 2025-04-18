<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\Customer;
use App\Models\Motorbike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RentalsReportExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Default tanggal: awal & akhir bulan ini
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();

        // Ambil data rental sesuai filter
        $rentals = Rental::with(['motorbike', 'customer'])
            ->whereBetween('start_date', [$startDate, $endDate])
            ->when($request->customer_id, fn($q) => $q->where('customer_id', $request->customer_id))
            ->when($request->motorbike_id, fn($q) => $q->where('motorbike_id', $request->motorbike_id))
            ->get();

        // Ringkasan statistik
        $summary = [
            'total_rentals' => $rentals->count(),
            'total_revenue' => $rentals->sum('total_price'),
            'unique_customers' => $rentals->pluck('customer_id')->unique()->count(),
            'top_motorbike' => $rentals->groupBy('motorbike_id')->sortByDesc(fn($g) => $g->count())->first()?->first()->motorbike ?? null,
        ];

        // Data grafik harian (line chart)
        $dailyStats = Rental::select(
            DB::raw('DATE(start_date) as date'),
            DB::raw('COUNT(*) as total')
        )
            ->whereBetween('start_date', [$startDate, $endDate])
            ->when($request->customer_id, fn($q) => $q->where('customer_id', $request->customer_id))
            ->when($request->motorbike_id, fn($q) => $q->where('motorbike_id', $request->motorbike_id))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Data pie chart status
        $statusCounts = Rental::selectRaw("
                SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN is_cancelled = 1 THEN 1 ELSE 0 END) as cancelled
            ")
            ->whereBetween('start_date', [$startDate, $endDate])
            ->when($request->customer_id, fn($q) => $q->where('customer_id', $request->customer_id))
            ->when($request->motorbike_id, fn($q) => $q->where('motorbike_id', $request->motorbike_id))
            ->first();

        // Grafik Bulanan (12 bulan terakhir)
        $monthlyStats = Rental::select(
            DB::raw("DATE_FORMAT(start_date, '%Y-%m') as month"),
            DB::raw("COUNT(*) as total")
        )
            ->whereBetween('start_date', [
                Carbon::now()->subMonths(11)->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Ringkasan Mingguan (7 hari terakhir)
        $weeklyStats = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $count = Rental::whereDate('start_date', $date)->count();
            $weeklyStats->push([
                'label' => $date->format('D, d M'),
                'value' => $count
            ]);
        }


        return view('admin.reports.index', [
            'rentals' => $rentals,
            'summary' => $summary,
            'customers' => Customer::all(),
            'motorbikes' => Motorbike::all(),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dailyStats' => $dailyStats,
            'statusCounts' => $statusCounts,
            'monthlyStats' => $monthlyStats,
            'weeklyStats' => $weeklyStats,
        ]);
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();

        $rentals = Rental::with(['motorbike', 'customer'])
            ->whereBetween('start_date', [$startDate, $endDate])
            ->get();

        $summary = [
            'total_rentals' => $rentals->count(),
            'total_revenue' => $rentals->sum('total_price'),
            'unique_customers' => $rentals->pluck('customer_id')->unique()->count(),
            'top_motorbike' => $rentals->groupBy('motorbike_id')->sortByDesc(fn($g) => $g->count())->first()?->first()->motorbike ?? null,
        ];

        $pdf = PDF::loadView('admin.reports.pdf', compact('rentals', 'startDate', 'endDate', 'summary'));
        return $pdf->download("Laporan_MotoRent_{$startDate}_sampai_{$endDate}.pdf");
    }


    public function exportExcel(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();

        $rentals = Rental::with(['motorbike', 'customer'])
            ->whereBetween('start_date', [$startDate, $endDate])
            ->get();

        return Excel::download(
            new RentalsReportExport($rentals, $startDate, $endDate),
            "Laporan_MotoRent_{$startDate}_sampai_{$endDate}.xlsx"
        );
    }




}

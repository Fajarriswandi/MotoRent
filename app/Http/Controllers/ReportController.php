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
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();

        $rentals = Rental::with(['motorbike', 'customer'])
            ->whereBetween('start_date', [$startDate, $endDate])
            ->when($request->customer_id, fn($q) => $q->where('customer_id', $request->customer_id))
            ->when($request->motorbike_id, fn($q) => $q->where('motorbike_id', $request->motorbike_id))
            ->get();

        $totalRevenue = $rentals->sum('total_price');

        $dailyRevenue = collect($rentals)
            ->groupBy(fn($item) => Carbon::parse($item->start_date)->format('Y-m-d'))
            ->map(fn($group) => $group->sum('total_price'));

        $highestDailyRevenue = $dailyRevenue->max() ?? 0;

        $dailyStats = $rentals
            ->groupBy(fn($rental) => Carbon::parse($rental->start_date)->format('Y-m-d'))
            ->map(function ($group, $date) {
                return [
                    'date' => $date,
                    'total' => $group->sum('total_price'),
                ];
            })
            ->values();

        $customerRentalCounts = $rentals->groupBy('customer_id')->map->count();
        $repeatCustomerCount = $customerRentalCounts->filter(fn($count) => $count > 1)->count();
        $uniqueCustomerCount = $customerRentalCounts->count();

        $repeatCustomerRate = $uniqueCustomerCount > 0
            ? round(($repeatCustomerCount / $uniqueCustomerCount) * 100)
            : 0;



        $topMotorStats = $rentals
            ->groupBy('motorbike_id')
            ->map(function ($group, $id) {
                return [
                    'motorbike' => optional($group->first()->motorbike)->model,
                    'total' => $group->count(),
                ];
            })
            ->sortByDesc('total')
            ->values()
            ->take(5);

        $totalRentals = $rentals->count();
        $cancelledRentals = $rentals->where('is_cancelled', true)->count();

        $cancellationRate = $totalRentals > 0
            ? round(($cancelledRentals / $totalRentals) * 100)
            : 0;

        $motorbikes = Motorbike::all();

        $motorRentedIds = $rentals->pluck('motorbike_id')->unique();
        $fleetUtilization = $motorbikes->count() > 0
            ? round(($motorRentedIds->count() / $motorbikes->count()) * 100)
            : 0;

        $selectedYear = $request->year ?? Carbon::now()->year;

        $monthlyRevenue = Rental::whereYear('start_date', $selectedYear)
            ->where('is_cancelled', false)
            ->get()
            ->groupBy(fn($r) => Carbon::parse($r->start_date)->format('m'))
            ->map(fn($g) => $g->sum('total_price'))
            ->toArray();

        // Siapkan semua bulan biar urut 1-12 meskipun tidak semua ada datanya
        $monthlyStats = [];
        foreach (range(1, 12) as $month) {
            $monthStr = str_pad($month, 2, '0', STR_PAD_LEFT);
            $monthlyStats[] = [
                'month' => Carbon::create()->month($month)->format('M'),
                'total' => $monthlyRevenue[$monthStr] ?? 0
            ];
        }


        $summary = [
            'total_revenue' => $totalRevenue,
            'average_revenue' => $rentals->count() > 0 ? round($totalRevenue / $rentals->count()) : 0,
            'highest_daily_revenue' => $highestDailyRevenue,
            'total_rentals' => $rentals->count(),
            'unique_customers' => $uniqueCustomerCount,
            'repeat_customer_rate' => $repeatCustomerRate,
            'cancellation_rate' => $cancellationRate,
            'fleet_utilization' => $fleetUtilization,

        ];

        return view('admin.reports.index', [
            'rentals' => $rentals,
            'customers' => Customer::all(),
            'motorbikes' => Motorbike::all(),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'summary' => $summary,
            'dailyStats' => $dailyStats,
            'topMotorStats' => $topMotorStats,
            'monthlyStats' => $monthlyStats,
            'selectedYear' => $selectedYear,
        ]);
    }

    public function monthlyData(Request $request)
    {
        $year = $request->year ?? Carbon::now()->year;

        $monthlyRevenue = Rental::whereYear('start_date', $year)
            ->where('is_cancelled', false)
            ->get()
            ->groupBy(fn($r) => Carbon::parse($r->start_date)->format('m'))
            ->map(fn($g) => $g->sum('total_price'))
            ->toArray();

        $monthlyStats = [];
        foreach (range(1, 12) as $month) {
            $monthStr = str_pad($month, 2, '0', STR_PAD_LEFT);
            $monthlyStats[] = [
                'month' => Carbon::create()->month($month)->format('M'),
                'total' => $monthlyRevenue[$monthStr] ?? 0
            ];
        }

        return response()->json($monthlyStats);
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

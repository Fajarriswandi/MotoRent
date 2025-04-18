<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Motorbike;
use App\Models\Rental;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Akses hanya untuk admin & manager
        if (!in_array(auth()->user()->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized');
        }

        // Tanggal filter
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfWeek();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfWeek();

        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        // Statistik jumlah motor
        $motorCounts = [
            'active' => Motorbike::where('technical_status', 'active')->count(),
            'maintenance' => Motorbike::where('technical_status', 'maintenance')->count(),
            'inactive' => Motorbike::where('technical_status', 'inactive')->count(),
        ];

        // Penyewaan dalam rentang minggu ini
        $rentalsInRange = Rental::whereBetween('start_date', [$startDate, $endDate]);
        $rentalsThisMonth = $rentalsInRange->count();

        $revenueToday = Rental::whereDate('start_date', $today)->sum('total_price');
        $revenueMonth = $rentalsInRange->sum('total_price');

        // Penyewaan yang akan dimulai besok
        $rentalsTomorrow = Rental::with('motorbike', 'customer')
            ->whereDate('start_date', $tomorrow)
            ->where('is_cancelled', false)
            ->get();

        // Penyewaan yang berakhir hari ini
        $rentalsEndingToday = Rental::with('motorbike', 'customer')
            ->whereDate('end_date', $today)
            ->where('is_cancelled', false)
            ->get();

        // ✅ Penyewaan yang berakhir BESOK → untuk notifikasi
        $rentalsEndingTomorrow = Rental::with('motorbike', 'customer')
            ->whereDate('end_date', $tomorrow)
            ->where('is_cancelled', false)
            ->get();

        // Statistik mingguan (chart)
        $weeklyStats = collect();
        $diffDays = $startDate->diffInDays($endDate);

        for ($i = 0; $i <= $diffDays; $i++) {
            $date = $startDate->copy()->addDays($i)->toDateString();

            $dailyRentals = Rental::whereDate('start_date', $date)->count();
            $dailyRevenue = Rental::whereDate('start_date', $date)->sum('total_price');

            $weeklyStats->push([
                'date' => Carbon::parse($date)->format('d M'),
                'rentals' => $dailyRentals,
                'revenue' => $dailyRevenue,
            ]);
        }

        // Top 5 motor terlaris
        $topMotorbikes = Rental::with('motorbike')
            ->selectRaw('motorbike_id, COUNT(*) as total')
            ->whereBetween('start_date', [$startDate, $endDate])
            ->groupBy('motorbike_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // Jumlah customer unik
        $uniqueCustomerCount = Rental::whereBetween('start_date', [$startDate, $endDate])
            ->distinct('customer_id')
            ->count('customer_id');

        return view('admin.dashboard', compact(
            'motorCounts',
            'rentalsThisMonth',
            'revenueToday',
            'revenueMonth',
            'rentalsTomorrow',
            'rentalsEndingToday',
            'rentalsEndingTomorrow', // ⬅️ tambahan ini
            'startDate',
            'endDate',
            'weeklyStats',
            'topMotorbikes',
            'uniqueCustomerCount'
        ));
    }
}

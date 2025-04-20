<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\Motorbike;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class RentalAdminController extends Controller
{
    public function index()
    {
        $rentals = Rental::with('motorbike', 'customer')->latest()->paginate(5);
        return view('admin.rentals.index', compact('rentals'));
    }

    public function create()
    {
        $customers = Customer::all();
        $motorbikes = Motorbike::all();
        return view('admin.rentals.create', compact('customers', 'motorbikes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'motorbike_id' => 'required|exists:motorbikes,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $motorbike = Motorbike::findOrFail($request->motorbike_id);

        // ✅ 1. Validasi status teknis
        if ($motorbike->technical_status !== 'active') {
            return redirect()->back()
                ->with('rental_conflict', 'Motor ini sedang tidak aktif atau dalam perawatan.')
                ->withInput();
        }

        // ✅ 2. Validasi konflik tanggal sewa
        $conflict = Rental::where('motorbike_id', $motorbike->id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                    });
            })
            ->where('is_cancelled', false)
            ->where('is_completed', false)
            ->exists();

        if ($conflict) {
            return redirect()->back()
                ->with('rental_conflict', 'Motor ini sudah dibooking dalam rentang tanggal tersebut!')
                ->withInput();
        }

        // ✅ 3. Snapshot harga motor saat ini
        $priceDay = (int) $motorbike->rental_price_day;

        // ✅ 4. Hitung total berdasarkan harian
        $days = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)) + 1;
        $total = $priceDay * $days;

        // ✅ 5. Simpan penyewaan
        Rental::create([
            'customer_id' => $request->customer_id,
            'motorbike_id' => $motorbike->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_price' => $total,
            'price_day' => $priceDay,
            'is_approved' => true,
            'is_completed' => false,
            'is_cancelled' => false,
        ]);

        return redirect()->route('admin.rentals.index')->with('success', 'Penyewaan berhasil dibuat.');
    }





    public function updateStatus(Request $request, Rental $rental)
    {
        $request->validate([
            'technical_status' => 'required|in:active,inactive,maintenance'
        ]);

        $rental->motorbike->update(['technical_status' => $request->technical_status]);

        return redirect()->back()->with('success', 'Status teknis motor berhasil diperbarui.');
    }

    public function complete(Rental $rental)
    {
        $rental->update([
            'is_completed' => true,
            'is_cancelled' => false,
            'end_date' => now()->toDateString(), // atau now()->subDay()->toDateString() kalau mau lebih ketat
        ]);

        return redirect()->back()->with('success', 'Penyewaan selesai dan motor tersedia kembali.');
    }

    public function cancel(Rental $rental)
    {
        $rental->update([
            'is_cancelled' => true,
            'is_approved' => false,
            'is_completed' => false,
        ]);

        return redirect()->back()->with('success', 'Transaksi penyewaan dibatalkan.');
    }


    public function destroy(Rental $rental)
    {
        $rental->delete();

        return response()->json(['success' => true]);
    }

    public function invoice(Rental $rental)
    {
        $rental->load(['customer', 'motorbike']);

        $pdf = Pdf::loadView('admin.rentals.invoice', compact('rental'));
        return $pdf->download('invoice-rental-' . $rental->id . '.pdf');
    }


}

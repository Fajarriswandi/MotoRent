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
    public function index(Request $request)
    {
        $query = Rental::with(['motorbike', 'customer']);

        // 🔍 Filter Search Brand / Model
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('motorbike', function ($q) use ($search) {
                $q->where('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            });
        }

        // 📅 Filter Tanggal Mulai
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereDate('start_date', '<=', $request->end_date)
                ->whereDate('end_date', '>=', $request->start_date);
        }


        // ⚡ Filter Status Penyewaan
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'ongoing':
                    $query->where('start_date', '<=', now())
                        ->where('end_date', '>=', now())
                        ->where('is_cancelled', false)
                        ->where('is_completed', false);
                    break;
                case 'completed':
                    $query->where('is_completed', true);
                    break;
                case 'cancelled':
                    $query->where('is_cancelled', true);
                    break;
            }
        }

        // Pagination dengan query string tetap terbawa
        $rentals = $query->latest()->paginate(10)->appends($request->query());

        return view('admin.rentals.index', compact('rentals'));
    }


    public function show(Rental $rental)
    {
        $rental->load(['motorbike', 'customer']);

        // Ambil histori penyewa motor yang sama
        $rentalHistory = Rental::with('customer')
            ->where('motorbike_id', $rental->motorbike_id)
            ->where('id', '!=', $rental->id)
            ->latest()
            ->get();

        return view('admin.rentals.show', compact('rental', 'rentalHistory'));
    }

    public function edit(Rental $rental)
    {
        $customers = Customer::all();
        $motorbikes = Motorbike::all();
        return view('admin.rentals.edit', compact('rental', 'customers', 'motorbikes'));
    }

    public function update(Request $request, Rental $rental)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'motorbike_id' => 'required|exists:motorbikes,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Optional: bisa tambahkan pengecekan jika tanggal diubah menjadi invalid di masa lalu
        if (Carbon::parse($request->start_date)->lt(now()) && $rental->start_date != $request->start_date) {
            return redirect()->back()->with('rental_conflict', 'Tanggal mulai tidak boleh diubah ke masa lalu.');
        }

        // ❌ Jangan izinkan edit jika rental sudah berjalan
        if (Carbon::parse($rental->start_date)->isPast()) {
            return redirect()->back()->with('rental_conflict', 'Penyewaan yang sudah berjalan tidak bisa diedit.');
        }

        $motorbike = Motorbike::findOrFail($request->motorbike_id);

        if ($motorbike->technical_status !== 'active') {
            return redirect()->back()->with('rental_conflict', 'Motor ini sedang tidak aktif.');
        }

        // Validasi konflik jadwal baru (jangan cek terhadap rental ini sendiri)
        $conflict = Rental::where('motorbike_id', $motorbike->id)
            ->where('id', '!=', $rental->id)
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
            return redirect()->back()->with('rental_conflict', 'Motor ini sudah dibooking dalam rentang tanggal tersebut.');
        }

        $priceDay = (int) $motorbike->rental_price_day;
        $days = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)) + 1;
        $total = $priceDay * $days;

        $rental->update([
            'customer_id' => $request->customer_id,
            'motorbike_id' => $motorbike->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'price_day' => $priceDay,
            'total_price' => $total,
        ]);

        // return redirect()->route('admin.rentals.index')->with('success', 'Data penyewaan berhasil diperbarui.');

        if (Carbon::parse($rental->start_date)->isPast()) {
            return redirect()->route('admin.rentals.edit', $rental->id)
                ->with('rental_conflict', 'Penyewaan ini sudah berjalan, tidak bisa diedit.');
        }

        if ($motorbike->technical_status !== 'active') {
            return redirect()->route('admin.rentals.edit', $rental->id)
                ->with('rental_conflict', 'Motor ini sedang tidak aktif atau dalam perawatan.');
        }

        if ($conflict) {
            return redirect()->route('admin.rentals.edit', $rental->id)
                ->with('rental_conflict', 'Motor ini sudah dibooking dalam rentang tanggal tersebut!');
        }

        return redirect()->route('admin.rentals.index')->with('success', 'Data penyewaan berhasil diperbarui.');

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

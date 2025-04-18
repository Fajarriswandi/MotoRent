<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\Motorbike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RentalController extends Controller
{
    public function create(Request $request)
    {
        $motorbike = Motorbike::findOrFail($request->motorbike);
        return view('rentals.create', compact('motorbike'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'motorbike_id' => 'required|exists:motorbikes,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $motorbike = Motorbike::findOrFail($request->motorbike_id);

        if ($motorbike->status !== 'available') {
            return back()->withErrors(['motorbike_id' => 'Motor tidak tersedia untuk disewa.']);
        }

        $start = new \Carbon\Carbon($request->start_date);
        $end = new \Carbon\Carbon($request->end_date);
        $days = $start->diffInDays($end) + 1;
        $totalPrice = $motorbike->rental_price_day * $days;

        Rental::create([
            'user_id' => Auth::id(),
            'motorbike_id' => $motorbike->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_price' => $totalPrice,
            'is_approved' => false,
        ]);

        // Tandai motor sedang disewa
        // Status motor tidak diubah langsung, menunggu persetujuan admin

        return redirect()->route('rentals.index')->with('success', 'Penyewaan berhasil dilakukan.');
    }

    public function index()
    {
        $rentals = Rental::with('motorbike')->where('user_id', Auth::id())->latest()->get();
        return view('rentals.index', compact('rentals'));
    }

    public function approve(Rental $rental)
    {
        $rental->is_approved = true;
        $rental->save();

        $motorbike = $rental->motorbike;
        if ($motorbike) {
            $motorbike->status = 'rented';
            $motorbike->save();
        }

        return redirect()->back()->with('success', 'Penyewaan telah disetujui dan motor diset ke status rented.');
    }

    public function show(Rental $rental)
    {
        return view('rentals.show', compact('rental'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Motorbike;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MotorbikeController extends Controller
{
    public function index(Request $request)
    {
        $query = Motorbike::with('rentals');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%")
                    ->orWhere('license_plate', 'like', "%{$search}%");
            });
        }

        $now = now()->toDateString();

        if ($request->filled('status')) {
            if (in_array($request->status, ['rented', 'available'])) {
                if ($request->status === 'rented') {
                    $query->whereHas('rentals', function ($q) use ($now) {
                        $q->where('start_date', '<=', $now)
                            ->where('end_date', '>=', $now)
                            ->where('is_cancelled', false)
                            ->where('is_completed', false);
                    });
                } elseif ($request->status === 'available') {
                    $query->whereDoesntHave('rentals', function ($q) use ($now) {
                        $q->where('start_date', '<=', $now)
                            ->where('end_date', '>=', $now)
                            ->where('is_cancelled', false)
                            ->where('is_completed', false);
                    });
                }
            } else {
                $query->where('technical_status', $request->status);
            }
        }

        $motorbikes = $query->latest()->paginate(8);

        return view('motorbikes.index', compact('motorbikes'));
    }

    public function publicIndex(Request $request)
    {
        $query = Motorbike::with('rentals');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%")
                    ->orWhere('license_plate', 'like', "%{$search}%");
            });
        }

        $now = now()->toDateString();

        if ($request->filled('status')) {
            if (in_array($request->status, ['rented', 'available'])) {
                if ($request->status === 'rented') {
                    $query->whereHas('rentals', function ($q) use ($now) {
                        $q->where('start_date', '<=', $now)
                            ->where('end_date', '>=', $now);
                    });
                } elseif ($request->status === 'available') {
                    $query->whereDoesntHave('rentals', function ($q) use ($now) {
                        $q->where('start_date', '<=', $now)
                            ->where('end_date', '>=', $now);
                    });
                }
            } else {
                $query->where('technical_status', $request->status);
            }
        }

        $motorbikes = $query->latest()->paginate(6);

        return view('motorbikes.public', compact('motorbikes'));
    }

    public function create()
    {
        return view('motorbikes.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);
        $validated = $this->sanitizeCurrencyFields($validated);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('motorbike_images', 'public');
        }

        Motorbike::create($validated);

        return redirect()->route('motorbikes.index')->with('success', 'Data motor berhasil ditambahkan.');
    }

    public function edit(Motorbike $motorbike)
    {
        return view('motorbikes.edit', compact('motorbike'));
    }

    public function update(Request $request, Motorbike $motorbike)
    {
        $today = now()->toDateString();

        // Cek apakah motor sedang disewa
        $isRented = $motorbike->rentals()
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->where('is_cancelled', false)
            ->where('is_completed', false)
            ->exists();

        // Validasi input
        $validated = $this->validateData($request, $motorbike);
        $validated = $this->sanitizeCurrencyFields($validated);

        // Proteksi: Jika motor sedang disewa, jangan izinkan perubahan status teknis
        if ($isRented) {
            // Kembalikan nilai status teknis ke yang lama
            $validated['technical_status'] = $motorbike->technical_status;
        }

        // Handle upload gambar baru
        if ($request->hasFile('image')) {
            if ($motorbike->image) {
                Storage::disk('public')->delete($motorbike->image);
            }
            $validated['image'] = $request->file('image')->store('motorbike_images', 'public');
        }

        // Update data motor
        $motorbike->update($validated);

        // Pesan sukses, tapi informasikan juga kalau status teknis tidak diubah jika sedang disewa
        if ($isRented) {
            return redirect()->route('motorbikes.index')
                ->with('success', 'Data motor berhasil diperbarui, namun status teknis tidak dapat diubah karena motor sedang disewa.');
        }

        return redirect()->route('motorbikes.index')->with('success', 'Data motor berhasil diperbarui.');
    }

    public function destroy(Motorbike $motorbike)
    {
        if ($motorbike->image) {
            Storage::disk('public')->delete($motorbike->image);
        }

        $motorbike->delete();

        return redirect()->route('motorbikes.index')->with('success', 'Data Motor berhasil dihapus.');
    }

    public function show(Motorbike $motorbike)
    {
        return view('motorbikes.show', compact('motorbike'));
    }

    public function showAdmin(Motorbike $motorbike)
    {
        $motorbike->load(['rentals.customer']);
        return view('motorbikes.show-admin', compact('motorbike'));
    }

    private function validateData(Request $request, Motorbike $motorbike = null): array
    {
        $motorbikeId = $motorbike?->id ?? null;

        return $request->validate([
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'year' => ['required', 'digits:4', 'integer', 'min:1900', 'max:2100'],
            'color' => ['required', 'string', 'max:50'],
            'license_plate' => ['required', 'string', 'max:20', Rule::unique('motorbikes')->ignore($motorbikeId)],
            'rental_price_day' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'technical_status' => ['required', Rule::in(['active', 'inactive', 'maintenance'])],
        ]);
    }

    private function sanitizeCurrencyFields(array $data): array
    {
        if (isset($data['rental_price_day'])) {
            $data['rental_price_day'] = str_replace('.', '', $data['rental_price_day']);
            $data['rental_price_day'] = floatval($data['rental_price_day']);
        }
        return $data;
    }
}

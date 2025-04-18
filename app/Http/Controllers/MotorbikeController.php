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

        if ($request->filled('brand')) {
            $query->where('brand', 'like', "%{$request->brand}%");
        }

        if ($request->filled('model')) {
            $query->where('model', 'like', "%{$request->model}%");
        }

        // Tetap bisa filter status teknis: available/maintenance (jika diperlukan)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $motorbikes = $query->latest()->paginate(10);

        return view('motorbikes.index', compact('motorbikes'));
    }

    public function publicIndex(Request $request)
    {
        $query = Motorbike::with('rentals');

        if ($request->filled('brand')) {
            $query->where('brand', 'like', "%{$request->brand}%");
        }

        if ($request->filled('model')) {
            $query->where('model', 'like', "%{$request->model}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
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

        // return redirect()->route('motorbikes.index')->with('success', 'Motor berhasil ditambahkan.');
        return redirect()->route('motorbikes.index')->with('success', 'Data motor berhasil ditambahkan.');


    }

    public function edit(Motorbike $motorbike)
    {
        return view('motorbikes.edit', compact('motorbike'));
    }

    public function update(Request $request, Motorbike $motorbike)
    {
        
        $validated = $this->validateData($request, $motorbike);
        $validated = $this->sanitizeCurrencyFields($validated);

        if ($request->hasFile('image')) {
            if ($motorbike->image) {
                Storage::disk('public')->delete($motorbike->image);
            }
            $validated['image'] = $request->file('image')->store('motorbike_images', 'public');
        }

        $motorbike->update($validated);

        return redirect()->route('motorbikes.index')->with('success', 'DataMotor berhasil diperbarui.');
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
            'rental_price_hour' => ['nullable', 'string'],
            'rental_price_day' => ['nullable', 'string'],
            'rental_price_week' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'technical_status' => ['required', Rule::in(['active', 'inactive', 'maintenance'])],
        ]);
    }


    private function sanitizeCurrencyFields(array $data): array
    {
        foreach (['rental_price_hour', 'rental_price_day', 'rental_price_week'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = str_replace('.', '', $data[$field]);
                $data[$field] = floatval($data[$field]);
            }
        }
        return $data;
    }
}

<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MotorbikeController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\RentalAdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;

// 🔐 Auth & Landing
Route::get('/', fn() => redirect()->route('login'));
Auth::routes();

Route::get('/cek-waktu', fn() => now()->timezone('Asia/Jakarta')->format('Y-m-d H:i:s'));

// 🔁 Redirect dashboard berdasarkan role
Route::get('/dashboard', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if (in_array($user->role, ['admin', 'manager'])) {
            return redirect('/admin/dashboard');
        } elseif ($user->role === 'customer') {
            return redirect('/customer/dashboard');
        }
        abort(403, 'Unauthorized');
    }
    return redirect('/login');
});

// ✅ Admin & Manager Group
Route::middleware(['auth'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Motorbikes
    Route::middleware('permission:motorbikes,read')->get('/motorbikes', [MotorbikeController::class, 'index'])->name('motorbikes.index');
    Route::middleware('permission:motorbikes,create')->group(function () {
        Route::get('/motorbikes/create', [MotorbikeController::class, 'create'])->name('motorbikes.create');
        Route::post('/motorbikes', [MotorbikeController::class, 'store'])->name('motorbikes.store');
    });
    Route::middleware('permission:motorbikes,edit')->group(function () {
        Route::get('/motorbikes/{motorbike}/edit', [MotorbikeController::class, 'edit'])->name('motorbikes.edit');
        Route::put('/motorbikes/{motorbike}', [MotorbikeController::class, 'update'])->name('motorbikes.update');
    });
    Route::middleware('permission:motorbikes,delete')->delete('/motorbikes/{motorbike}', [MotorbikeController::class, 'destroy'])->name('motorbikes.destroy');
    Route::get('/motorbikes/{motorbike}/show', [MotorbikeController::class, 'showAdmin'])->name('motorbikes.admin.show');

    // Rentals - Admin
    Route::middleware('permission:rentals,read')->group(function () {
        Route::get('/rentals', [RentalAdminController::class, 'index'])->name('admin.rentals.index');
        Route::get('/rentals/create', [RentalAdminController::class, 'create'])->name('admin.rentals.create'); // <- pindah ke atas
        Route::get('/rentals/{rental}', [RentalAdminController::class, 'show'])->name('admin.rentals.show');
        Route::get('/rentals/{rental}/invoice', [RentalAdminController::class, 'invoice'])->name('admin.rentals.invoice');
    });
    Route::middleware('permission:rentals,create')->post('/rentals', [RentalAdminController::class, 'store'])->name('admin.rentals.store');

    Route::middleware('permission:rentals,create')->group(function () {
        Route::get('/rentals/create', [RentalAdminController::class, 'create'])->name('admin.rentals.create');
        Route::post('/rentals', [RentalAdminController::class, 'store'])->name('admin.rentals.store');
    });
    Route::middleware('permission:rentals,edit')->group(function () {
        Route::get('/rentals/{rental}/edit', [RentalAdminController::class, 'edit'])->name('admin.rentals.edit');
        Route::put('/rentals/{rental}', [RentalAdminController::class, 'update'])->name('admin.rentals.update');
        Route::post('/rentals/{rental}/complete', [RentalAdminController::class, 'complete'])->name('admin.rentals.complete');
        Route::post('/rentals/{rental}/cancel', [RentalAdminController::class, 'cancel'])->name('admin.rentals.cancel');
    });
    Route::middleware('permission:rentals,delete')->delete('/rentals/{rental}', [RentalAdminController::class, 'destroy'])->name('admin.rentals.destroy');

    // Customers
    Route::middleware('permission:customers,read')->get('/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::middleware('permission:customers,create')->group(function () {
        Route::get('/customers/create', [CustomerController::class, 'create'])->name('admin.customers.create');
        Route::post('/customers', [CustomerController::class, 'store'])->name('admin.customers.store');
    });
    Route::middleware('permission:customers,edit')->group(function () {
        Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('admin.customers.edit');
        Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('admin.customers.update');
    });
    Route::middleware('permission:customers,delete')->delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('admin.customers.destroy');
    Route::middleware('permission:customers,read')->get('/customers/{customer}', [CustomerController::class, 'show'])->name('admin.customers.show');

    // Users
    Route::middleware('permission:users,read')->get('/users', [UserController::class, 'index'])->name('users.index');
    Route::middleware('permission:users,create')->group(function () {
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
    });
    Route::middleware('permission:users,edit')->group(function () {
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    });
    Route::middleware('permission:users,delete')->delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Reports
    Route::middleware('permission:reports,read')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
        Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
        Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
    });
});

// ✅ Customer Dashboard & Rentals
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/customer/dashboard', fn() => view('customer.dashboard'));
    Route::resource('rentals', RentalController::class)->only(['create', 'store', 'index', 'show']);
});

// ✅ Profil (semua role)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// ✅ Public motor page
Route::get('/motor', [MotorbikeController::class, 'publicIndex'])->name('motorbikes.public');
Route::get('/motorbikes/{motorbike}', [MotorbikeController::class, 'show'])->name('motorbikes.show');

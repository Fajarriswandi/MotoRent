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

Route::get('/', fn() => view('welcome'));

Auth::routes();

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
    Route::middleware('permission:motorbikes,create')->get('/motorbikes/create', [MotorbikeController::class, 'create'])->name('motorbikes.create');
    Route::middleware('permission:motorbikes,create')->post('/motorbikes', [MotorbikeController::class, 'store'])->name('motorbikes.store');
    Route::middleware('permission:motorbikes,edit')->get('/motorbikes/{motorbike}/edit', [MotorbikeController::class, 'edit'])->name('motorbikes.edit');
    Route::middleware('permission:motorbikes,edit')->put('/motorbikes/{motorbike}', [MotorbikeController::class, 'update'])->name('motorbikes.update');
    Route::middleware('permission:motorbikes,delete')->delete('/motorbikes/{motorbike}', [MotorbikeController::class, 'destroy'])->name('motorbikes.destroy');
    Route::get('motorbikes/{motorbike}/show', [MotorbikeController::class, 'showAdmin'])->name('motorbikes.admin.show');

    // Rentals
    Route::middleware('permission:rentals,read')->get('/rentals', [RentalAdminController::class, 'index'])->name('admin.rentals.index');
    Route::middleware('permission:rentals,create')->get('/rentals/create', [RentalAdminController::class, 'create'])->name('admin.rentals.create');
    Route::middleware('permission:rentals,create')->post('/rentals', [RentalAdminController::class, 'store'])->name('admin.rentals.store');
    Route::middleware('permission:rentals,edit')->post('/rentals/{rental}/complete', [RentalAdminController::class, 'complete'])->name('admin.rentals.complete');
    Route::middleware('permission:rentals,edit')->post('/rentals/{rental}/cancel', [RentalAdminController::class, 'cancel'])->name('admin.rentals.cancel');
    Route::middleware('permission:rentals,delete')->delete('/rentals/{rental}', [RentalAdminController::class, 'destroy'])->name('admin.rentals.destroy');
    Route::middleware('permission:rentals,read')->get('/rentals/{rental}/invoice', [RentalAdminController::class, 'invoice'])->name('admin.rentals.invoice');
    Route::post('/rentals/{rental}/status', [RentalAdminController::class, 'updateStatus'])->name('admin.rentals.updateStatus');
    Route::post('/rentals/{rental}/approve', [RentalController::class, 'approve'])->name('admin.rentals.approve');

    // Customers
    Route::middleware('permission:customers,read')->get('/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::middleware('permission:customers,create')->get('/customers/create', [CustomerController::class, 'create'])->name('admin.customers.create');
    Route::middleware('permission:customers,create')->post('/customers', [CustomerController::class, 'store'])->name('admin.customers.store');
    Route::middleware('permission:customers,edit')->get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('admin.customers.edit');
    Route::middleware('permission:customers,edit')->put('/customers/{customer}', [CustomerController::class, 'update'])->name('admin.customers.update');
    Route::middleware('permission:customers,delete')->delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('admin.customers.destroy');
    Route::middleware('permission:customers,read')->get('/customers/{customer}', [CustomerController::class, 'show'])->name('admin.customers.show');

    // Users
    Route::middleware('permission:users,read')->get('/users', [UserController::class, 'index'])->name('users.index');
    Route::middleware('permission:users,create')->get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::middleware('permission:users,create')->post('/users', [UserController::class, 'store'])->name('users.store');
    Route::middleware('permission:users,edit')->get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::middleware('permission:users,edit')->put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::middleware('permission:users,delete')->delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Reports BELUM DIBUAT FITURNYA
    Route::middleware('permission:reports,read')->get('/reports', [ReportController::class, 'index'])->name('reports.index');


    // Reports routes for admins and managers
    Route::middleware(['permission:reports,read'])->group(function () {
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


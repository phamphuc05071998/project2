<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\StockEntryController;

// Home route
// Route::get('/', [HomeController::class, 'index'])->name('home');

// User authentication routes
Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserController::class, 'login']);
Route::get('/register', [UserController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [UserController::class, 'register']);
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// User management routes (only accessible by admin)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

// Stock entry routes (accessible by employees, managers, and admins)
Route::middleware(['auth', 'role:employee|manager|admin'])->group(function () {
    Route::get('/stock-entries', [StockEntryController::class, 'index'])->name('stock-entries.index');
    Route::get('/stock-entries/create', [StockEntryController::class, 'create'])->name('stock-entries.create');
    Route::post('/stock-entries', [StockEntryController::class, 'store'])->name('stock-entries.store');
    Route::get('/stock-entries/{stockEntry}/edit', [StockEntryController::class, 'edit'])->name('stock-entries.edit');
    Route::put('/stock-entries/{stockEntry}', [StockEntryController::class, 'update'])->name('stock-entries.update');
    Route::delete('/stock-entries/{stockEntry}', [StockEntryController::class, 'destroy'])->name('stock-entries.destroy');
});

// Approval routes (accessible only by managers and admins)
Route::middleware(['auth', 'role:manager|admin'])->group(function () {
    Route::get('stock-entries/approve', [StockEntryController::class, 'approveIndex'])->name('stock-entries.approve');
    Route::put('stock-entries/{stockEntry}/approve', [StockEntryController::class, 'approve'])->name('stock-entries.approveactions');
    // Route::put('/stock-entries/{stockEntry}/approve-create', [StockEntryController::class, 'approveCreate'])->name('stock-entries.approve-create');
    // Route::put('/stock-entries/{stockEntry}/approve-update', [StockEntryController::class, 'approveUpdate'])->name('stock-entries.approve-update');
    // Route::put('/stock-entries/{stockEntry}/approve-delete', [StockEntryController::class, 'approveDelete'])->name('stock-entries.approve-delete');

    // Category routes
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Supplier routes
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
});

// Statistics route (accessible only by managers and admins)
Route::middleware(['auth', 'role:manager|admin'])->group(function () {
    Route::get('/stock-entries/statistics', [StockEntryController::class, 'statistics'])->name('stock-entries.statistics');
});

// Item routes (accessible only by managers and admins)
Route::middleware(['auth', 'role:manager|admin'])->group(function () {
    Route::get('/items', [ItemController::class, 'index'])->name('items.index');
    Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
    Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
});

Route::get('/items-by-supplier/{supplierId}', [StockEntryController::class, 'getItemsBySupplier']);

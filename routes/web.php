<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleRequestController;

// User authentication routes
Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserController::class, 'login']);
Route::get('/register', [UserController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [UserController::class, 'register']);
Route::post('/logout', [UserController::class, 'logout'])->name('logout');
Route::middleware(['role:author|editor|admin'])->group(function () {
    Route::get('posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('posts/{post}', [PostController::class, 'update'])->name('posts.update');
});

Route::get('posts/approveRequest',  [PostController::class, 'approveRequest'])->name('posts.approveRequest')->middleware('role:admin|editor');

Route::middleware(['role:author'])->group(function () {
    Route::get('posts/author', [PostController::class, 'authorPosts'])->name('posts.author');
});


// Public routes
Route::get('/', [PostController::class, 'index'])->name('home');

Route::get('posts/{post}', [PostController::class, 'show'])->name('posts.show');

Route::middleware(['role:admin|editor'])->group(function () {
    Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
    Route::get('/category/create', [CategoryController::class, 'create'])->name('category.create');
    Route::post('/category', [CategoryController::class, 'store'])->name('category.store');
    Route::get('/category/{category}/edit', [CategoryController::class, 'edit'])->name('category.edit');
    Route::put('/category/{category}', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('/category/{category}', [CategoryController::class, 'destroy'])->name('category.destroy');
});
// Routes for managing posts


Route::middleware(['role:editor|admin'])->group(function () {
    Route::post('posts/{post}/approve', [PostController::class, 'approve'])->name('posts.approve');
    Route::post('posts/{post}/change-status', [PostController::class, 'changeStatus'])->name('posts.changeStatus');
    Route::post('posts/{post}/change-category', [PostController::class, 'changeCategory'])->name('posts.changeCategory');
    Route::post('temp-posts/{tempPost}/approve', [PostController::class, 'approveEdit'])->name('tempPosts.approveEdit');

    Route::post('posts/{tempPost}/approveTempPost', [PostController::class, 'approveTempPost'])->name('posts.approveTempPost');
    Route::post('posts/{tempPost}/rejectTempPost', [PostController::class, 'rejectTempPost'])->name('posts.rejectTempPost');
    Route::post('posts/{post}/assignCategory', [PostController::class, 'assignCategory'])->name('posts.assignCategory');
    Route::post('posts/{tempPost}/confirmDelete', [PostController::class, 'confirmDelete'])->name('posts.confirmDelete');
    Route::post('posts/{tempPost}/rejectDelete', [PostController::class, 'rejectDelete'])->name('posts.rejectDelete');
});

// Routes for role requests
Route::middleware(['auth'])->group(function () {
    Route::get('role-requests/create', [RoleRequestController::class, 'create'])->name('roleRequests.create');
    Route::post('role-requests', [RoleRequestController::class, 'store'])->name('roleRequests.store');
    Route::delete(('posts/{post}/delete'), [PostController::class, 'deleteRequest'])->name('posts.deleteRequest');
});

Route::middleware(['role:admin|editor'])->group(function () {
    Route::get('role-requests', [RoleRequestController::class, 'index'])->name('roleRequests.index');
    Route::post('role-requests/{roleRequest}/approve', [RoleRequestController::class, 'approve'])->name('roleRequests.approve');
    Route::post('role-requests/{roleRequest}/reject', [RoleRequestController::class, 'reject'])->name('roleRequests.reject');
});

Route::resource('posts', PostController::class)->except(['create', 'store', 'edit', 'update'])->middleware('role:author|editor|admin');

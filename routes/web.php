<?php

use App\Http\Controllers\ChartController;
use App\Http\Controllers\DailyStatController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

Route::get('/', function () {
    // If user is logged in, redirect them to the dashboard
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    // Show the Login page with its required properties
    return Inertia::render('Auth/Login', [
        'canResetPassword' => Route::has('password.request'),
        'status' => session('status'),
    ]);
})->name('home');

Route::get('/register', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return Inertia::render('Auth/Register');
})->name('register');

// Updated to route through DailyStatController to load database entries
Route::get('/dashboard', [DailyStatController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Endpoints for processing tracking entry submissions and updates
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/daily-stats', [DailyStatController::class, 'store'])->name('daily-stats.store');
    Route::put('/daily-stats/{daily_stat}', [DailyStatController::class, 'update'])->name('daily-stats.update');
    Route::delete('/daily-stats/{daily_stat}', [DailyStatController::class, 'destroy'])->name('daily-stats.destroy');
});

Route::get('/charts', ChartController::class)
    ->middleware(['auth', 'verified'])
    ->name('charts');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('auth/Login');
})->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/select-warehouse', function (Request $request) {
        $data = $request->validate([
            'id' => 'required|integer',
            'name' => 'required|string|max:255',
        ]);

        session()->forget('selectedWarehouse');
        session(['selectedWarehouse' => $data]);

        return response()->json($data);
    })->name('select-warehouse');
});


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/general.php';

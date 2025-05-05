<?php

use App\Http\Controllers\ClientsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProductsEntriesController;
use App\Http\Controllers\ProductsPricesController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\UserWarehouseController;
use App\Http\Controllers\WarehousesController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('warehouses/show', [WarehousesController::class, 'showWarehouses'])->name('warehouses.show');
    Route::get('warehouses/chart', [WarehousesController::class, 'showWarehousesChart'])->name('warehouses.chart');
    // Warehouses
    Route::get('warehouses', [WarehousesController::class, 'index'])->name('warehouses.index');
    Route::post('warehouses', [WarehousesController::class, 'store'])->name('warehouses.store');
    Route::put('warehouses/{warehouse}', [WarehousesController::class, 'update'])->name('warehouses.update');
    Route::delete('warehouses/{warehouse}', [WarehousesController::class, 'destroy'])->name('warehouses.destroy');

    Route::post('warehouses/{warehouse}/users', [UserWarehouseController::class, 'store'])->name('warehouses.users.store');
    Route::delete('warehouses/{warehouse}/users/{user}', [UserWarehouseController::class, 'destroy'])->name('warehouses.users.destroy');

    // Products
    Route::get('warehouses/products', [ProductsController::class, 'index'])->name('warehouses.products.index');
    Route::post('warehouses/products', [ProductsController::class, 'store'])->name('warehouses.products.store');
    Route::put('warehouses/products/{product}', [ProductsController::class, 'update'])->name('warehouses.products.update');
    Route::get('warehouses/products/{product}', [ProductsController::class, 'show'])->name('warehouses.products.show');
    Route::delete('warehouses/products/{product}', [ProductsController::class, 'destroy'])->name('warehouses.products.destroy');
    Route::get('products', [ProductsController::class, 'showAllProducts'])->name('products.showAllProducts');
    Route::get('products/prices', [ProductsController::class, 'showAllProductsPrices'])->name('products.showAllProductsPrices');

    // Clients
    Route::get('customers', [ClientsController::class, 'index'])->name('customers.index');
    Route::post('customers', action: [ClientsController::class, 'store'])->name('customers.store');
    Route::put('customers/{id}', [ClientsController::class, 'update'])->name('customers.update');
    Route::delete('customers/{id}', [ClientsController::class, 'destroy'])->name('customers.destroy');
    Route::get('customers/all', [ClientsController::class, 'showAllClients'])->name('customers.showAllClients');

    // Entries
    Route::get('entries', [ProductsEntriesController::class, 'index'])->name('entries.index');
    Route::post('entries', [ProductsEntriesController::class, 'store'])->name('entries.store');
    Route::put('entries/{id}', [ProductsEntriesController::class, 'update'])->name('entries.update');
    Route::delete('entries/{id}', [ProductsEntriesController::class, 'destroy'])->name('entries.destroy');

    // Sales
    Route::get('sales', [SalesController::class, 'index'])->name('sales.index');
    Route::post('sales', [SalesController::class, 'store'])->name('sales.store');
    Route::put('sales/{id}', [SalesController::class, 'update'])->name('sales.update');
    Route::delete('sales/{id}', [SalesController::class, 'destroy'])->name('sales.destroy');
});
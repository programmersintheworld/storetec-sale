<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Models\ProductsEntries;
use App\Models\Products;
use App\Models\Clients;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Obtener almacén seleccionado
        $selectedWarehouse = session('selectedWarehouse');
        $warehouseId = $selectedWarehouse['id'] ?? null;

        // Total de ventas = suma de (unit_price * quantity)
        $totalSales = Sales::where('warehouse_id', $warehouseId)
            ->selectRaw('SUM(unit_price * quantity) as total')
            ->value('total');

        // Total de ingresos = suma de total_cost
        $totalEntries = ProductsEntries::where('warehouse_id', $warehouseId)
            ->sum('total_cost');

        // Total de productos
        $totalProducts = Products::where('warehouse_id', $warehouseId)->count();

        // Total de clientes
        $totalClients = Clients::where('warehouse_id', $warehouseId)->count();

        // Ganancia y pérdida
        $profit = $totalSales - $totalEntries;
        $totalProfit = $profit > 0 ? $profit : 0;
        $totalLoss = $profit < 0 ? abs($profit) : 0;

        // Retornar a Inertia
        return Inertia::render('Dashboard', [
            'totalSales' => round($totalSales, 2) ?? 0,
            'totalEntries' => round($totalEntries, 2) ?? 0,
            'totalProducts' => $totalProducts ?? 0,
            'totalClients' => $totalClients ?? 0,
            'totalProfit' => round($totalProfit, 2) ?? 0,
            'totalLoss' => round($totalLoss, 2) ?? 0,
        ]);
    }
}

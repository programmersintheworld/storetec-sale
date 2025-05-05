<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\ProductsPrices;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $selectedWarehouse = session('selectedWarehouse');
        $warehouseId = $selectedWarehouse['id'] ?? null;

        $products = Products::with(['latestPrice'])
            ->withSum('entries', 'quantity') // suma ingresos
            ->withSum('sales', 'quantity')   // suma ventas
            ->where('warehouse_id', $warehouseId)
            ->orderBy('name')
            ->get()
            ->map(function ($product) {
                $stock = ($product->entries_sum_quantity ?? 0) - ($product->sales_sum_quantity ?? 0);
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'barcode' => $product->barcode,
                    'purchase_price' => $product->latestPrice->purchase_price ?? null,
                    'sale_price' => $product->latestPrice->sale_price ?? null,
                    'effective_date' => $product->latestPrice->effective_date ?? null,
                    'stock' => $stock,
                ];
            });

        return inertia('Products/Index', [
            'products' => $products,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'required|string|max:255',
        ]);
        $selectedWarehouse = session('selectedWarehouse');
        $warehouseId = $selectedWarehouse['id'] ?? null;
        if (!$warehouseId) {
            return response()->json(['error' => 'Warehouse not selected'], 400);
        }
        $product = new Products();
        $product->name = $request->name;
        $product->barcode = $request->barcode;
        $product->warehouse_id = $warehouseId;
        $product->save();

        return response()->json([
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // History Prices
        $product = ProductsPrices::where('product_id', $id)
            ->orderBy('effective_date', 'desc')
            ->get()
            ->map(function ($price) {
                return [
                    'id' => $price->id,
                    'purchase_price' => $price->purchase_price,
                    'sale_price' => $price->sale_price,
                    'effective_date' => $price->effective_date,
                ];
            });

        return response()->json([
            'message' => 'Product prices retrieved successfully',
            'data' => $product,
        ], 200);
    }

    public function showAllProducts()
    {
        $selectedWarehouse = session('selectedWarehouse');
        $warehouseId = $selectedWarehouse['id'] ?? null;
        if (!$warehouseId) {
            return response()->json(['error' => 'Warehouse not selected'], 400);
        }

        $products = Products::where('warehouse_id', $warehouseId)
            ->orderBy('name')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'barcode' => $product->barcode,
                ];
            });
        return response()->json([
            'message' => 'Products retrieved successfully',
            'data' => $products,
        ], 200);
    }
        
    public function showAllProductsPrices()
    {
        $selectedWarehouse = session('selectedWarehouse');
        $warehouseId = $selectedWarehouse['id'] ?? null;
        if (!$warehouseId) {
            return response()->json(['error' => 'Warehouse not selected'], 400);
        }

        $products = Products::with(['latestPrice'])
            ->withSum('entries', 'quantity') // suma ingresos
            ->withSum('sales', 'quantity')   // suma ventas
            ->where('warehouse_id', $warehouseId)
            ->orderBy('name')
            ->get()
            ->map(function ($product) {
                $stock = ($product->entries_sum_quantity ?? 0) - ($product->sales_sum_quantity ?? 0);
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sale_price' => $product->latestPrice->sale_price ?? null,
                    'stock' => $stock,
                ];
            });
        return response()->json([
            'message' => 'Products prices retrieved successfully',
            'data' => $products,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'required|string|max:255',
        ]);

        $product = Products::findOrFail($product);
        $product->name = $request->name;
        $product->barcode = $request->barcode;
        $product->save();

        return response()->json(['message' => 'Product updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $product)
    {
        $product = Products::findOrFail($product);
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
}

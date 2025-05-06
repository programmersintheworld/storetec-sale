<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\ProductsEntries;
use App\Models\ProductsPrices;
use App\Models\Warehouses;
use Illuminate\Http\Request;

class ProductsEntriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $selectedWarehouse = session('selectedWarehouse');
        $warehouseId = $selectedWarehouse['id'] ?? null;
        if (!$warehouseId) {
            return response()->json(['error' => 'Warehouse not selected'], 400);
        }

        $entries = ProductsEntries::with(['product', 'user'])
            ->whereHas('product', function ($query) use ($warehouseId) {
                $query->where('warehouse_id', $warehouseId);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'product' => [
                        'id' => $entry->product->id,
                        'name' => $entry->product->name,
                    ],
                    'user' => $entry->user->name ?? 'N/A',
                    'quantity' => $entry->quantity,
                    'total_cost' => $entry->total_cost,
                    'unit_cost' => $entry->unit_cost,
                    'entry_date' => $entry->entry_date,
                ];
            });

        return inertia('Entries/Index', [
            'entries' => $entries,
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
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'total_cost' => 'required|numeric|min:0',
            'unit_cost' => 'required|numeric|min:0',
        ]);

        $selectedWarehouse = session('selectedWarehouse');
        $warehouseId = $selectedWarehouse['id'] ?? null;
        if (!$warehouseId) {
            return response()->json(['error' => 'Warehouse not selected'], 400);
        }

        $warehouse = Warehouses::find($warehouseId)->first();
        if (!$warehouse) {
            return response()->json(['error' => 'Warehouse not found'], 404);
        }

        $entry = new ProductsEntries();
        $entry->product_id = $request->product_id;
        $entry->user_id = auth()->user()->id;
        $entry->quantity = $request->quantity;
        $entry->total_cost = $request->total_cost;
        $entry->unit_cost = $request->unit_cost;
        $entry->warehouse_id = $warehouseId;
        $entry->save();

        $prices = new ProductsPrices();
        $prices->product_id = $request->product_id;
        $prices->purchase_price = $request->unit_cost;
        $prices->sale_price = $request->unit_cost * $warehouse->percentage_earnings;
        $prices->warehouse_id = $warehouseId;
        $prices->save();

        $product = Products::find($request->product_id);

        return response()->json([
            'message' => 'Entry created successfully',
            'data' => [
                'id' => $entry->id,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                ],
                'user' => auth()->user()->name,
                'quantity' => $entry->quantity,
                'total_cost' => $entry->total_cost,
                'unit_cost' => $entry->unit_cost,
                'entry_date' => now(),
            ]
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'total_cost' => 'required|numeric|min:0',
            'unit_cost' => 'required|numeric|min:0',
        ]);

        $entry = ProductsEntries::findOrFail($id);
        $entry->product_id = $request->product_id;
        $entry->quantity = $request->quantity;
        $entry->total_cost = $request->total_cost;
        $entry->unit_cost = $request->unit_cost;
        $entry->save();

        return response()->json(['message' => 'Entry updated successfully', 'data' => $entry], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $entry = ProductsEntries::findOrFail($id);
        $entry->delete();

        return response()->json(['message' => 'Entry deleted successfully'], 200);
    }
}

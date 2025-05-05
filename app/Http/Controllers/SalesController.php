<?php

namespace App\Http\Controllers;

use App\Models\Clients;
use App\Models\Products;
use App\Models\Sales;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $selectedWarehouse = session('selectedWarehouse');
        $warehouseId = $selectedWarehouse['id'] ?? null;

        $sales = Sales::with(['product', 'client', 'user'])
            ->where('warehouse_id', $warehouseId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($sale) {
            return [
                'id' => $sale->id,
                'product' => [
                    'id' => $sale->product->id,
                    'name' => $sale->product->name, 
                ],
                'client' => [
                    'id' => $sale->client->id,
                    'name' => $sale->client->name,
                ],
                'user' => $sale->user->name,
                'quantity' => $sale->quantity,
                'unit_price' => $sale->unit_price,
                'sold_at' => $sale->sold_at,
                'created_at' => $sale->created_at,
            ];
            });

        return inertia('Sales/Index', [
            'sales' => $sales,
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
            'client_id' => 'required|exists:clients,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);
        $selectedWarehouse = session('selectedWarehouse');
        $warehouseId = $selectedWarehouse['id'] ?? null;
        if (!$warehouseId) {
            return response()->json(['error' => 'Warehouse not found'], 404);
        }
        $sale = new Sales();
        $sale->product_id = $request->product_id;
        $sale->client_id = $request->client_id;
        $sale->user_id = auth()->user()->id;
        $sale->warehouse_id = $warehouseId;
        $sale->quantity = $request->quantity;
        $sale->unit_price = $request->price;
        $sale->warehouse_id = $warehouseId;
        $sale->save();

        return response()->json(['message' => 'Sale created successfully', 'data' => [
            'id' => $sale->id,
            'product' => [
                'id' => $sale->product->id,
                'name' => $sale->product->name, 
            ],
            'client' => [
                'id' => $sale->client->id,
                'name' => $sale->client->name,
            ],
            'user' => auth()->user()->name,
            'quantity' => $sale->quantity,
            'unit_price' => $sale->unit_price,
            'sold_at' => $sale->sold_at,
        ]], 201);
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
            'client_id' => 'required|exists:clients,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $sale = Sales::findOrFail($id);
        $sale->product_id = $request->product_id;
        $sale->client_id = $request->client_id;
        $sale->quantity = $request->quantity;
        $sale->unit_price = $request->price;
        $sale->save();

        return response()->json([
            'message' => 'Sale updated successfully',
            'data' => [
                'id' => $sale->id,
                'product' => [
                    'id' => $sale->product->id,
                    'name' => $sale->product->name,
                ],
                'client' => [
                    'id' => $sale->client->id,
                    'name' => $sale->client->name,
                ],
                'user' => auth()->user()->name,
                'quantity' => $sale->quantity,
                'unit_price' => $sale->unit_price,
                'sold_at' => now(),
            ]
        ], status: 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $sale = Sales::findOrFail($id);
        $sale->delete();

        return response()->json(['message' => 'Sale deleted successfully'], 200);
    }
}

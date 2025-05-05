<?php

namespace App\Http\Controllers;

use App\Models\Clients;
use Illuminate\Http\Request;

class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $selectedWarehouse = session('selectedWarehouse');
        $warehouseId = $selectedWarehouse['id'] ?? null;

        $clients = Clients::where('warehouse_id', $warehouseId)
            ->orderBy('name')
            ->get();
        return inertia('Clients/Index', [
            'clients' => $clients,
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
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:255',
        ]);
        $selectedWarehouse = session('selectedWarehouse');
        $warehouseId = $selectedWarehouse['id'] ?? null;
        if (!$warehouseId) {
            return response()->json(['error' => 'Warehouse not selected'], 400);
        }
        $client = new Clients();
        $client->name = $request->name;
        $client->email = $request->email;
        $client->phone = $request->phone;
        $client->warehouse_id = $warehouseId;
        $client->save();
        return response()->json([
            'message' => 'Client created successfully',
            'data' => $client,
        ], 201);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function showAllClients()
    {
        $selectedWarehouse = session('selectedWarehouse');
        $warehouseId = $selectedWarehouse['id'] ?? null;
        if (!$warehouseId) {
            return response()->json(['error' => 'Warehouse not selected'], 400);
        }
        $clients = Clients::where('warehouse_id', $warehouseId)
            ->orderBy('name')
            ->get(['id', 'name']);
        return response()->json([
            'data' => $clients,
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
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:255',
        ]);
        $client = Clients::find($id);
        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }
        $client->name = $request->name;
        $client->email = $request->email;
        $client->phone = $request->phone;
        $client->save();
        return response()->json([
            'message' => 'Client updated successfully',
            'data' => $client,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $client = Clients::find($id);
        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }
        $client->delete();
        return response()->json(['message' => 'Client deleted successfully'], 200);
    }
}

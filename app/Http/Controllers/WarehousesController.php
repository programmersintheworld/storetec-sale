<?php

namespace App\Http\Controllers;

use App\Models\Warehouses;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class WarehousesController extends Controller
{
    public function showWarehouses()
    {
        $user = Auth::user();
        $warehouses = Warehouses::select('id', 'name')
            ->where('user_id', $user->id)
            ->orWhereHas('users', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get();

        return response()->json($warehouses);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $user = Auth::user();
        $warehouses = Warehouses::with('owner:id,name,email', 'users:id,name,email')
            ->where('user_id', $user->id)
            ->orWhereHas('users', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get();

        return Inertia::render('Warehouses/Index', [
            'warehouses' => $warehouses,
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
            'percentage_earnings' => 'nullable|numeric|min:0|max:100',
        ]);
        $user = Auth::user();
        $warehouse = new Warehouses();
        $warehouse->name = $request->name;
        $warehouse->user_id = $user->id;
        $warehouse->percentage_earnings = $request->percentage_earnings ?? 1.2;
        $warehouse->save();

        $warehouseInformation = [
            'id' => $warehouse->id,
            'name' => $warehouse->name,
            'owner' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'percentage_earnings' => $warehouse->percentage_earnings,
            'created_at' => $warehouse->created_at,
        ];

        session([
            'selectedWarehouse' => [
                'id' => $warehouse->id,
                'name' => $warehouse->name,
            ],
        ]);

        return response()->json([
            'message' => 'Warehouse created successfully.',
            'warehouse' => $warehouseInformation,
        ]);
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
    public function update(Request $request, string $warehouse)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'percentage_earnings' => 'nullable|numeric|min:0|max:100',
        ]);
        $warehouse = Warehouses::findOrFail($warehouse);
        $warehouse->name = $request->name;
        $warehouse->percentage_earnings = $request->percentage_earnings ?? 1.2;
        $warehouse->save();

        if (session('selectedWarehouse.id') == $warehouse->id) {
            session([
                'selectedWarehouse' => [
                    'id' => $warehouse->id,
                    'name' => $warehouse->name,
                ],
            ]);
        }
        return redirect()->route('warehouses.index')->with('success', 'Warehouse updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $warehouse)
    {
        $warehouse = Warehouses::findOrFail($warehouse);
        $warehouse->delete();
        if (session('selectedWarehouse.id') == $warehouse->id) {
            session()->forget('selectedWarehouse');
        }
        return redirect()->route('warehouses.index')->with('success', 'Warehouse deleted successfully.');
    }
}

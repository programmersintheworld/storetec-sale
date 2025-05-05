<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Warehouses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserWarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request, string $warehouse)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $user = Auth::user();
        $userToAdd = User::where('email', $request->email)->first();
        if (!$userToAdd) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $warehouse = Warehouses::find($warehouse);
        if (!$warehouse) {
            return response()->json(['message' => 'Warehouse not found'], 404);
        }
        if ($warehouse->user_id !== $user->id) {
            return response()->json(['message' => 'You do not have permission to add users to this warehouse'], 403);
        }
        if ($warehouse->users()->where('user_id', $userToAdd->id)->exists()) {
            return response()->json(['message' => 'User already has access to this warehouse'], 409);
        }
        $warehouse->users()->attach($userToAdd->id);
        return response()->json([
            'message' => 'User added to warehouse successfully.',
            'user' => [
                'id' => $userToAdd->id,
                'name' => $userToAdd->name,
                'email' => $userToAdd->email,
            ],
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $warehouse, string $user)
    {
        $currentUser = Auth::user();
        $warehouse = Warehouses::find($warehouse);
        if (!$warehouse) {
            return response()->json(['message' => 'Warehouse not found'], 404);
        }
        if ($warehouse->user_id !== $currentUser->id) {
            return response()->json(['message' => 'You do not have permission to remove users from this warehouse'], 403);
        }
        
        $userToRemove = User::find($user);
        if (!$userToRemove) {
            return response()->json(['message' => 'User not found'], 404);
        }
        if (!$warehouse->users()->where('user_id', $userToRemove->id)->exists()) {
            return response()->json(['message' => 'User does not have access to this warehouse'], 409);
        }
        $warehouse->users()->detach($userToRemove->id);
        return response()->json(['message' => 'User removed from warehouse successfully.']);
    }
}

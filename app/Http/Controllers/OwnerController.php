<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use Illuminate\Http\Request;

class OwnerController extends Controller
{
    public function index()
    {
        $owners = Owner::all();
        return response()->json([
            'success' => true,
            'data' => $owners
        ], 200);
    }

    public function create()
    {
        return response()->json([
            'success' => true,
            'message' => 'Provide the required fields to create an owner.'
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15',
            'email' => 'required|email|unique:owners',
            'address' => 'required|string|max:255',
            'aadhaar_card' => 'nullable|string|max:12',
        ]);

        $owner = Owner::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Owner created successfully.',
            'data' => $owner
        ], 201);
    }

    public function show(Owner $owner)
    {
        return response()->json([
            'success' => true,
            'data' => $owner
        ], 200);
    }

    public function edit(Owner $owner)
    {
        return response()->json([
            'success' => true,
            'message' => 'Fetch the owner details for editing.',
            'data' => $owner
        ], 200);
    }

    public function update(Request $request, Owner $owner)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:15',
            'email' => 'nullable|email|unique:owners,email,' . $owner->id,
            'address' => 'nullable|string|max:255',
            'aadhaar_card' => 'nullable|string|max:12',
            'website' => 'nullable|string',
            'sector' => 'nullable|string'
        ]);

        $owner->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Owner updated successfully.',
            'data' => $owner
        ], 200);
    }

    public function destroy(Owner $owner)
    {
        $owner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Owner deleted successfully.'
        ], 200);
    }
}


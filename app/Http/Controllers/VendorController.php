<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Owner;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    // Fetch all vendors with their owners
    public function index()
    {
        $vendors = Vendor::with('owner')->get();
        return response()->json([
            'success' => true,
            'message' => 'Vendors retrieved successfully.',
            'data' => $vendors,
        ], 200);
    }

    // Fetch specific vendor details
    public function show(Vendor $vendor)
    {
        return response()->json([
            'success' => true,
            'message' => 'Vendor details retrieved successfully.',
            'data' => $vendor->load('owner'), // Include related owner details
        ], 200);
    }

    // Create a new vendor
    public function store(Request $request)
    {
        $validated = $request->validate([
            'owner_id' => [
                'required',
                'exists:owners,id',
                'integer',
            ],
            'name_of_vendor' => 'required|string|max:255',
            'category_of_products' => 'required|string|max:255',
            'email' => 'required|email|unique:vendors',
            'registered_office_address' => 'required|string|max:255',
            'head_office_address' => 'required|string|max:255',
        ]);

        try {
            $vendor = Vendor::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Vendor created successfully.',
                'data' => $vendor,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the vendor.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Update an existing vendor
    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'owner_id' => [
                'nullable',
                'exists:owners,id',
                'integer',
            ],
            'name_of_vendor' => 'nullable|string|max:255',
            'category_of_products' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:vendors,email,' . $vendor->id,
            'registered_office_address' => 'nullable|string|max:255',
            'head_office_address' => 'nullable|string|max:255',
        ]);

        try {
            $vendor->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Vendor updated successfully.',
                'data' => $vendor,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the vendor.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Delete a vendor
    public function destroy(Vendor $vendor)
    {
        $vendor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vendor deleted successfully.',
        ], 200);
    }

    // Change the status of vendor submissions
    public function changeStatus(Request $request, Vendor $vendor)
    {
        // Validate the status field
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        // Update the status
        $vendor->status = $request->status;
        $vendor->save();

        return response()->json([
            'success' => true,
            'message' => 'Vendor status updated successfully.',
            'data' => $vendor,
        ]);
    }

    //get vendors by status
    public function getVendorsByStatus(Request $request)
    {
        // Validate the status parameter
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        // Retrieve vendors based on the status
        $vendors = Vendor::where('status', $request->status)->get();

        return response()->json([
            'success' => true,
            'message' => 'Vendors retrieved successfully.',
            'data' => $vendors,
        ]);
    }

    //get count of vendor statuses
    public function getStatusCount()
    {
        // Count vendors by status
        $statusCounts = Vendor::select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status => $item->count];
            });

        // Ensure all statuses are included with a count of 0 if missing
        $statuses = ['pending', 'approved', 'rejected', 'disputed'];
        $result = [];
        foreach ($statuses as $status) {
            $result[$status] = $statusCounts[$status] ?? 0;
        }

        return response()->json([
            'success' => true,
            'message' => 'Vendor status counts retrieved successfully.',
            'data' => $result,
        ], 200);
    }


    // search and filter
    public function searchAndFilter(Request $request)
    {
        $query = Vendor::query();

        // Search by vendor name (optional)
        if ($request->has('name_of_vendor')) {
            $query->where('name_of_vendor', $request->name_of_vendor); // Exact match
        }

        // Search by email (optional)
        if ($request->has('email')) {
            $query->where('email', $request->email); // Exact match
        }

        // Search by owner name (optional)
        if ($request->has('owner_name')) {
            $query->whereHas('owner', function ($q) use ($request) {
                $q->where('name', '=', $request->owner_name); // Exact match
            });
        }

        // Search by phone number (optional)
        if ($request->has('phone_number')) {
            $query->whereHas('owner', function ($q) use ($request) {
                $q->where('phone_number', $request->phone_number); // Exact match
            });
        }

        // Filter by status (e.g., pending, approved, rejected)
        if ($request->has('status')) {
            $query->where('status', $request->status); // Exact match
        }

        // Filter by date range (created_at column)
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // If only start_date is provided (optional)
        if ($request->has('start_date') && !$request->has('end_date')) {
            $query->where('created_at', '>=', $request->start_date); // Filter records from start_date onwards
        }

        // If only end_date is provided (optional)
        if ($request->has('end_date') && !$request->has('start_date')) {
            $query->where('created_at', '<=', $request->end_date); // Filter records up to end_date
        }


        // Retrieve filtered data with owner details
        $vendors = $query->with('owner')->get();

        return response()->json([
            'success' => true,
            'message' => 'Filtered vendors retrieved successfully.',
            'data' => $vendors,
        ], 200);
    }



}

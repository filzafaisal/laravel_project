<?php

namespace App\Http\Controllers;

use App\Models\Dispute;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DisputeController extends Controller
{
    //Display a listing of disputes.
    public function index()
    {
        $disputes = Dispute::with('vendor')->get();

        return response()->json([
            'success' => true,
            'message' => 'Disputes retrieved successfully.',
            'data' => $disputes,
        ]);
    }

    //Store a newly created dispute.
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required|exists:vendors,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $dispute = Dispute::create([
            'vendor_id' => $request->vendor_id,
            'subject' => $request->subject,
            'description' => $request->description,
            'status' => 'pending',
            'files' => $request->has('files') ? $this->uploadFiles($request->file('files')) : [],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dispute created successfully.',
            'data' => $dispute,
        ], 201);
    }

    //Display a specific dispute.
    public function show(Dispute $dispute)
    {
        return response()->json([
            'success' => true,
            'message' => 'Dispute retrieved successfully.',
            'data' => $dispute->load('vendor'),
        ]);
    }

    //Update a specific dispute.
    public function update(Request $request, Dispute $dispute)
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'sometimes|exists:vendors,id',
            'subject' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'status' => 'sometimes|in:pending,in-review,resolved',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $dispute->update(array_merge(
            $request->except('files'),
            ['files' => $request->has('files') ? $this->uploadFiles($request->file('files')) : $dispute->files]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Dispute updated successfully.',
            'data' => $dispute,
        ]);
    }

    //Delete a specific dispute
    public function destroy(Dispute $dispute)
    {
        $dispute->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dispute deleted successfully.',
        ]);
    }

    // Handle file uploads.
    private function uploadFiles($files)
    {
        $filePaths = [];
        foreach ($files as $file) {
            $filePaths[] = $file->store('disputes', 'public');
        }
        return $filePaths;
    }


    //get dispute of a specific vendor
    public function getDisputesByVendor($vendorId)
    {
        // Fetch disputes for the specified vendor
        $disputes = Dispute::where('vendor_id', $vendorId)->get();

        if ($disputes->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No disputes found for the specified vendor.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Disputes retrieved successfully.',
            'data' => $disputes,
        ], 200);
    }

    public function changeStatus(Request $request, Dispute $dispute)
    {
        // Custom validation for the 'status' field
        $validated = $request->validate([
            'status' => 'required|in:pending,in-review,resolved',
        ]);

        try {
            if ($dispute->status === $request->status) {
                return response()->json([
                    'success' => false,
                    'message' => 'The status is already set to this value.',
                ], 400);
            }

            // Update the status of the dispute
            $dispute->status = $request->status;
            $dispute->save();

            return response()->json([
                'success' => true,
                'message' => 'Dispute status updated successfully.',
                'data' => $dispute,
            ], 200);
        } catch (\Exception $e) {
            // Return a custom JSON error response if an exception occurs
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the dispute status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function getPendingDisputesForVendor($vendorId)
    {
        // Validate if the vendor exists
        $vendor = Vendor::find($vendorId);

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found.',
            ], 404);
        }

        // Fetch all pending disputes for the given vendor
        $pendingDisputes = Dispute::where('vendor_id', $vendorId)
            ->where('status', 'pending')
            ->get();

        if ($pendingDisputes->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No pending disputes found for the specified vendor.',
            ], 404);
        }

        // Count the pending disputes
        $pendingDisputesCount = $pendingDisputes->count();

        return response()->json([
            'success' => true,
            'message' => 'Pending disputes retrieved successfully.',
            'data' => [
                'pending_disputes_count' => $pendingDisputesCount,
                'pending_disputes' => $pendingDisputes,
            ],
        ], 200);
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Dispute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /**
     * Display a listing of messages.
     */
    public function index()
    {
        $messages = Message::with('dispute')->get();

        return response()->json([
            'success' => true,
            'message' => 'Messages retrieved successfully.',
            'data' => $messages,
        ]);
    }

    /**
     * Store a newly created message.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dispute_id' => 'required|exists:disputes,id',
            'message' => 'required|string',
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

        $message = Message::create([
            'dispute_id' => $request->dispute_id,
            'message' => $request->message,
            'files' => $request->has('files') ? $this->uploadFiles($request->file('files')) : [],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message created successfully.',
            'data' => $message,
        ], 201);
    }

    /**
     * Display a specific message.
     */
    public function show(Message $message)
    {
        $message->load('dispute.vendor');
        return response()->json([
            'success' => true,
            'message' => 'Message retrieved successfully.',
            'data' => $message,
        ]);
    }

    /**
     * Update a specific message.
     */
    public function update(Request $request, Message $message)
    {
        $validator = Validator::make($request->all(), [
            'dispute_id' => 'sometimes|exists:disputes,id',
            'message' => 'sometimes|string',
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

        $message->update(array_merge(
            $request->except('files'),
            ['files' => $request->has('files') ? $this->uploadFiles($request->file('files')) : $message->files]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Message updated successfully.',
            'data' => $message,
        ]);
    }

    /**
     * Delete a specific message.
     */
    public function destroy(Message $message)
    {
        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully.',
        ]);
    }

    /**
     * Handle file uploads.
     */
    private function uploadFiles($files)
    {
        $filePaths = [];
        foreach ($files as $file) {
            $filePaths[] = $file->store('messages', 'public');
        }
        return $filePaths;
    }

    /**
     * Get messages related to a specific dispute.
     */
    public function getMessagesByDispute($disputeId)
    {
        // Fetch messages for the specified dispute, and also load the related dispute and vendor details
        $messages = Message::where('dispute_id', $disputeId)
            ->with('dispute.vendor') // Eager load the 'dispute' and 'vendor' relationships
            ->get();

        if ($messages->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No messages found for the specified dispute.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Messages retrieved successfully.',
            'data' => $messages,
        ], 200);
    }


}

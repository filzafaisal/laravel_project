<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\DisputeController;
use App\Http\Controllers\MessageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Example API route for getting the authenticated user
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Routes for Owner API
Route::prefix('owners')->group(function () {
    Route::get('/', [OwnerController::class, 'index']); // Fetch all owners
    Route::post('/', [OwnerController::class, 'store']); // Create a new owner
    Route::get('/{owner}', [OwnerController::class, 'show']); // Fetch a single owner
    Route::put('/edit/{owner}', [OwnerController::class, 'update']); // Update an owner
    Route::delete('/{owner}', [OwnerController::class, 'destroy']); // Delete an owner
});

// Routes for Vendor API
Route::prefix('vendors')->group(function () {
    Route::get('/', [VendorController::class, 'index']); // Fetch all vendors
    Route::post('/', [VendorController::class, 'store']); // Create a new vendor
    Route::get('/{vendor}', [VendorController::class, 'show']); // Fetch a single vendor
    Route::put('/{vendor}', [VendorController::class, 'update']); // Update a vendor
    Route::delete('/{vendor}', [VendorController::class, 'destroy']); // Delete a vendor
    Route::put('/status/{vendor}', [VendorController::class, 'changeStatus']);
    Route::get('/status/by-status', [VendorController::class, 'getVendorsByStatus']);
    Route::get('/status/status-count', [VendorController::class, 'getStatusCount']);
    Route::get('/search/search-filter', [VendorController::class, 'searchAndFilter']);
});

// Routes for Dispute API
Route::prefix('disputes')->group(function () {
    Route::get('/', [DisputeController::class, 'index']); // Fetch all disputes
    Route::post('/', [DisputeController::class, 'store']); // Create a new dispute
    Route::get('/{dispute}', [DisputeController::class, 'show']); // Fetch a single dispute
    Route::put('/{dispute}', [DisputeController::class, 'update']); // Update a dispute
    Route::delete('/{dispute}', [DisputeController::class, 'destroy']); // Delete a dispute
    Route::get('/vendor/{vendorId}', [DisputeController::class, 'getDisputesByVendor']);
    Route::get('/vendor/{vendorId}/pending', [DisputeController::class, 'getPendingDisputesForVendor']);
    Route::put('/status/{dispute}', [DisputeController::class, 'changeStatus']);
});

// Routes for Message API
Route::prefix('messages')->group(function () {
    Route::post('/', [MessageController::class, 'store']); // Create message
    Route::get('/', [MessageController::class, 'index']); // Get all messages
    Route::get('{message}', [MessageController::class, 'show']); // Get single message by ID
    Route::put('{message}', [MessageController::class, 'update']); // Update message by ID
    Route::delete('{message}', [MessageController::class, 'destroy']); // Delete message by ID
    Route::get('/dispute/{disputeId}', [MessageController::class, 'getMessagesByDispute']); // Get message by dispute id
});


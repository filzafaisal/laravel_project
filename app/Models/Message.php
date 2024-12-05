<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispute_id',
        'message',
        'files', // Make sure files are fillable
    ];

    // Cast files as an array when retrieving from the database
    protected $casts = [
        'files' => 'array',  // Ensure files are stored as a JSON array
    ];

    /**
     * Get the dispute that owns the message.
     */
    public function dispute()
    {
        return $this->belongsTo(Dispute::class);
    }
}

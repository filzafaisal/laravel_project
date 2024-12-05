<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'subject',
        'description',
        'status',
        'files',
    ];

    protected $casts = [
        'files' => 'array', // Cast the files field to an array
    ];

    /**
     * Get the vendor associated with the dispute.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}

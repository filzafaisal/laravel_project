<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'designation',
        'phone_number',
        'email',
        'address',
        'aadhaar_card',
        'website',
        'sector',
    ];

    /**
     * Get the vendors associated with the owner.
     */
    public function vendors()
    {
        return $this->hasMany(Vendor::class);
    }
}

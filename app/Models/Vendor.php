<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name_of_vendor',
        'category_of_products',
        'email',
        'registered_office_address',
        'head_office_address',
        'status',
    ];

    // Default attributes
    protected $attributes = [
        'status' => 'pending',
    ];
    /**
     * Get the owner of the vendor.
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
    public function disputes()
    {
        return $this->hasMany(Dispute::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'business_id',
        'name',
        'description',
        'price',
        'stock',
        'image',
        'is_active',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}

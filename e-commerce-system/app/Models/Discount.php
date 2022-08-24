<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'discount_percentage',
        'product_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

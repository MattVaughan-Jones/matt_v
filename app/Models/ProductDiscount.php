<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ProductDiscount extends Model
{
    use HasFactory;
    protected $table = 'product_discounts';
    protected $fillable = ['product_id', 'type', 'discount'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

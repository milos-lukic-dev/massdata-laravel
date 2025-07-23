<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Price Discount model
 *
 * @class PriceDiscount
 */
class PriceDiscount extends Model
{
    protected $fillable = [
        'price_id',
        'qty',
        'discount',
    ];

    protected $casts = [
        'qty' => 'integer',
        'discount' => 'float',
    ];

    /**
     * Get the price this discount belongs to.
     *
     * @return BelongsTo
     */
    public function price(): BelongsTo
    {
        return $this->belongsTo(Price::class);
    }
}

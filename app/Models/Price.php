<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Price model
 *
 * @class Price
 */
class Price extends Model
{
    protected $fillable = [
        'sku',
        'price'
    ];

    protected $casts = [
        'price' => 'float'
    ];

    /**
     * Discounts relation
     *
     * @return HasMany
     */
    public function discounts(): HasMany
    {
        return $this->hasMany(PriceDiscount::class)->orderBy('qty');
    }
}

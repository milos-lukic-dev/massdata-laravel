<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Product model
 *
 * @class Product
 */
class Product extends Model
{
    protected $fillable = [
        'sku',
        'title',
        'item_description',
        'created_at',
        'updated_at'
    ];
}

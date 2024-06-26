<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_code',
        'proudct_barcode',
        'product_name',
        'product_description',
        'product_image',
        'product_stock',
        'product_price',
        'product_category_id',
        'product_user_id',
        'product_user_id_update',
    ];

}

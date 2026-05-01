<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'path',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

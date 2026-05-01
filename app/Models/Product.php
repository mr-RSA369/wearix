<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
        protected $fillable = [
            'name',
            'description',
            'price',
            'category_id',
        ];

    public function category()
        {
            return $this->belongsTo(Category::class);
        }

    public function colors()
    {
        return $this->hasMany(ProductColor::class);
    }

    public function media()
        {
            return $this->hasMany(ProductMedia::class);
        }

    public function sizes()
    {
        return $this->belongsToMany(Size::class);
    }

    public function details()
    {
        return $this->hasMany(ProductDetail::class);
    }

    public function sizeGuide()
    {
        return $this->hasOne(SizeGuide::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = ['name_en', 'name_ar', 'description_en', 'description_ar', 'images', 'price', 'discounted_price', 'quantity', 'status'];

    public function getLocalizedNameAttribute()
    {
        $lang = request()->header('Accept-Language', 'en');
        return $lang === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function getLocalizedDescriptionAttribute()
    {
        $lang = request()->header('Accept-Language', 'en');
        return $lang === 'ar' ? $this->description_ar : $this->description_en;
    }
    protected $casts = ['images' => 'array'];
    public function categories() {
        return $this->belongsToMany(Category::class);
    }
}

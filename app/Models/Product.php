<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = ['name', 'description', 'images', 'price', 'discounted_price', 'quantity', 'status'];

    protected $casts = ['images' => 'array'];
    public function categories() {
        return $this->belongsToMany(Category::class);
    }
}

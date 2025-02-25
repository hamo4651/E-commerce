<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;
    protected $fillable = ['name', 'description', 'image', 'status'];

    protected static function boot() {
        parent::boot();

        static::deleting(function ($category) {
            $category->products()->each(function ($product) use ($category) {
                $product->categories()->detach($category->id);
                if ($product->categories()->count() === 0) {
                    $product->delete();
                }
            });
        });
    }
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}

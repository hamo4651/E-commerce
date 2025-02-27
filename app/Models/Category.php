<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;
    protected $fillable = ['name_en', 'name_ar', 'description_en', 'description_ar', 'image', 'status'];

    public function getLocalizedNameAttribute()
    {
        $lang = request()->header('Accept-Language', 'en'); // افتراضيًا إنجليزي
        return $lang === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function getLocalizedDescriptionAttribute()
    {
        $lang = request()->header('Accept-Language', 'en');
        return $lang === 'ar' ? $this->description_ar : $this->description_en;
    }
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

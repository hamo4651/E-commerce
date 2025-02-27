<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $lang = $request->header('Accept-Language', 'en'); // افتراضيًا الإنجليزية
    
        return [
            'id' => $this->id,
            'name' => $lang === 'ar' ? $this->name_ar : $this->name_en,
            'description' => $lang === 'ar' ? $this->description_ar : $this->description_en,
            'price' => $this->price,
            'discounted_price' => $this->discounted_price,
            'quantity' => $this->quantity,
            'status' => $this->status,
            'images' => is_string($this->images) ? json_decode($this->images, true) : [],
            'categories' => $this->categories->map(function ($category) use ($lang) {
                return $lang === 'ar' ? $category->name_ar : $category->name_en;
            }),
        ];
    }
    
    
}

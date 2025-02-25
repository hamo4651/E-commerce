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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'discounted_price' => $this->discounted_price,
            'quantity' => $this->quantity,
            'status' => $this->status,
            'images' => is_string($this->images) ? json_decode($this->images, true) : [],
            'categories' => $this->categories->pluck('name'),  
        
        ];
    }
    
}

<?php


namespace App\Repositories;
use Illuminate\Support\Str;
use App\Models\Product;

class productRepository implements ProductRepositoryInterface
{
    private $product;
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function getAll($perpage = 10)
    {
        return $this->product->paginate($perpage);
    }
    public function findById($id)
    {
        return $this->product->findOrFail($id);
    }
    public function create(array $entity)
    {

        $imageNames = [];

        if (isset($entity['images'])) {
            foreach ($entity['images'] as $image) {
                if (!in_array($image->extension(), ['jpeg', 'jpg', 'png', 'webp'])) {
                    continue;
                }
                $imageName = Str::uuid() . '.' . $image->extension();
                $image->move(public_path('images/products'), $imageName);
                $imageNames[] = $imageName ? asset('images/products/' . $imageName) : null;
            }
        }
        // dd($imageNames);

        $entity['images'] = json_encode($imageNames);
        return $this->product->create($entity);
    }
    public function update(array $entity, $id)
    {
        $product = $this->product->findOrFail($id);
        $imageNames = is_string($product->images) ? json_decode($product->images, true) : [];

        if (isset($entity['images'])) {
            if (!empty($imageNames)) {
                foreach ($imageNames as $oldImage) {
                    $imagePath = public_path('images/products/' . basename($oldImage));
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
            }

            $newImageNames = [];
            foreach ($entity['images'] as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->extension();
                $image->move(public_path('images/products'), $imageName);
                $newImageNames[] = asset('images/products/' . $imageName);
            }
            // dd($newImageNames);
            $entity['images'] = json_encode($newImageNames);
        }
        $product->update($entity);
        return $product;
    }

    public function delete($id)
    {

        $product = $this->product->findOrFail($id);
        $images = json_decode($product->images, true);

        if (!empty($images)) {
            foreach ($images as $oldImage) {
                $imagePath = public_path('images/products/' . basename($oldImage));

                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
        }
        return $product->delete();
    }

}
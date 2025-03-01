<?php 

namespace App\Repositories;

use App\Models\Category;

 class CategoryRepository implements CategoryRepositoryInterface{

    private $category;
   public function __construct(Category $category){
     $this->category=$category;
   }


   public function getAll($perpage = 10){
    return $this->category->paginate($perpage);
   }
   public function findById($id){
    
     return $this->category->findOrFail($id);
     
   }
   public function create(array $entity){
    $imageName='';
    if(isset($entity['image'])){
     $image = $entity['image'];
     $imageName = time().'.'.$image->extension();
     $image->move(public_path('images/categories'),$imageName);
    }
   return $this->category->create(
    [
        "name_en"=>$entity['name_en'],
        "name_ar"=>$entity['name_ar'],
        "description_en"=>$entity['description_en'],
        "description_ar"=>$entity['description_ar'],
        "status"=>$entity['status'] ?? 'active',
        'image' => $imageName ? asset('images/categories/' . $imageName) : null

    ]
    );

   }
   public function update(array $entity,$id){
    $cat = $this->category->findOrFail($id);

    $imageName=$cat->image;
    // dd($cat->image);
    if(isset($entity['image'])){
      $imagePath = public_path('images/categories/' . $imageName);
      if (file_exists($imagePath) && is_file($imagePath)) {
          unlink($imagePath);
      }

     $image = $entity['image'];
     $imageName = time().'.'.$image->extension();
     $image->move(public_path('images/categories'),$imageName);
    }
     $cat->update([
        "name_en"=>$entity['name_en'],
        "name_ar"=>$entity['name_ar'],
        "description_en"=>$entity['description_en'],
        "description_ar"=>$entity['description_ar'],
        "status"=>$entity['status'],
        'image' =>isset($entity['image'])? asset('images/categories/' . $imageName): $imageName
    ]);
    return $cat;

   }
   public function delete($id){
    $cat = $this->category->findOrFail($id);
    if (!empty($cat->image)) {
      $imagePath = public_path('images/categories/' . basename($cat->image));  

      if (file_exists($imagePath) && is_file($imagePath)) {
          unlink($imagePath);
      }
     
  }         

    return $cat->delete();

   }

 }
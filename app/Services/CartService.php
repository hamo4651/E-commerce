<?php
namespace App\Services;

use Gloudemans\Shoppingcart\Facades\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartService
{
  

  
    public function addToCart($productId, $quantity, $request)
    {
        Cart::instance('user_' . Auth::id())->restore(Auth::id());
        $product = Product::findOrFail($productId);

        $images = json_decode($product->images, true);
        $firstImage = !empty($images) ? $images[0] : 'default.jpg';

        $price = $product->discounted_price ?? $product->price;
        $locale = $request->header('Accept-Language', 'en'); 

        Cart::instance('user_' . Auth::id())->add(
            $product->id,
            $locale === 'ar' ? $product->name_ar : $product->name_en,
            $quantity,
            $price,
            1,
            ['image' => $firstImage, 'unique_id' => uniqid()]
        );

        Cart::instance('user_' . Auth::id())->store(Auth::id()); // حفظ في قاعدة البيانات
        return Cart::instance('user_' . Auth::id())->content();
    }

   
    public function getCartItems()
    {
        $storedCart = DB::table('shoppingcart')
            ->where('identifier', Auth::id())
            ->first();
    //    dd($storedCart);
        if ($storedCart) {
            return unserialize($storedCart->content);
        }
    
        return [];
    }
    
    
    

    public function updateCartItem($rowId, $quantity)
    {
        Cart::instance('user_' . Auth::id())->restore(Auth::id());

        Cart::instance('user_' . Auth::id())->update($rowId, $quantity);
        Cart::instance('user_' . Auth::id())->store(Auth::id()); 
        return Cart::instance('user_' . Auth::id())->content();
    }

    public function removeCartItem($rowId)
    {
        Cart::instance('user_' . Auth::id())->restore(Auth::id());

        Cart::instance('user_' . Auth::id())->remove($rowId);
        Cart::instance('user_' . Auth::id())->store(Auth::id()); 
        return Cart::instance('user_' . Auth::id())->content();
    }

    public function clearCart()
    {

        Cart::instance('user_' . Auth::id())->destroy();
        Cart::instance('user_' . Auth::id())->store(Auth::id()); 
        return Cart::instance('user_' . Auth::id())->content();
    }
}

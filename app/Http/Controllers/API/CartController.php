<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = $this->cartService->addToCart($request->product_id, $request->quantity);
        return response()->json($cart, 200);
    }

    public function getCartItems()
    {
        
        $cart = $this->cartService->getCartItems();
        return response()->json($cart, 200);
    }

    public function updateCartItem(Request $request, $rowId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = $this->cartService->updateCartItem($rowId, $request->quantity);
        return response()->json($cart, 200);
    }

    public function removeCartItem($rowId)
    {
        $cart = $this->cartService->removeCartItem($rowId);
        return response()->json($cart, 200);
    }

    public function clearCart()
    {
        $cart = $this->cartService->clearCart();
        return response()->json($cart, 200);
    }
}

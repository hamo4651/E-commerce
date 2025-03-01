<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Add a product to the cart
     * 
     * @group Cart Management
     * 
     * @bodyParam product_id int required The ID of the product. Example: 1
     * @bodyParam quantity int required The quantity of the product. Minimum: 1. Example: 2
     * 
     * @response 200 {
     *    "message": "Product added to cart successfully",
     *    "cart": { ...cart data... }
     * }
     * @response 422 {
     *    "message": "Validation error",
     *    "errors": { "product_id": ["The selected product_id is invalid."] }
     * }
     */
    public function addToCart(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validate->errors()
            ], 422);
        }

        $cart = $this->cartService->addToCart($request->product_id, $request->quantity, $request);
        return response()->json([
            'message' => 'Product added to cart successfully',
            'cart' => $cart
        ], 200);
    }

    /**
     * Retrieve all items in the cart
     * 
     * @group Cart Management
     * 
     * @response 200 {
     *    "cart": { ...cart items... }
     * }
     */
    public function getCartItems()
    {
        $cart = $this->cartService->getCartItems();
        return response()->json(['cart' => $cart], 200);
    }

    /**
     * Update the quantity of a cart item
     * 
     * @group Cart Management
     * 
     * @urlParam rowId string required The unique row ID of the cart item. Example: "abc123"
     * @bodyParam quantity int required The new quantity. Minimum: 1. Example: 3
     * 
     * @response 200 {
     *    "message": "Cart item updated successfully",
     *    "cart": { ...updated cart data... }
     * }
     */
    public function updateCartItem(Request $request, $rowId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = $this->cartService->updateCartItem($rowId, $request->quantity);
        return response()->json([
            'message' => 'Cart item updated successfully',
            'cart' => $cart
        ], 200);
    }

    /**
     * Remove a specific item from the cart
     * 
     * @group Cart Management
     * 
     * @urlParam rowId string required The unique row ID of the cart item. Example: "abc123"
     * 
     * @response 200 {
     *    "message": "Cart item removed successfully",
     *    "cart": { ...updated cart data... }
     * }
     */
    public function removeCartItem($rowId)
    {
        $cart = $this->cartService->removeCartItem($rowId);
        return response()->json([
            'message' => 'Cart item removed successfully',
            'cart' => $cart
        ], 200);
    }

    /**
     * Clear all items from the cart
     * 
     * @group Cart Management
     * 
     * @response 200 {
     *    "message": "Cart cleared successfully",
     *    "cart": []
     * }
     */
    public function clearCart()
    {
        $cart = $this->cartService->clearCart();
        return response()->json([
            'message' => 'Cart cleared successfully',
            'cart' => $cart
        ], 200);
    }
}

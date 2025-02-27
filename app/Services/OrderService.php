<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function createOrder($request)
    {
        $storedCart = DB::table('shoppingcart')->where('identifier', Auth::id())->first();

        if (!$storedCart || empty($storedCart->content)) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $cartItems = unserialize($storedCart->content);
        if (empty($cartItems)) {
            return response()->json(['message' => 'Cart is empty or corrupted'], 400);
        }
        $products = [];
        $total = 0;

        foreach ($cartItems as $item) {
            $subtotal = $item->price * $item->qty;
            $products[] = [
                'id' => $item->id,
                'name' => $item->name,
                'qty' => $item->qty,
                'price' => $item->price,
                'image' => $item->options['image'] ?? null,
                'subtotal' => $subtotal,
            ];
            $total += $subtotal;
        }
        $order = Order::create([
            'order_number' => Str::uuid(),
            'user_id' => Auth::id(),
            'products' => json_encode($products),
            'city' => $request->city,
            'address' => $request->address,
            'building_number' => $request->building_number,
            'status' => 'pending',
            'payment_status' => 'not_paid',
            'payment_method' => $request->payment_method,
            'total' => $total,
        ]);
        DB::table('shoppingcart')->where('identifier', Auth::id())->delete();

        return $order;
    }
    public function getUserOrders()
    {
        return Order::where('user_id', Auth::id())->get();
    }

    public function getOrderById($id)
    {
        return Order::with('user')->findOrFail($id);
    }

    public function updateOrderStatus($id, $status)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => $status]);
        return $order;
    }
}

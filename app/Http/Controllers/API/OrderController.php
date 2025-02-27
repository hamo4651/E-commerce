<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store(Request $request)
    {
        $order = $this->orderService->createOrder($request);
        return response()->json($order, 201 ,[], JSON_UNESCAPED_UNICODE);
    }
    public function index()
    {
        return response()->json($this->orderService->getUserOrders());
    }
    public function show($id)
    {
        $order = $this->orderService->getOrderById($id);

        return response()->json([
            'order_number' => $order->order_number,
            'products' => json_decode($order->products),
            'delivery_address' => [
                'city' => $order->city,
                'address' => $order->address,
                'building_number' => $order->building_number,
            ],
            'user' => [
                'id' => $order->user->id,
                'name' => $order->user->name,
                'email' => $order->user->email,
            ],
            'status' => $order->status,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'total_price' => $order->total,
        ]);
    }
    public function updateStatus(Request $request, $id)
    {
        $order = $this->orderService->updateOrderStatus($id, $request->status);
        return response()->json(['message' => 'Order status updated', 'order' => $order]);
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Models\Order;
use App\Services\StripeService;
use App\Services\PaypalService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    protected $stripeService;
    protected $paypalService;

    public function __construct(StripeService $stripeService, PaypalService $paypalService)
    {
        $this->stripeService = $stripeService;
        $this->paypalService = $paypalService;
    }

    public function stripePayment(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'order_number' => 'required|exists:orders,order_number',
            'token' => 'required'

        ]);
      
        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        $order = Order::where('order_number', $request->order_number)->first();
      
        $amount = $order->total;
        //  dd($order);
        if($order->payment_status == 'paid'){
            return response()->json(['message' => 'Order already paid'], 500);
        }
        $response = $this->stripeService->charge($amount, $request->token);
              
        if ($response['status']) {
            $order->update(['payment_status' => 'paid']);
            return response()->json(['message' => 'Payment successful', 
            'id' => $response['charge']->id,
            'amount' => $response['charge']->amount,
            'currency' => $response['charge']->currency,
            'status' => $response['charge']->status,
            "receipt_url" => $response['charge']->receipt_url
        ], 200);
        } else {
            return response()->json(['error' => $response['error']], 500);
        }
    }

    public function paypalPayment(Request $request)
    {
        $request->validate([
            'order_number' => 'required|exists:orders,order_number',
        ]);

        $order = Order::where('order_number', $request->order_number)->first();

        if ($order->payment_status == 'paid') {
            return response()->json(['message' => 'Order already paid'], 500);
        }
        $amount = $order->total;

        $response = $this->paypalService->createPayment($amount);

        return response()->json($response);
    }

    public function paypalCapture(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'order_number' => 'required|exists:orders,order_number'
        ]);

        $order = Order::where('order_number', $request->order_number)->first();

        if ($order->payment_status == 'paid') {
            return response()->json(['message' => 'Order already paid'], 500);
        }
        $response = $this->paypalService->capturePayment($request->order_id);

        if ($response['status'] ?? false) {
            $order->update(['payment_status' => 'paid']);
        }

        return response()->json($response);
    }
}

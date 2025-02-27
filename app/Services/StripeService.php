<?php 
namespace App\Services;

use Stripe\Stripe;
use Stripe\Charge;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function charge($amount, $token)
    {
        try {
            $charge = Charge::create([
                "amount" => $amount * 100, // تحويل لـ سنت
                "currency" => "usd",
                "source" => $token,
                "description" => "Stripe Payment"
            ]);

            return ['status' => true, 'charge' => $charge];
        } catch (\Exception $e) {
            return ['status' => false, 'error' => $e->getMessage()];
        }
    }
}

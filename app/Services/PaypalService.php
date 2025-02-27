<?php 
namespace App\Services;

use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalService
{
    protected $paypal;

    public function __construct()
    {
        $this->paypal = new PayPalClient();
        $this->paypal->setApiCredentials(config('paypal'));
        $token = $this->paypal->getAccessToken();
        $this->paypal->setAccessToken($token);
    }

    public function createPayment($amount)
    {
        return $this->paypal->createOrder([
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "amount" => [
                    "currency_code" => "USD",
                    "value" => $amount
                ]
            ]]
        ]);
    }

    public function capturePayment($orderId)
    {
        return $this->paypal->capturePaymentOrder($orderId);
    }
}

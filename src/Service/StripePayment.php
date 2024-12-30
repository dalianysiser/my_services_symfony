<?php

namespace App\Service;

use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Response;

class StripePayment
{
    private $redirectUrl;
    public function __construct() {
        Stripe::setApiKey($_SERVER['STRIPE_SECRET']);
        //2024-11-20.acacia
        Stripe::setApiVersion('2024-11-20.acacia');
    }


    public function startPayment($cart, $shippingCost, $orderId): Response
    { 
        $cartServices = $cart['cart'];
        $services = [
            [
                'name'=>'shipping Cost',
                'price'=>$shippingCost,
                'quantity'=>1
            ]
        ];
        foreach ($cartServices as $value) {
            $serviceItem =[];
            $serviceItem['name'] = $value['service']->getName();
            $serviceItem['price'] = $value['service']->getPrice();
            $serviceItem['quantity'] = $value['qte'];
            $services[]= $serviceItem;
        }
       $session = Session::create([
            'line_items'=>[
                array_map(
                    fn(array $service)=>[
                        'quantity'=>$service['quantity'],
                        'price_data'=>[
                            'currency'=>'EUR',
                            'product_data'=>[
                                'name'=>$service['name']
                            ],
                            'unit_amount'=>$service['price'] * 100
                        ],
                        ],$services
                )

            ],
            'mode'=>'payment',
            'cancel_url'=>'http://127.0.0.1:8000/pay/cancel',
            'success_url'=>'http://127.0.0.1:8000/pay/success',
            'billing_address_collection'=>'required',
            'shipping_address_collection'=>[
                'allowed_countries'=>['NL','ES', 'CU','US']
            ],
           // 'payment_intent_data'=>[
                'metadata'=>[
                    // 'orderId'=>$orderId,
                    // 'shippingCost'=>$shippingCost,
                    // 'last_name'=>'Dev'
                ]
            //]
            
       ]);
 
       $this->redirecUrl = $session->url;
    }

    public function getStripeRedirectUrl(): Response
    {
        return $this->redirecUrl;
    }
}
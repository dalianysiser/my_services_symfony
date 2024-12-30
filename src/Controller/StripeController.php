<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class StripeController extends AbstractController
{
    #[Route('/pay/success', name: 'app_stripe_success')]
    public function success(Cart $cart, SessionInterface $sessionInterface): Response
    {
        $sessionInterface->set('cart', []);
        return $this->render('stripe/index.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }

    #[Route('/pay/cancel', name: 'app_stripe_cancel')]
    public function cancel(): Response
    {
        return $this->render('stripe/index.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }

    #[Route('/stripe/notify', name: 'app_stripe_notify')]
    public function stripeNotify(Request $request, OrderRepositoy $orderRepository, EntityManagerInterface $entityManager): Response
    {
        Stripe::setApiKey($_SERVER['STRIPE_SECRET']);
        $endpoint_secret = 'poner valorq s guardo anteriormente ver video';
        $payload = $request->getContent();
        $sig_header = $request->headers->get('stripe-signature');
        $event = null;
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );

        } catch (\UnexpectedValueException $e){
                return new Response('payload invalid', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e){
            return new Response('signature invalid', 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $fileName = 'stripe-details-'.uniqid().'txt';
                $orderId = $paymentIntent->metadata->orderId;
                $order = $orderRepository->find($orderId);
                $cartPrice = $order->getTotalPrice();
                $stripeTotalAmount = $paymentIntent->amount/100;
                if ($cartPrice == $stripeTotalAmount) {
                    $order->setPaymentCompleted(1);
                    $entityManager->flush();
                }
                
                //file_put_contents($fileName, $orderId);
                break;
            case 'payment_method.attached':
                $paymentMethod = $event->data->object;
                break;
            
            default:
                break;
        }

         return new Response('event received', 200);
    }
}

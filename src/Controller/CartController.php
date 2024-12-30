<?php

namespace App\Controller;

use App\Repository\ServiceRepository;
use App\Service\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    public function __construct(private readonly ServiceRepository $serviceRepository) { }

    #[Route('/cart', name: 'app_cart', methods: ['GET'])]
    public function index(SessionInterface $sessionInterface, Cart $cart): Response
    {
        $data = $cart->getCart($sessionInterface);
        $cartServices = $data['cart'];
        $services =[];
        foreach ($cartServices as $value) {
            # code...
        }
        return $this->render('cart/index.html.twig', [
            'items' =>$data['cart'],
            'total'=>$data['total']
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_new', methods: ['GET'])]
    public function addCart(int $id, SessionInterface $sessionInterface) : Response {
        $cart =$sessionInterface->get('cart',[]);
        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id]= 1;
        }
        $sessionInterface->set('cart',$cart);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/service/{id}', name: 'app_cart_service_remove', methods: ['GET'])]
    public function removeToCart(int $id, SessionInterface $sessionInterface) : Response {
        $cart =$sessionInterface->get('cart',[]);
        if (!empty($cart[$id])) {
            unset($cart[$id]);
        } 
        $sessionInterface->set('cart',$cart);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/', name: 'app_cart_remove', methods: ['GET'])]
    public function removeCart(SessionInterface $sessionInterface) : Response {
        $sessionInterface->set('cart',[]);
        return $this->redirectToRoute('app_cart');
    }

}

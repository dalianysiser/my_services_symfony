<?php

namespace App\Service;

use App\Repository\ServiceRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    public function __construct(private readonly ServiceRepository $serviceRepository) { }
    public function getCart(SessionInterface $sessionInterface):array
    {
        $cart = $sessionInterface->get('cart',[]);
        $cartData=[];
       foreach ($cart as $index => $qte) { 
            $cartData[]=[
                'service'=>$this->serviceRepository->find($index),
                'qte'=>$qte
            ];
       }
       $total = array_sum(array_map(function ($item){
            return $item['service']->getPrice() * $item['qte'];
       }, $cartData));
       return [
            'cart'=>$cartData,
            'total'=>$total
       ];
    }
}
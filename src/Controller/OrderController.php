<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Order;
use App\Entity\OrderServices;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use App\Repository\ServiceRepository;
use App\Service\Cart;
use App\Service\StripePayment;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    public function __construct(private MailerInterface $mailerInterface) {}

    #[Route('/order', name: 'app_order')]
    public function index(Request $request, 
        SessionInterface $sessionInterface, 
        ServiceRepository $serviceRepository,
        EntityManagerInterface $entityManagerInterface,
        OrderRepository $orderRepository,
        Cart $cart): Response
    {
    
        $data = $cart->getCart($sessionInterface);
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
                if (!empty($data['total'])) {
                    $totalPrice = $data['total'] + $order->getCity()->getShippingCost();
                    $order->setTotalPrice($totalPrice);
                    $order->setCreatedAt(new \DateTimeImmutable());
                    $order->setPaymentCompleted(0);
                    $entityManagerInterface->persist($order);
                    $entityManagerInterface->flush();
                    foreach ($data['cart'] as $value) {
                        $orderService = new OrderServices;
                        $orderService->setOrder($order);
                        $orderService->setService($value['service']);
                        $orderService->setQte($value['qte']);
                        $entityManagerInterface->persist($orderService);
                        $order->addOrderService($orderService);
                        $entityManagerInterface->flush();
                    }
                    
                    if ($order->isPayOnDelivery()) {  
                        $sessionInterface->set('cart',[]);
                        $html = $this->renderView('mail/orderConfirm.html.twig', [
                            'order'=>$order
                        ]);
                        $email = (new Email())
                                ->from('my_services@gmail.com')
                                ->to($order->getEmail())
                                ->subject('Confirmation of receipt of the order')
                                ->html($html);
                        $this->mailerInterface->send($email);
                        return $this->redirectToRoute('order_ok');
                    }

                    $payment = new StripePayment();
                    $schippingCost = $order->getCity()->getShippingCost();
                    $payment->startPayment($data, $schippingCost, $order->getId());
                    $stripeRedirectUrl = $payment->getStripeRedirectUrl();
                    dd($stripeRedirectUrl);
                    header("location: $stripeRedirectUrl");
                    //return $this->redirect($stripeRedirectUrl);
                }
               
           
        }
        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'total'=>$data['total']
        ]);
    }

    #[Route('/editor/order/{type}/', name: 'app_orders_show')]
    public function getAllOrder($type, OrderRepository $orderRepository, Request $request, PaginatorInterface $paginatorInterface): Response
    {
        if ($type == 'is-completed') {
            $data = $orderRepository->findBy(['isCompleted'=>1],['id'=>'DESC']);
        }elseif ($type == 'pay-on-stripe-not-delivered') {
            $data = $orderRepository->findBy(['isCompleted'=>null,'payOnDelivery'=>0,'isPaymentCompleted'=>1],['id'=>'DESC']);
        }elseif ($type == 'pay-on-stripe-is-delivered') {
            $data = $orderRepository->findBy(['isCompleted'=>1,'payOnDelivery'=>0,'isPaymentCompleted'=>1],['id'=>'DESC']);
        }
        //$data = $orderRepository->findBy([],['id'=>'DESC']);
        $order = $paginatorInterface->paginate(
            $data,
            $request->query->getInt('page', 1),
            1
        );
        return $this->render('order/order.html.twig', [
            "orders"=>$order
        ]);
    }

   
    #[Route('/editor/order/{id}/is-completed/update', name: 'app_orders_is_completed_update')]
    public function isCompletedUpdate($id, OrderRepository $orderRepository, EntityManagerInterface $entityManagerInterface, Request $request): Response
    {
       $order = $orderRepository->find($id);
       $order->setCompleted(true);
       $entityManagerInterface->flush();
       $this->addFlash('success', 'Updated!');
       return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/editor/order/{id}/remove', name: 'app_orders_remove')]
    public function removeOrder(Order $order, EntityManagerInterface $entityManagerInterface): Response
    {
        $entityManagerInterface->remove($order);
        $entityManagerInterface->flush();
        $this->addFlash('danger', 'Deleted!');
        return $this->redirectToRoute('app_orders_show');
    }

    #[Route('/order-ok-message', name: 'order_ok')]
    public function orderMessage(): Response
    {
        return $this->render('order/order_message.html.twig');
    }
   
    #[Route('/city/{id}/shipping/cost', name: 'app_city_shipping_cost')]
    public function cityShippingCost(City $city): Response
    {
        $citySippinPrice = $city->getShippingCost();

        return new Response(json_encode(['status'=>200, 'message'=>'on', 'content'=>$citySippinPrice]));
    }
}

<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSucessController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface  $entityManager)
    {
      $this->entityManager = $entityManager;
    }


    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_sucess")
     */
    public function index(Cart $cart,$stripeSessionId): Response
    {
      $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

      if(!$order || $order->getUser() != $this->getUser() ){
        return $this->redirectToRoute('home');
      }


      if(!$order->getIsPaid()){
        $cart->remove();

        $order->setIsPaid(1);
        $this->entityManager->flush();

        //envoyer un email au client pour lui confirmer sa commande
      }

      return $this->render('order_success/index.html.twig',[
        'order' => $order,
      ]);
    }
}

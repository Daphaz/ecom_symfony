<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\Mail;
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


      if($order->getState() == 0 ){
        $cart->remove();

        $order->setState(1);
        $this->entityManager->flush();

        //envoyer un email au client pour lui confirmer sa commande
        $mail = new Mail();

        $content = "Bonjour ".$order->getUser()->getFirstName()."<br/>Merci pour votre commande, ipsum dolor sit amet consectetur adipisicing elit. Alias tenetur possimus eius maiores aspernatur sed quam recusandae laborum quo debitis, sequi dolorum aliquam dolore. Facere ex corrupti qui! Eaque, dicta!";

        $mail->send($order->getUser()->getEmail(),$order->getUser()->getFirstName(),"Votre commande Test Symfony est validée",$content);
      }

      return $this->render('order_success/index.html.twig',[
        'order' => $order,
      ]);
    }
}

<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
      $this->entityManager = $entityManager;
    }

    /**
     * @Route("/commande", name="order")
     */
    public function index(Cart $cart): Response
    {
      if(!$this->getUser()->getAddresses()->getValues()){
        return $this->redirectToRoute('account_address_add');
      }


      $form = $this->createForm(OrderType::class,null,[
        'user' => $this->getUser(),
      ]);

      return $this->render('order/index.html.twig',[
        'form' => $form->createView(),
        'cart' => $cart->getFull(),
      ]);
    }

    /**
     * @Route("/commande/recapitulatif", name="order_recap", methods={"POST"})
     */
    public function add(Cart $cart, Request $request): Response
    {

      $form = $this->createForm(OrderType::class,null,[
        'user' => $this->getUser(),
      ]);

      $form->handleRequest($request);

      if($form->isSubmitted() && $form->isValid()){
        $date = new DateTime();
        $carriers = $form->get('carriers')->getData();
        $delivery = $form->get('addresses')->getData();
        $delivery_content = $delivery->getFirstname().' '.$delivery->getLastname();
        $delivery_content .= '<br/>'.$delivery->getPhone();

        if($delivery->getCompany()){
          $delivery_content .= '<br/>'.$delivery->getCompany(); 
        }

        $delivery_content .= '<br/>'.$delivery->getAddress();
        $delivery_content .= '<br/>'.$delivery->getPostal().' '.$delivery->getCity();
        $delivery_content .= '<br/>'.$delivery->getCountry();

        $order = new Order();
        $order->setUser($this->getUser());
        $order->setCreatedAt($date);
        $order->setCarrierName($carriers->getName());
        $order->setCarrierPrice($carriers->getPrice());
        $order->setDelivery($delivery->getAddress());
        $order->setDelivery($delivery_content);
        $order->setIsPaid(0);

        $this->entityManager->persist($order);

        foreach($cart->getFull() as $product){
          $orderDetails = new OrderDetails();
          $orderDetails->setMyOrder($order);
          $orderDetails->setProduct($product['product']->getName());
          $orderDetails->setQuantity($product['quantity']);
          $orderDetails->setPrice($product['product']->getPrice());
          $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);
          $this->entityManager->persist($orderDetails);
        }

        //$this->entityManager->flush();

        Stripe::setApiKey('sk_test_51IgxTXKVY4aQ8AGQ0fOeL1bQLpUYkciNodNIl9V5a6ZGX4hDgXiR4RSP06rCNPceWGsDm0gio2M7C5mCOrIRBfvA00phS5MYxb');

        $YOUR_DOMAIN = 'http://127.0.0.1:800';

        $checkout_session = Session::create([
          'payment_method_types' => ['card'],
          'line_items' => [[
            'price_data' => [
              'currency' => 'eur',
              'unit_amount' => 2000,
              'product_data' => [
                'name' => 'Stubborn Attachments',
                'images' => ["https://i.imgur.com/EHyR2nP.png"],
              ],
            ],
            'quantity' => 1,
          ]],
          'mode' => 'payment',
          'success_url' => $YOUR_DOMAIN . '/success.html',
          'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
        ]);

        dump($checkout_session->id);
        dd($checkout_session);

        return $this->render('order/add.html.twig',[
          'cart' => $cart->getFull(),
          'carrier' => $carriers,
          'delivery' => $delivery_content,
        ]);
      }

      return $this->redirectToRoute('cart');
    }
}

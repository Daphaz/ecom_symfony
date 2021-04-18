<?php

namespace App\Controller;

use App\Classes\Mail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {

        $mail = new Mail();

        $mail->send("damooctest@gmail.com","Jhon Doe","test d'envoie d'email","ici le contenue d'email,<br>test de html en même temps <h1>Au revoir</h1>");


        return $this->render('home/index.html.twig');
    }
}

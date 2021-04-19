<?php

namespace App\Controller;

use App\Classes\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
      $this->entityManager = $entityManager;
    }

    /**
     * @Route("/inscription", name="register")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {

        $notification = null;
        $user = new User();
        $form = $this -> createForm(RegisterType::class, $user );

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
          $user = $form->getData();

          $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());

          if(!$search_email){
            $password = $encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($password);
  
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $mail = new Mail();

            $content = "Bonjour ".$user->getFirstName()."<br/>Bienvenue Lorem, ipsum dolor sit amet consectetur adipisicing elit. Alias tenetur possimus eius maiores aspernatur sed quam recusandae laborum quo debitis, sequi dolorum aliquam dolore. Facere ex corrupti qui! Eaque, dicta!";

            $mail->send($user->getEmail(),$user->getFirstName(),"Bienvenue sur Test Symfony",$content);
  
            $notification = "Votre inscription c'est correctement déroulé, vous pouvez vous connecter a votre compte";
          } else {
            $notification = "l'email que vous avez renseigner existe déjà";
          }
        }

        return $this->render('register/index.html.twig',[
          'form' => $form -> createView(),
          'notification' => $notification,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Classes\Mail;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
      $this->entityManager = $entityManager;
    }


    /**
     * @Route("/mot-de-passe-oublie", name="reset_password")
     */
    public function index(Request $request): Response
    {
        $notification = null;

        if($this->getUser()){
          return $this->redirectToRoute('home');
        }

        if($request->get('email')){
          $user = $this->entityManager->getRepository(User::class)->findOneByEmail($request->get('email'));

          if($user){
            $resetPassowrd = new ResetPassword();
            $resetPassowrd->setUser($user);
            $resetPassowrd->setToken(uniqid());
            $resetPassowrd->setCreatedAt(new DateTime());
            $this->entityManager->persist($resetPassowrd);
            $this->entityManager->flush();

            $url = $this->generateUrl('update_password',[
              'token' => $resetPassowrd->getToken(),
            ]);

            $content = "Bonjour ".$user->getFirstname()."<br/>Vous avez demandez à réinitialiser votre mot de passe sur le site Test Symfony.<br/><br/>";
            $content .= "Merci de bien vouloir cliquer sur le lien suivant pour <a href='".$url."'>mettre à jour votre mot de passe<a/>";
            
            $mail = new Mail();
            $mail->send($user->getEmail(),$user->getFirstname().' '.$user->getLastname(),"Demande de nouveau mot de passe",$content);

            $notification = "Vous allez recevoir un email avec la procédure pour réinitialiser votre mot de passe.";
          }else{
            $notification = "Veuillez vérifiez votre email";
          }
        }

        return $this->render('reset_password/index.html.twig',[
          'notification' => $notification
        ]);
    }

    /**
     * @Route("/modifier-mon-mot-de-passe/{token}", name="update_password")
     */
    public function update(Request $request, UserPasswordEncoderInterface $encoder,$token)
    {
      $reset_password = $this->entityManager->getRepository(ResetPassword::class)->findOneByToken($token);

      if(!$reset_password){
        return $this->redirectToRoute('reset_password');
      }

      $now = new DateTime();
      if($now > $reset_password->getCreatedAt()->modify('+ 1 hour')){
          $this->addFlash('notice','Votre demande de mot de passe à expiré, merci de renouveller');
          return $this->redirectToRoute('reset_password');
      }

      $form = $this -> createForm(ResetPasswordType::class );

      $form->handleRequest($request);

      if($form->isSubmitted() && $form->isValid()){
        $new_password = $form->get('password')->getData();
        $user = $reset_password->getUser();
        $password = $encoder->encodePassword($user,$new_password);

        $user->setPassword($password);

        $this->entityManager->flush();

        $this->addFlash('notice','Votre mot de passe à été mis à jour');

        return $this->redirectToRoute('app_login');
      }

      return $this->render('reset_password/update.html.twig',[
        'form' => $form->createView(),
      ]);
    }
}

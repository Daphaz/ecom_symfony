<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
              'label' => 'Email',
              'constraints' => new Email([
                'message' => "Cet email {{ value }} n'est pas un email valide.",
                'mode' => 'strict'
              ])
            ])
            ->add('firstname', TextType::class, [
              'label' => 'Votre prénom',
              'constraints' => new Length([
                'min' => 2,
                'max' => 30,
                'minMessage' => 'Votre prénom doit avoir au moins {{ limit }} charactères.',
                'maxMessage' => 'Votre prénom doit avoir au maximum {{ limit }} charactères.'
              ])
            ])
            ->add('lastname',TextType::class, [
              'label' => 'Votre nom',
              'constraints' => new Length([
                'min' => 2,
                'max' => 30,
                'minMessage' => 'Votre nom doit avoir au moins {{ limit }} charactères.',
                'maxMessage' => 'Votre nom doit avoir au maximum {{ limit }} charactères.'
              ])
            ])
            ->add('password', RepeatedType::class, [
              'type' => PasswordType::class,
              'invalid_message' => 'les mot de passe doivent être identique.',
              'required' => true,
              'first_options' => [ 'label' => 'Mot de passe'],
              'second_options' => [ 'label' => 'Confirmer votre mot de passe']
            ])
            ->add('submit', SubmitType::class, [
              'label' => "S'inscrire",
              'attr' => [
                'class' => 'btn btn_white'
              ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

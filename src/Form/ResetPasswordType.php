<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'les mot de passe doivent être identique.',
            'required' => true,
            'first_options' => [ 'label' => 'Mot de passe'],
            'second_options' => [ 'label' => 'Confirmer votre mot de passe']
          ])
          ->add('submit', SubmitType::class, [
            'label' => "Enregistrer",
            'attr' => [
              'class' => 'btn btn_white'
            ]
          ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

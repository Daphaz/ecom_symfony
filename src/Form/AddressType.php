<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class,[
              'label' => 'Nom de l\'addresse'
            ])
            ->add('firstname',TextType::class,[
              'label' => 'Prénom'
            ])
            ->add('lastname',TextType::class,[
              'label' => 'Nom'
            ])
            ->add('company',TextType::class,[
              'label' => 'société',
              'required' => false,
            ])
            ->add('phone',TelType::class,[
              'label' => 'Téléphone'
            ])
            ->add('postal',TextType::class,[
              'label' => 'code postale'
            ])
            ->add('city',TextType::class,[
              'label' => 'ville'
            ])
            ->add('country',CountryType::class,[
              'label' => 'pays'
            ])
            ->add('address',TextType::class,[
              'label' => 'Addresse'
            ])
            ->add('submit',SubmitType::class,[
              'label' => "Ajouter",
              'attr' => [
                'class' => 'btn btn_white'
              ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}

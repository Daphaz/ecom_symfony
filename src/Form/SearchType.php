<?php

namespace App\Form;

use App\Classes\Search;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('string', TextType::class,[
        'label' => false,
        'attr' => [
          'placeholder' => 'votre recherche...'
        ],
        'required' => false
      ])
      ->add('categories',EntityType::class,[
        'label' => false,
        'required' => false,
        'class' => Category::class,
        'multiple' => true,
        'expanded' => true,
      ])
      ->add('submit', SubmitType::class,[
        'label' => 'rechercher',
        'attr' => [
          'class' => 'btn btn_white'
        ]
      ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => Search::class,
      'method' => 'GET',
      'crsf_protected' => false,
    ]);
  }

  public function getBlockPrefix()
  {
    return '';
  }
}
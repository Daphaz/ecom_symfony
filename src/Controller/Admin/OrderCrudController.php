<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;

class OrderCrudController extends AbstractCrudController
{
    private $entityManager;
    private $crudUrlGenerator;

    public function __construct(EntityManagerInterface $entityManager, CrudUrlGenerator $crudUrlGenerator)
    {
      $this->entityManager = $entityManager;
      $this->crudUrlGenerator = $crudUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {
      $updatePreparation = Action::new('upadatePreparation', 'Préparation en cours','fas fa-box-open')->linkToCrudAction('updatePreparation');
      $updateDelivery = Action::new('upadateDelivery', 'Livraison en cours','fas fa-truck')->linkToCrudAction('updateDelivery');
      $updateFinish = Action::new('upadateFinish', 'Commande Terminée','fas fa-check-circle')->linkToCrudAction('updateFinish');
      return $actions
        ->add('detail',$updatePreparation)
        ->add('detail',$updateDelivery)
        ->add('detail',$updateFinish)
        ->add('index','detail');
    }

    public function updatePreparation(AdminContext $context)
    {
      $order = $context->getEntity()->getInstance();
      $order->setState(2);
      $this->entityManager->flush();

      $this->addFlash('notice', "<span style='color:green;'>La commande ".$order->getReference()." est bien en cours de preparation</span>");

      $url = $this->crudUrlGenerator->build()
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

      return $this->redirect($url);
    }

    public function updateDelivery(AdminContext $context)
    {
      $order = $context->getEntity()->getInstance();
      $order->setState(3);
      $this->entityManager->flush();

      $this->addFlash('notice', "<span style='color:green;'>La commande ".$order->getReference()." est bien en cours de livraison</span>");

      $url = $this->crudUrlGenerator->build()
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

      return $this->redirect($url);
    }

    public function updateFinish(AdminContext $context)
    {
      $order = $context->getEntity()->getInstance();
      $order->setState(4);
      $this->entityManager->flush();

      $this->addFlash('notice', "<span style='color:green;'>La commande ".$order->getReference()." est Terminée</span>");

      $url = $this->crudUrlGenerator->build()
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

      return $this->redirect($url);
    }

    public function configureCrud(Crud $crud): Crud
    {
      return $crud->setDefaultSort(['id' =>  'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('createdAt', 'Crée le'),
            TextField::new('user.getfullName', 'Client'),
            TextEditorField::new('delivery', 'Adresse de livraison')->onlyOnDetail(),
            MoneyField::new('total')->setCurrency('EUR'),
            TextField::new('carrierName', 'Transporteur'),
            MoneyField::new('carrierPrice','frais de port')->setCurrency('EUR'),
            ChoiceField::new('state')->setChoices([
              'Non Payée' => 0,
              'Payée' => 1,
              'Préparation en cours' => 2,
              'Livraison en cours' => 3,
              'Terminée' => 4,
            ]),
            ArrayField::new('orderDetails', 'Produits achetée')->hideOnIndex(),
        ];
    }
}

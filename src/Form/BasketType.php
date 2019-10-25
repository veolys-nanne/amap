<?php

namespace App\Form;

use App\Entity\Basket;
use App\EntityManager\ProductManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BasketType extends AbstractType
{
    protected $entityManager;
    protected $productManager;

    public function __construct(EntityManagerInterface $entityManager, ProductManager $productManager)
    {
        $this->entityManager = $entityManager;
        $this->productManager = $productManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('productQuantityCollection', CollectionType::class, [
                    'label' => false,
                    'entry_type' => ProductQuantityType::class,
                    'entry_options' => [
                        'label' => false,
                    ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Basket::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class MassCreditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'query_builder' => function (ServiceEntityRepository $entityRepository) use ($options) {
                    return $entityRepository->createQueryBuilder('product');
                },
                'label' => false,
                'required' => false,
                'attr' => ['class' => 'hidden'],
            ])
            ->add('member', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (ServiceEntityRepository $entityRepository) use ($options) {
                    return $entityRepository->createQueryBuilder('user');
                },
                'label' => false,
                'required' => false,
                'attr' => ['class' => 'hidden'],
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => false,
                'required' => false,
                'attr' => ['class' => 'hidden'],
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité',
                'required' => false,
            ])
        ;
    }
}

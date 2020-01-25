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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class GenerateCreditType extends AbstractType
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
            ->add('start', DateType::class, [
                'widget' => 'single_text',
                'label' => false,
                'required' => false,
                'attr' => ['class' => 'hidden'],
            ])
            ->add('end', DateType::class, [
                'widget' => 'single_text',
                'label' => false,
                'required' => false,
                'attr' => ['class' => 'hidden'],
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité',
                'required' => false,
                'attr' => ['class' => 'quantity form-popin'],
                'label_attr' => ['class' => 'form-popin'],
            ])
            ->add('submit', SubmitType::class, ['label' => 'Générer', 'attr' => ['class' => 'btn-success btn-block form-popin', 'data-sub-form' => '']])
        ;
    }
}

<?php

namespace App\Form;

use App\Entity\PlanningElement;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanningElementType extends AbstractType
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, [
                'label' => false,
                'widget' => 'single_text',
                'format' => 'dd/MM/y',
                'attr' => ['class' => 'hidden'],
            ])
            ->add('members', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (ServiceEntityRepository $entityRepository) {
                    return $entityRepository->getQueryBuilderForPlanning();
                },
                'label' => false,
                'multiple' => true,
                'attr' => ['class' => 'hidden'],
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PlanningElement::class,
        ]);
    }
}

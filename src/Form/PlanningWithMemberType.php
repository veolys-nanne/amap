<?php
namespace App\Form;

use App\Entity\Planning;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanningWithMemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('submit', SubmitType::class, ['label'=>'Envoyer', 'attr'=>['class'=>'btn-success btn-block']])
            ->add('elements', CollectionType::class, [
                'label' => false,
                'entry_type' => PlanningElementWithMemberType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'prototype' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Planning::class,
        ]);
    }
}

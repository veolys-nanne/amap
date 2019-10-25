<?php

namespace App\Form;

use App\DataTransformer\DateTimeToStringTransformer;
use App\Entity\PlanningElement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanningElementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', HiddenType::class)
        ;
        $builder->get('date')->addModelTransformer(new DateTimeToStringTransformer());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PlanningElement::class,
        ]);
    }
}

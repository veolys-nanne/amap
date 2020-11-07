<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BillFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => ['autocomplete' => 'off', 'class' => 'date-picker'],
                'required' => false,
            ])
            ->add('end', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => ['autocomplete' => 'off', 'class' => 'date-picker'],
                'required' => false,
            ])
            ->add('pdf', HiddenType::class, [
                'data' => '0',
            ])
            ->add('submit', SubmitType::class, ['label' => 'Extraire', 'attr' => ['class' => 'btn-success btn-block']]);
    }
}

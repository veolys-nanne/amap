<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class PreviewEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('to', CollectionType::class, [
                'label' => false,
                'entry_type' => HiddenType::class,
            ])
            ->add('subject', HiddenType::class)
            ->add('body', HiddenType::class)
            ->add('part', HiddenType::class);
    }
}

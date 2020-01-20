<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class FormatContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('extra', TextareaType::class, ['label' => 'Corps'])
            ->add('preview', SubmitType::class, ['label' => 'Prévisualiser', 'attr' => ['class' => 'btn-info btn-block']])
            ->add('email', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn-success btn-block']]);
    }
}

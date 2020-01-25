<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FormatEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('extra', TextareaType::class, [
                'label' => 'Texte Complémentaire',
                'attr' => ['class' => 'form-popin extra-formMail'],
                'label_attr' => ['class' => 'form-popin'],
                'required' => false,
            ])
            ->add('reference', TextType::class, [
                'label' => false,
                'attr' => ['class' => 'hidden extra-formMail'],
                'required' => false,
            ])
            ->add('preview', SubmitType::class, ['label' => 'Prévisualiser', 'attr' => [
                'class' => 'btn-info btn-block form-popin email-formMail',
            ]])
            ->add('email', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'btn-success btn-block form-popin email-formMail',
                    'data-sub-form' => '.extra-formMail',
                    'data-button' => '.email-formMail',
                ],
            ]);
    }
}

<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormatEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $buttonCss = 'email';
        $formCss = 'extra';
        if (null !== $options['builder']) {
            $buttonCss .= '-'.$options['builder']->getName();
            $formCss .= '-'.$options['builder']->getName();
        }
        $builder
            ->add('extra', TextareaType::class, [
                'label' => 'Texte Complémentaire',
                'attr' => ['class' => 'form-popin '.$formCss],
                'label_attr' => ['class' => 'form-popin'],
                'required' => false,
            ])
            ->add('preview', SubmitType::class, ['label' => 'Prévisualiser', 'attr' => [
                'class' => 'btn-info btn-block form-popin '.$buttonCss,
            ]])
            ->add('email', SubmitType::class, ['label' => $options['submitLabel'], 'attr' => [
                'class' => 'btn-success btn-block '.$buttonCss,
                'data-sub-form' => '.'.$formCss,
                'data-button' => '.'.$buttonCss,
            ]]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'submitLabel' => 'Envoyer',
            'builder' => null,
        ]);
    }
}

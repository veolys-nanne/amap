<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;

class FormatEmailType extends AbstractType
{
    protected $url;

    public function __construct(RouterInterface $router)
    {
        $this->url = $router->generate('document_form_image');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('extra', TextareaType::class, [
                'label' => 'Texte Complémentaire',
                'attr' => ['class' => 'extra-formMail', 'data-tiny-mce-url' => $this->url],
                'required' => false,
            ])
            ->add('reference', TextType::class, [
                'label' => false,
                'attr' => ['class' => 'hidden extra-formMail'],
                'required' => false,
            ])
            ->add('preview', SubmitType::class, ['label' => 'Prévisualiser', 'attr' => [
                'class' => 'btn-info btn-block email-formMail',
            ]])
            ->add('email', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'btn-success btn-block email-formMail',
                    'data-sub-form' => '.extra-formMail',
                    'data-button' => '.email-formMail',
                ],
            ]);
    }
}

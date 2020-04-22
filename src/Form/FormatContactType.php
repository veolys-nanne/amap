<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;

class FormatContactType extends AbstractType
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
                'label' => 'Corps',
                'attr' => ['data-tiny-mce-url' => $this->url],
            ])
            ->add('preview', SubmitType::class, ['label' => 'Prévisualiser', 'attr' => ['class' => 'btn-info btn-block']])
            ->add('email', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn-success btn-block']]);
    }
}

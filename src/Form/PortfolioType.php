<?php
namespace App\Form;

use App\Entity\Portfolio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PortfolioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('thumbnailCollection', CollectionType::class, [
                'label' => 'Portfolio',
                'entry_type' => ThumbnailType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'attr' => [
                    'class' => 'collection',
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Portfolio::class,
        ]);
    }
}

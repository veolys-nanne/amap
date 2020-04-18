<?php

namespace App\Form;

use App\Entity\Thumbnail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ThumbnailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', VichImageType::class, [
                'label' => 'Image',
                'download_uri' => true,
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
            ]);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $thumbnail = $event->getData();
            $portfolio = $event->getForm()->getParent()->getParent()->getData();
            $thumbnail->setPortfolio($portfolio);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Thumbnail::class,
        ]);
    }
}

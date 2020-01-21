<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvailabilityScheduleElementsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            $availabilityScheduleElements = $event->getData();
            $builder = $event->getForm();
            $builder
                ->add('form', CollectionType::class, [
                    'label' => $options['label'],
                    'entry_type' => AvailabilityScheduleElementType::class,
                    'data' => $availabilityScheduleElements,
                    'entry_options' => [
                        'label' => false,
                        'type' => $options['type'],
                    ],
                ]);
        });
        $builder
            ->add('form')
            ->add('submit', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn-success btn-block']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'type' => 'date',
        ]);
    }
}

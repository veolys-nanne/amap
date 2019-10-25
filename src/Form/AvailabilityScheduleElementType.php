<?php

namespace App\Form;

use App\Entity\AvailabilityScheduleElement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvailabilityScheduleElementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            $availabilityScheduleElement = $event->getData();
            $builder = $event->getForm();
            $builder
                ->add('isAvailable', CheckboxType::class, [
                    'label' => 'date' == $options['type'] ? $availabilityScheduleElement->getDate()->format('d/m/y') : $availabilityScheduleElement->getAvailabilitySchedule()->getMember(),
                    'required' => false,
                ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AvailabilityScheduleElement::class,
            'type' => 'date',
        ]);
    }
}

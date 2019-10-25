<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CommandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $baskets = $event->getData();
            $builder = $event->getForm();
            foreach ($baskets as $key => $basket) {
                $builder
                    ->add('basket_'.$key, BasketType::class, [
                        'label' => false,
                        'data' => $basket,
                    ]);
            }
        });
        $builder->add('submit', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn-success btn-block']]);
    }
}

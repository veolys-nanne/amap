<?php
namespace App\Form;

use App\Entity\ProductQuantity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductInModelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $productQuantity = $event->getData();
            $builder = $event->getForm();
            $builder
                ->add('active', CheckboxType::class, [
                    'label' => $productQuantity->getProduct()->getName(),
                    'value' => $productQuantity->getProduct()->getId(),
                    'mapped' => false,
                    'required' => false,
                    'data' => $productQuantity->getQuantity() ? true : false,
                ])
            ;
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductQuantity::class,
        ]);
    }
}

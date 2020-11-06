<?php

namespace App\Form;

use App\Entity\ProductQuantity;
use App\Repository\ProductQuantityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductQuantityType extends AbstractType
{
    protected $quantityRepository;

    public function __construct(ProductQuantityRepository $quantityRepository)
    {
        $this->quantityRepository = $quantityRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('quantity', IntegerType::class, [
            'attr' => [
                'style' => 'width: 80px',
                'min' => 0,
            ],
        ]);
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $productQuantity = $event->getData();
            $builder = $event->getForm();
            $stock = $productQuantity->getProduct()->getStock();
            if (null != $stock) {
                $stock -= $this->quantityRepository->findSumByModelAndProduct($productQuantity);
                if ($stock > 0) {
                    $builder->add('quantity', ChoiceType::class, [
                        'choices' => range(0, $stock, 1),
                        'attr' => ['style' => 'width: 80px', 'min' => 0],
                    ]);
                } else {
                    $builder->add('quantity', IntegerType::class, [
                        'attr' => [
                            'style' => 'width: 80px',
                            'min' => 0,
                        ],
                        'disabled' => true,
                    ]);
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductQuantity::class,
        ]);
    }
}

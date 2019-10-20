<?php
namespace App\Form;

use App\Entity\Basket;
use App\EntityManager\ProductManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModelType extends AbstractType
{
    protected $productManager;

    public function __construct(ProductManager $productManager)
    {
        $this->productManager = $productManager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, [
                'label' => 'Date de la commande',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => ['autocomplete' => 'off', 'class' => 'date-picker'],
            ])
            ->add('productQuantityCollection', CollectionType::class, [
                    'label' => false,
                    'entry_type' => ProductInModelType::class,
                    'entry_options' => [
                        'label' => false,
                    ],
            ])
            ->add('submit', SubmitType::class, ['label'=>'Envoyer', 'attr'=>['class'=>'btn-success btn-block']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Basket::class,
        ]);
    }
}

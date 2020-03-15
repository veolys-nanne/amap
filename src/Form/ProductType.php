<?php

namespace App\Form;

use App\Entity\Basket;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (in_array('ROLE_ADMIN', $options['user']->getRoles()) || in_array('ROLE_REFERENT', $options['user']->getRoles())) {
            $builder
                ->add('producer', EntityType::class, [
                    'class' => User::class,
                    'query_builder' => function (ServiceEntityRepository $entityRepository) use ($options) {
                        return $entityRepository->getQueryBuilderForFindByRole('ROLE_PRODUCER', $options['user']);
                    },
                    'label' => 'Producteur',
                ])
                ->add('active', CheckboxType::class, ['label' => 'Actif']);
        }
        $builder
            ->add('name', TextType::class, ['label' => 'Dénomination'])
            ->add('stock', TextType::class, ['label' => 'Stock disponible par livraison', 'required' => false]);
        if (in_array('ROLE_PRODUCER', $options['user']->getRoles())) {
            $builder->add('portfolio', PortfolioType::class, ['label' => false, 'required' => false]);
        }
        $builder->add('submit', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn-success btn-block']]);
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            $product = $event->getData();
            $builder = $event->getForm();
            $frozen = $product->getId() && $product->isActive() && !in_array('ROLE_ADMIN', $options['user']->getRoles()) && !in_array('ROLE_REFERENT', $options['user']->getRoles());
            $active = false;
            if ($frozen) {
                $active = $this->entityManager->getRepository(Basket::class)->isProductInActiveBasket($product);
            }
            $builder->add('price', NumberType::class, [
                'label' => 'Prix',
                'attr' => [
                    'min' => 0,
                    'frozen' => $frozen,
                ],
                'disabled' => $frozen,
                'label_attr' => [
                    'class' => $active ? 'fas fa-exclamation-triangle text-danger' : '',
                    'title' => $active ? 'Ce produit est présent dans une commande en cours' : '',
                ],
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'user',
        ]);
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}

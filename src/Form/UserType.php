<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('lastname', TextType::class, ['label' => 'Nom'])
            ->add('firstname', TextType::class, ['label' => 'Prénom', 'required' => false]);
        if ('admin' == $options['role']) {
            $builder->add('roles', ChoiceType::class, [
                'label' => 'Role',
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'administrateur/trice' => 'ROLE_ADMIN',
                    'référent/e' => 'ROLE_REFERENT',
                    'producteur/trice' => 'ROLE_PRODUCER',
                    'consom\'acteur/trice' => 'ROLE_MEMBER',
                ],
            ]);
        }
        if ('referent' == $options['role'] && !$options['isAccount']) {
            $builder->add('roles', ChoiceType::class, [
                'label' => 'Role',
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'référent/e' => 'ROLE_REFERENT',
                    'producteur/trice' => 'ROLE_PRODUCER',
                    'consom\'acteur/trice' => 'ROLE_MEMBER',
                ],
            ]);
        }
        if ('admin' == $options['role'] && 'producer' == $options['type']) {
            $builder->add('parent', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (ServiceEntityRepository $entityRepository) {
                    return $entityRepository->getQueryBuilderForFindByRole('ROLE_REFERENT');
                },
                'label' => 'Référent',
            ]);
        }
        if ('producer' == $options['role'] || 'producer' == $options['type']) {
            $builder
                ->add('color', ColorType::class, ['label' => 'Couleur', 'required' => false])
                ->add('denomination', TextType::class, ['label' => 'Dénomination', 'required' => false])
                ->add('payto', TextType::class, ['label' => 'Ordre chèque', 'required' => false])
                ->add('deleveries', ChoiceType::class, [
                    'label' => 'Semaine de livraison',
                    'required' => false,
                    'multiple' => true,
                    'expanded' => true,
                    'choices' => [
                        'S1' => 1,
                        'S2' => 2,
                        'S3' => 3,
                        'S4' => 4,
                        'S5' => 5,
                ], ])
                ->add('portfolio', PortfolioType::class, ['label' => false, 'required' => false]);
        }
        $builder
            ->add('address', TextType::class, ['label' => 'Adresse', 'required' => false])
            ->add('city', TextType::class, ['label' => 'Ville', 'required' => false])
            ->add('zipCode', IntegerType::class, ['label' => 'Code postal', 'required' => false])
            ->add('phoneNumbers', CollectionType::class, [
                'label' => 'Numéros de téléphone',
                'entry_type' => TelType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'attr' => [
                    'class' => 'collection',
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ])
            ->add('broadcastList', CollectionType::class, [
                'label' => 'Liste de diffusion',
                'entry_type' => EmailType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'attr' => [
                    'class' => 'collection',
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ])
            ->add('submit', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn-success btn-block']])
        ;
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            $user = $event->getData();
            $builder = $event->getForm();
            if (!$user->getId()) {
                $builder->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => ['label' => 'Mot de passe'],
                    'second_options' => ['label' => 'Confirmation du mot de passe'],
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'role',
            'type',
            'isAccount',
        ]);
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['registration'],
        ]);
    }
}

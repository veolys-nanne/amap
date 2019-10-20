<?php
namespace App\Form;

use App\Entity\Credit;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

class CreditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('member', EntityType::class, [
            'class' => User::class,
            'query_builder' => function (ServiceEntityRepository $entityRepository) {
                return $entityRepository->getQueryBuilderForFindByRole('ROLE_MEMBER');
            },
            'label'=>'Consom\'acteur/trice'
        ]);
        if (in_array('ROLE_ADMIN', $options['user']->getRoles()) || in_array('ROLE_REFERENT', $options['user']->getRoles())) {
            $builder
                ->add('producer', EntityType::class, [
                    'class' => User::class,
                    'query_builder' => function (ServiceEntityRepository $entityRepository) use ($options) {
                        return $entityRepository->getQueryBuilderForFindByRole('ROLE_PRODUCER', $options['user']);
                    },
                    'label'=>'Producteur/trice'
                ])
                ->add('active', CheckboxType::class, ['label'=>'Actif']);
        }
        $builder
            ->add('object', TextType::class, ['label' => 'Object'])
            ->add('submit', SubmitType::class, ['label'=>'Envoyer', 'attr'=>['class'=>'btn-success btn-block']])
        ;
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options){
            $credit = $event->getData();
            $builder = $event->getForm();
            $mailOptions = [];
            if ($options['disabled'] && !in_array('ROLE_ADMIN', $options['user']->getRoles()) && !in_array('ROLE_REFERENT', $options['user']->getRoles())) {
                $mailOptions = ['mail' => true];
            }
            if ($credit->getTotalAmount() != $credit->getCurrentAmount()) {
                $builder
                    ->add('totalAmount', NumberType::class, [
                        'label' => 'Montant',
                        'disabled' => true,
                        'attr' => ['min' => 0],
                    ])
                    ->add('currentAmount', NumberType::class, [
                        'label' => 'Montant restant',
                        'attr' => array_merge(['min' => 0], $mailOptions),
                    ]);
            } else {
                $builder
                    ->add('totalAmount', NumberType::class, [
                        'label' => 'Montant',
                        'attr' => array_merge(['min' => 0], $mailOptions),
                    ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Credit::class,
            'user' => false
        ]);
    }
}

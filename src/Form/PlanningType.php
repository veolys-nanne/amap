<?php

namespace App\Form;

use App\Entity\AvailabilitySchedule;
use App\Entity\Planning;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;

class PlanningType extends AbstractType
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('elements', CollectionType::class, [
                'label' => 'Dates',
                'entry_type' => PlanningElementType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'error_bubbling' => false,
                'constraints' => [
                    new Count(['min' => 1]),
                ],
            ])
            ->add('submit', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn-success btn-block']])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $planning = $event->getData();
            $builder = $event->getForm();
            $members = new ArrayCollection();
            if ($planning->getId()) {
                $availabilitySchedules = $this->entityManager->getRepository(AvailabilitySchedule::class)->findByPlanning($planning);
                foreach ($availabilitySchedules as $availabilitySchedule) {
                    $members->add($availabilitySchedule->getMember());
                }
            } else {
                $members = $this->entityManager->getRepository(User::class)->findByRoleAndActive('ROLE_MEMBER');
            }
            $builder
                ->add('members', EntityType::class, [
                    'class' => User::class,
                    'query_builder' => function (ServiceEntityRepository $entityRepository) {
                        return $entityRepository->getQueryBuilderForFindByRoleAndActive('ROLE_MEMBER');
                    },
                    'label' => 'Consom\'acteur/trice',
                    'mapped' => false,
                    'multiple' => true,
                    'expanded' => true,
                    'data' => $members,
                    'constraints' => [
                        new Count(['min' => 1]),
                    ],
                ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Planning::class,
        ]);
    }
}

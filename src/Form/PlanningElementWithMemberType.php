<?php
namespace App\Form;

use App\Entity\AvailabilityScheduleElement;
use App\Entity\PlanningElement;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanningElementWithMemberType extends AbstractType
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            $planningElement = $event->getData();
            $builder = $event->getForm();
            if ($planningElement->getId()) {
                $availabilityScheduleElements = $this->entityManager->getRepository(AvailabilityScheduleElement::class)->findByPlanningAndDate($planningElement->getPlanning(), $planningElement->getDate());
                $membersInfo = [];
                foreach ($availabilityScheduleElements as $availabilityScheduleElement) {
                    $member = $availabilityScheduleElement->getAvailabilitySchedule()->getMember();
                    $membersInfo[] = [
                        'member' => $member,
                        'name' => $member->__toString(),
                        'isAvailable' => $availabilityScheduleElement->getIsAvailable()
                    ];
                }
            }
            $unavailableMembers = array_filter($membersInfo, function ($user) {
                return !$user['isAvailable'];
            });
            $isUnavailable = count($unavailableMembers);
            $unavailableMembersListing = implode(', ', array_column($unavailableMembers, 'name'));
            $builder
                ->add('members', EntityType::class, [
                    'class' => User::class,
                    'choices' => array_column($membersInfo, 'member'),
                    'choice_attr' => function($choiceValue, $key, $value) use ($membersInfo) {
                        return !$membersInfo[$key]['isAvailable'] ? [
                            'class' => 'fas text-danger',
                            'data-icon' => ' &#xf071;',
                            'data-disabled' => true,
                            'data-id' => $membersInfo[$key]['member']->getId(),
                        ] : ['class' => 'fas'];
                    },
                    'attr' => ['class' => 'fas'],
                    'label_attr' => [
                        'class' => $isUnavailable ? 'fas fa-exclamation-triangle text-danger' : '',
                        'title' => $unavailableMembersListing,
                        'data-date' => $planningElement->getDate()->format('Y-m-d'),
                    ],
                    'label'=> $planningElement->getDate()->format('d/m/Y'),
                    'multiple' => true,
                    'required' => false,
                ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PlanningElement::class,
        ]);
    }
}

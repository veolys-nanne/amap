<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ContactType extends AbstractType
{
    const ADMIN = 'admin';
    const ALL = 'all';
    const ALL_MEMBER = 'allMember';
    const ALL_PRODUCER = 'allProducer';
    const ALL_REFERENT = 'allReferent';
    const MY_PRODUCERS = 'myProducers';

    protected $entityManager;
    protected $security;
    protected $to = null;
    protected $choices = [];

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->security->getUser();
        $roles = $user->getRoles();

        $this->choices['Liste de diffusion']['Les administrateurs/trices'] = self::ADMIN;
        $this->choices['Liste de diffusion']['Les consom\'acteurs/trices'] = self::ALL_MEMBER;
        if (in_array('ROLE_ADMIN', $roles)) {
            $this->choices['Liste de diffusion']['Les producteurs/trices'] = self::ALL_PRODUCER;
            $this->choices['Liste de diffusion']['Les référents/es'] = self::ALL_REFERENT;
            $this->choices['Liste de diffusion']['Tous'] = self::ALL;
            foreach ($this->entityManager->getRepository(User::class)->findByRoleAndActive('ROLE_PRODUCER') as $producer) {
                $this->retrieveTo('Producteur/trice', $producer, 'p_'.$producer->getId(), $options['user']);
            }
            foreach ($this->entityManager->getRepository(User::class)->findByRoleAndActive('ROLE_REFERENT') as $referent) {
                $this->retrieveTo('Référent/e', $referent, 'r_'.$referent->getId(), $options['user']);
            }
        }
        if (in_array('ROLE_REFERENT', $roles)) {
            $this->choices['Liste de diffusion']['Mes producteurs/trices'] = self::MY_PRODUCERS;
            $this->choices['Liste de diffusion']['Les référents/es'] = self::ALL_REFERENT;
            foreach ($this->entityManager->getRepository(User::class)->findByRoleAndActive('ROLE_PRODUCER', $user) as $producer) {
                $this->retrieveTo('Mon/ma Producteur/trice', $producer, 'mp_'.$producer->getId(), $options['user']);
            }
        }
        if (in_array('ROLE_PRODUCER', $roles)) {
            $referent = $user->getParent();
            $this->retrieveTo('Mon/ma référent/e', $referent, 'myp_'.$referent->getId(), $options['user']);
        }
        if (in_array('ROLE_MEMBER', $roles)) {
            foreach ($this->entityManager->getRepository(User::class)->findByRoleAndActive('ROLE_PRODUCER') as $producer) {
                $parent = $producer->getParent();
                $this->retrieveTo('Producteur/trice (référent)', $producer, 'pr'.$producer->getId().'_'.$parent->getId(), $options['user']);
            }
        }
        foreach ($this->entityManager->getRepository(User::class)->findByRoleAndActive('ROLE_MEMBER') as $member) {
            $this->retrieveTo('Consomacteur/trice', $member, 'm_'.$member->getId(), $options['user']);
        }

        $builder
            ->add('subject', TextType::class, [
                'label' => 'Objet',
            ])
            ->add('to', ChoiceType::class, [
                'label' => 'À',
                'choices' => $this->choices,
                'data' => [$this->to],
                'multiple' => true,
            ])
            ->add('email', FormatContactType::class, ['label' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'user' => null,
        ]);
    }

    protected function retrieveTo(string $label, User $entry, string $value, User $user = null)
    {
        $this->choices[$label][$entry->__toString()] = $value;
        if ($user instanceof User && $user->getId() == $entry->getId()) {
            $this->to = $this->to ?? $value;
        }
    }
}

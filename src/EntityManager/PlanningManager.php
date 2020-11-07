<?php

namespace App\EntityManager;

use App\Entity\Planning;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

class PlanningManager
{
    const LABELS = [
        Planning::STATE_INACTIVE => 'Paramétrage',
        Planning::STATE_OPEN => 'Saisie des disponibilités',
        Planning::STATE_CLOSE => 'Création du planning définitif',
        Planning::STATE_ONLINE => 'En ligne',
    ];
    const TRANSITIONS = [
        Planning::STATE_INACTIVE => Planning::STATE_OPEN,
        Planning::STATE_OPEN => Planning::STATE_CLOSE,
        Planning::STATE_CLOSE => Planning::STATE_ONLINE,
    ];
    const MAIL = [
        Planning::STATE_OPEN => [
            'template' => 'emails/availabilities',
            'subject' => 'Disponibilités pour le planning des permanences AMAP Hommes de terre',
        ],
        Planning::STATE_ONLINE => [
            'template' => 'emails/planning',
            'subject' => 'Planning des permanences AMAP Hommes de terre',
        ],
    ];

    protected $entityManager;
    protected $router;

    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function getNextStateMail(Planning $planning): ?array
    {
        $nextState = self::TRANSITIONS[$planning->getState()] ?? false;

        $parameters = null;
        if ($nextState && (self::MAIL[$nextState] ?? false)) {
            $parameters = [
                'list' => $this->entityManager->getRepository(User::class)->findByRoleAndActive('ROLE_MEMBER'),
                'subject' => self::MAIL[$nextState]['subject'],
                'template' => self::MAIL[$nextState]['template'],
                'mailOptions' => ['period' => $this->entityManager->getRepository(Planning::class)->findPeriodByPlanning($planning)[0]],
                'callback' => urlencode($this->router->generate('planning_state', ['state' => $nextState, 'id' => $planning->getId()])),
            ];
        }

        return $parameters;
    }

    public function getUpMail(Planning $planning): ?array
    {
        $parameters = null;
        if (Planning::STATE_OPEN === $planning->getState()) {
            $parameters = [
                'list' => $this->entityManager->getRepository(User::class)->findByRoleAndActive('ROLE_MEMBER'),
                'subject' => 'Relance Disponibilité AMAP hommes de terre',
                'template' => 'emails/upplanning',
                'mailOptions' => ['period' => $this->entityManager->getRepository(Planning::class)->findPeriodByPlanning($planning)[0]],
            ];
        }

        return $parameters;
    }
}

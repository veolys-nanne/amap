<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NavbarTwigExtension extends AbstractExtension
{
    protected $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('needNavbar', [$this, 'needNavbar']),
        ];
    }

    public function needNavbar()
    {
        $needNavbar = true === $this->session->get('needNavbar');
        $this->session->set('needNavbar', false);

        return $needNavbar;
    }
}

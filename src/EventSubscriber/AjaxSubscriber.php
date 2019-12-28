<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class AjaxSubscriber implements EventSubscriberInterface
{
    protected $twig;
    protected $router;
    protected $requestStack;
    protected $httpKernel;

    public function __construct(Environment $twig, RouterInterface $router, Security $security, SessionInterface $session)
    {
        $this->twig = $twig;
        $this->router = $router;
        $this->security = $security;
        $this->session = $session;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        $controller = $request->attributes->get('_controller');
        $routeName = array_key_first(array_filter($this->router->getRouteCollection()->all(), function ($route) use ($controller) {
            return $route->getDefault('_controller') == $controller;
        }));
        if (HttpKernelInterface::MASTER_REQUEST == $event->getRequestType()) {
            $roles = $this->security->getUser() ? $this->security->getUser()->getRoles() : [];
            $this->twig->addGlobal('needNavbar', $this->session->get('roles') !== $roles);
            $this->session->set('roles', $roles);
        }
        $this->twig->addGlobal('url', preg_replace('/\?.*/', '', $this->router->generate($routeName, $event->getRequest()->attributes->all())));
        $this->twig->addGlobal('base', 'base.html.twig');
        if ($event->getRequest()->isXmlHttpRequest()) {
            $this->twig->addGlobal('base', 'ajax.html.twig');
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}

<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class AjaxSubscriber implements EventSubscriberInterface
{
    protected $twig;
    protected $router;
    protected $requestStack;

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
        $url = '';
        $routeName = '';
        if ($request->attributes->has('_route')) {
            $routeName = $request->attributes->get('_route');
            if ($request->attributes->has('_route_params')) {
                $url = $this->router->generate($routeName, $request->attributes->get('_route_params'));
            }
        }
        if (empty($url)) {
            if (empty($routeName)) {
                $controller = $request->attributes->get('_controller');
                $routeName = array_key_first(array_filter($this->router->getRouteCollection()->all(), function ($route) use ($controller) {
                    return $route->getDefault('_controller') == $controller;
                }));
            }
            if (!empty($routeName)) {
                $url = $this->router->generate($routeName, $event->getRequest()->attributes->all());
            }
        }
        if ($event->isMasterRequest()) {
            $roles = $this->security->getUser() ? $this->security->getUser()->getRoles() : [];
            if ($this->session->get('roles') !== $roles) {
                $this->session->set('needNavbar', true);
            }
            $this->session->set('roles', $roles);
        }
        $this->twig->addGlobal('url', preg_replace('/\?.*/', '', $url));
        $this->twig->addGlobal('base', $event->getRequest()->isXmlHttpRequest() ? 'ajax.html.twig' : 'base.html.twig');
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}

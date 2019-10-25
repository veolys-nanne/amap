<?php
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class AjaxSubscriber implements EventSubscriberInterface
{
    protected $twig;
    protected $router;
    protected $requestStack;
    protected $httpKernel;

    public function __construct(Environment $twig, RouterInterface $router, RequestStack $requestStack, HttpKernelInterface $httpKernel)
    {
        $this->twig = $twig;
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->httpKernel = $httpKernel;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $route = $event->getRequest()->attributes->all();
        if (array_key_exists('_route', $route) && 'home' == $route['_route']) {
            $this->twig->addGlobal('needNavbar', true);
        }
        $this->twig->addGlobal('url', $this->router->generate($route['_route'], array_diff_key($route, ['_route' => '', '_route_params' => '', '_firewall_context' => '','_controller' => ''])));
        $this->twig->addGlobal('base', 'base.html.twig');
        if ($event->getRequest()->isXmlHttpRequest()) {
            $this->twig->addGlobal('base', 'ajax.html.twig');
        };
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if ($event->getRequest()->isXmlHttpRequest() && $event->getResponse()->getStatusCode() == Response::HTTP_FOUND) {
            $urlInfos = parse_url($event->getResponse()->getTargetUrl());
            $route = $this->router->match($urlInfos['path']);
            $request = $this->requestStack->getCurrentRequest();
            $subRequest = $request->duplicate([], null, $route);
            $response = $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
            $event->setResponse($response);
        };
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}
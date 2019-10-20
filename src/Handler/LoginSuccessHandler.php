<?php

namespace App\Handler;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{

    protected $router;
    protected $tokenStorage;

    public function __construct(RouterInterface $router, TokenStorageInterface $tokenStorage)
    {
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        if ($user->isNew()) {
            $role = 'member';
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                $role = 'admin';
            }
            elseif (in_array('ROLE_REFERENT', $user->getRoles())) {
                $role = 'referent';
            }
            elseif (in_array('ROLE_PRODUCER', $user->getRoles())) {
                $role = 'producer';
            }

            return new RedirectResponse($this->router->generate('user_profil_password', ['role' => $role]));
        }

        return new RedirectResponse(empty($request->getSession()->get('_security.main.target_path')) ? $this->router->generate('home') : $request->getSession()->get('_security.main.target_path'));
    }

}
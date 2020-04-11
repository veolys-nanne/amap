<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(RouterInterface $router)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('login');
        }

        return $this->forward('App\Controller\DocumentController::documentViewAction', ['name' => 'homepage']);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->forward('App\Controller\SecurityController::login');
        }
        $roles = $this->getUser()->getRoles();
        if (in_array('ROLE_ADMIN', $roles)) {
            return $this->forward('App\Controller\DocumentController::documentViewAction', [
                'role' => 'admin',
                'name' => 'homepage',
            ]);
        } elseif (in_array('ROLE_REFERENT', $roles)) {
            return $this->forward('App\Controller\DocumentController::documentViewAction', [
                'role' => 'referent',
                'name' => 'homepage',
            ]);
        } elseif (in_array('ROLE_PRODUCER', $roles)) {
            return $this->forward('App\Controller\DocumentController::documentViewAction', [
                'role' => 'producer',
                'name' => 'homepage',
            ]);
        }

        return $this->forward('App\Controller\DocumentController::documentViewAction', [
            'role' => 'member',
            'name' => 'homepage',
        ]);
    }
}

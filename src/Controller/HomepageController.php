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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $roles = $this->getUser()->getRoles();

        if (in_array('ROLE_ADMIN', $roles)) {
            return $this->redirectToRoute('document_view', [
                'role' => 'admin',
                'name' => 'homepage',
            ]);
        } elseif (in_array('ROLE_REFERENT', $roles)) {
            return $this->redirectToRoute('document_view', [
                'role' => 'referent',
                'name' => 'homepage',
            ]);
        } elseif (in_array('ROLE_PRODUCER', $roles)) {
            return $this->redirectToRoute('document_view', [
                'role' => 'producer',
                'name' => 'homepage',
            ]);
        }
        return $this->redirectToRoute('document_view', [
            'role' => 'member',
            'name' => 'homepage',
        ]);
    }
}

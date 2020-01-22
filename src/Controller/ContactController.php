<?php

namespace App\Controller;

use App\Entity\Credit;
use App\Entity\PlanningElement;
use App\Entity\Product;
use App\Entity\User;
use App\Form\ContactType;
use App\Helper\MailHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route(
     *     "/{role}/contact/form/{user}",
     *     name="contact_form",
     *     requirements={
     *      "role"="admin|referent|producer|member",
     *      "user"="\d+",
     *     },
     *     defaults={
     *      "user"=0,
     *     }
     * )
     */
    public function contactEditAction(Request $request, EntityManagerInterface $entityManager, MailHelper $mailHelper, string $role, User $user = null)
    {
        $form = $this->createForm(ContactType::class, null, [
            'user' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return new Response($mailHelper->sendMessage($entityManager, $role, $form, $this->getUser())) ??
                $this->forward('App\Controller\DocumentController::documentViewAction', [
                    'role' => $role,
                    'name' => 'homepage',
                ]);
        }

        return $this->render('contact/form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Contact',
        ]);
    }

    /**
     * @Route(
     *     "/{role}/contact/form/product/{user}/{product}",
     *     name="contact_form_product",
     *     requirements={
     *      "role"="admin|referent|producer|member",
     *      "user"="\d+",
     *      "product"="\d+",
     *     },
     *     defaults={
     *      "user"=0,
     *      "product"=0,
     *     }
     * )
     */
    public function contactEditProductAction(Request $request, EntityManagerInterface $entityManager, MailHelper $mailHelper, string $role, User $user = null, Product $product = null)
    {
        $form = $this->createForm(ContactType::class, [
            'subject' => 'Question à propos du produit "'.$product->getName().'"',
        ], [
            'user' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return new Response($mailHelper->sendMessage($entityManager, $role, $form, $this->getUser())) ??
                $this->forward('App\Controller\DocumentController::documentViewAction', [
                    'role' => $role,
                    'name' => 'homepage',
                ]);
        }

        return $this->render('contact/form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Contact',
        ]);
    }

    /**
     * @Route(
     *     "/producer/contact/form/product_price/{user}/{product}",
     *     name="contact_form_product_price",
     *     requirements={
     *      "user"="\d+",
     *      "product"="\d+",
     *     },
     *     defaults={
     *      "user"=0,
     *      "product"=0,
     *     }
     * )
     */
    public function contactEditProductPriceAction(Request $request, EntityManagerInterface $entityManager, MailHelper $mailHelper, User $user = null, Product $product = null)
    {
        $form = $this->createForm(ContactType::class, [
            'subject' => 'Demande de modification du prix du produit "'.$product->getName().'"',
            'extra' => 'Merci de modifier le prix de <a href="'.$this->generateUrl('product_form', [
                    'role' => 'referent',
                    'id' => $product->getId(),
                ]).'">'.$product->getName().'</a>.<br />Nouveau prix:',
        ], [
            'user' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return new Response($mailHelper->sendMessage($entityManager, 'producer', $form, $this->getUser())) ??
                $this->forward('App\Controller\DocumentController::documentViewAction', [
                    'role' => 'producer',
                    'name' => 'homepage',
                ]);
        }

        return $this->render('contact/form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Contact',
        ]);
    }

    /**
     * @Route(
     *     "/{role}/contact/form/planning/{user}/{planningElement}",
     *     name="contact_form_planning_element",
     *     requirements={
     *      "role"="admin|referent|producer|member",
     *      "user"="\d+",
     *      "planningElement"="\d+",
     *     },
     *     defaults={
     *      "user"=0,
     *      "planningElement"=0,
     *     }
     * )
     */
    public function contactEditPlanningElementAction(Request $request, EntityManagerInterface $entityManager, MailHelper $mailHelper, string $role, User $user = null, PlanningElement $planningElement = null)
    {
        $form = $this->createForm(ContactType::class, [
            'subject' => 'Question à propos de la permanence du '.$planningElement->getDate()->format('d/m/Y'),
        ], [
            'user' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return new Response($mailHelper->sendMessage($entityManager, $role, $form, $this->getUser())) ??
                $this->forward('App\Controller\DocumentController::documentViewAction', [
                    'role' => $role,
                    'name' => 'homepage',
                ]);
        }

        return $this->render('contact/form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Contact',
        ]);
    }

    /**
     * @Route(
     *     "/producer/contact/form/credit/{user}/{credit}",
     *     name="contact_form_credit",
     *     requirements={
     *      "user"="\d+",
     *      "credit"="\d+",
     *     },
     *     defaults={
     *      "user"=0,
     *      "credit"=0,
     *     }
     * )
     */
    public function contactEditCreditAction(Request $request, EntityManagerInterface $entityManager, MailHelper $mailHelper, User $user = null, Credit $credit = null)
    {
        $form = $this->createForm(ContactType::class, [
            'subject' => 'Demande de modification du montant d\'un avoir (identifiant:'.$credit->getId().')',
            'extra' => 'Merci de modifier le montant de cet <a href="'.$this->generateUrl('credit_form', [
                    'role' => 'referent',
                    'id' => $credit->getId(),
                ]).'">avoir</a>.<br />Nouveau montant:',
        ], [
            'user' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return new Response($mailHelper->sendMessage($entityManager, 'producer', $form, $this->getUser())) ??
                $this->forward('App\Controller\DocumentController::documentViewAction', [
                    'role' => 'producer',
                    'name' => 'homepage',
                ]);
        }

        return $this->render('contact/form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Contact',
        ]);
    }

    /**
     * @Route(
     *     "/preview",
     *     name="preview"
     * )
     */
    public function previewAction(Request $request, MailHelper $mailHelper)
    {
        if ($request->request->has('preview_emails')) {
            $messages = $request->request->get('preview_emails')['messages'] ?? [];
            foreach ($messages as &$message) {
                $message = $mailHelper->createMessageFromArray($message);
            }
        }
        $mailHelper->sendMessages($messages);

        return $this->redirect(filter_var($request->headers->get('referer'), FILTER_SANITIZE_URL));
    }
}

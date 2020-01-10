<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\User;
use App\EntityManager\CreditManager;
use App\EntityManager\ProductManager;
use App\Form\BillFilterType;
use App\Form\CommandType;
use App\Form\FormatEmailType;
use App\Form\ModelType;
use App\Form\SynthesesType;
use App\EntityManager\BasketManager;
use App\Helper\MailHelper;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BasketController extends AbstractController
{
    /**
     * @Route(
     *     "/{role}/basket/view",
     *     name="basket_view",
     *     requirements={"role"="admin|referent|producer|member"},
     * )
     */
    public function basketViewAction(Request $request, BasketManager $basketManager, ProductManager $productManager, EntityManagerInterface $entityManager, Pdf $knpSnappy, ParameterBagInterface $parameterBag)
    {
        $form = $this->createForm(BillFilterType::class);
        $form->handleRequest($request);
        $baskets = [];
        if ($form->isSubmitted() && $form->isValid() && null !== $form->get('start')->getData() && null !== $form->get('end')->getData()) {
            $baskets = $entityManager->getRepository(Basket::class)->findByUserAndDate($this->getUser(), $form->get('start')->getData(), $form->get('end')->getData());
        } else {
            $modelBaskets = $entityManager->getRepository(Basket::class)->findByFrozenAndModel(0);
            foreach ($modelBaskets as $modelBasket) {
                $baskets[] = $entityManager->getRepository(Basket::class)->findOneByParentAndUser($modelBasket, $this->getUser()) ?? $basketManager->createBasket($modelBasket);
            }
        }

        $products = $productManager->getProductsFromBaskets($baskets);
        $productManager->orderProducts($products);
        $isPdf = $form->has('pdf') && $form->get('pdf')->getData();
        $html = $this->renderView('basket/view.html.twig', [
            'title' => 'Mes commandes',
            'form' => $form->createView(),
            'baskets' => $baskets,
            'products' => $products,
            'isPdf' => $isPdf,
        ]);
        if ($isPdf) {
            $filename = 'mes_commandes';
            if (count($baskets) > 0) {
                $maxDate = max(array_map(function (Basket $basket) {
                    return $basket->getDate();
                }, $baskets));
                $minDate = min(array_map(function (Basket $basket) {
                    return $basket->getDate();
                }, $baskets));
                $filename .= '_'.$minDate->format('dmY').'_'.$maxDate->format('dmY');
            }

            return new PdfResponse(
                $knpSnappy->getOutputFromHtml($html, ['user-style-sheet' => $parameterBag->get('kernel.project_dir').'/public/assets/css/pdf-color-page-break.css']),
                $filename.'.pdf'
            );
        }

        return new Response($html);
    }

    /**
     * @Route(
     *     "/{role}/basket/form",
     *     name="basket_form",
     *     requirements={"role"="admin|referent|producer|member"},
     * )
     */
    public function basketEditAction(Request $request, BasketManager $basketManager, ProductManager $productManager, EntityManagerInterface $entityManager, string $role)
    {
        $modelBaskets = $entityManager->getRepository(Basket::class)->findByFrozenAndModel(0);
        $baskets = [];
        $isNew = true;
        foreach ($modelBaskets as $modelBasket) {
            $basket = $entityManager->getRepository(Basket::class)->findOneByParentAndUser($modelBasket, $this->getUser());
            $isNew = $isNew && (null == $basket);
            $baskets[] = $basket ?? $basketManager->createBasket($modelBasket);
        }
        $products = $productManager->getProductsFromBaskets($baskets, -1);
        $productManager->orderProducts($products);

        $form = $this->createForm(CommandType::class, $baskets);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $key = 0;
            while ($form->has('basket_'.$key)) {
                $entityManager->persist($form->get('basket_'.$key)->getData());
                ++$key;
            }
            $entityManager->flush();
            $this->addFlash('success', 'Le panier a été mis à jour.');

            return $this->forward('App\Controller\BasketController::basketViewAction', ['role' => $role]);
        }

        return $this->render('basket/form.html.twig', [
            'title' => 'Mise à jour panier',
            'form' => $form->createView(),
            'role' => $role,
            'products' => $products,
            'isNew' => $isNew,
        ]);
    }

    /**
     * @Route(
     *     "/admin/basket/models",
     *     name="basket_models",
     * )
     */
    public function modelListingAction(Request $request, EntityManagerInterface $entityManager, MailHelper $mailHelper)
    {
        $baskets = $entityManager->getRepository(Basket::class)->findModel();
        $openModels = $entityManager->getRepository(Basket::class)->findByFrozenAndModel(0);
        $list = $entityManager->getRepository(User::class)->countBasketByUser($openModels);
        $getOpenModels = count($openModels) > 0;
        $options = [
            'baskets' => $baskets,
            'title' => 'Modèles de panier',
        ];
        if ($getOpenModels && count($list) > 0) {
            $builder = $this->get('form.factory')
                ->createNamedBuilder('formMailUp');
            $formMailUp = $builder
                ->add('email', FormatEmailType::class, ['label' => false, 'submitLabel' => 'Envoyer un mail de relance pour la commande', 'builder' => $builder])
                ->getForm();
            $formMailUp->handleRequest($request);
            $isEmail = $formMailUp->has('email') && $formMailUp->get('email')->get('email')->isClicked();
            $isPreview = $formMailUp->has('email') && $formMailUp->get('email')->get('preview')->isClicked();
            if ($isEmail || $isPreview) {
                $messages = [];
                if (count($list) > 0) {
                    $message = $mailHelper->getMailForList('Relance commande AMAP hommes de terre', $list);
                    if (null !== $message) {
                        $message
                            ->setBody(
                                $this->renderView('emails/upcommande.html.twig', [
                                    'message' => $message,
                                    'extra' => $request->request->has('extra') ? $request->request->get('extra') : '',
                                ]),
                                'text/html'
                            )
                            ->addPart(
                                $this->renderView('emails/upcommande.txt.twig', [
                                    'message' => $message,
                                ]),
                                'text/plain'
                            );
                        $messages[] = $message;
                    }
                }
                if (!$isPreview) {
                    $mailHelper->sendMessages($messages);
                }
            }
            $options['formMailUp'] = $formMailUp->createView();
            $options['messages'] = $isPreview ? $messages : null;
        }
        if ($getOpenModels) {
            $builder = $this->get('form.factory')
                ->createNamedBuilder('formMailInfo');
            $formMailInfo = $builder
                ->add('email', FormatEmailType::class, ['label' => false, 'submitLabel' => 'Envoyer un mail de mise à disposition des paniers', 'builder' => $builder])
                ->getForm();
            $formMailInfo->handleRequest($request);
            $isEmail = $formMailInfo->has('email') && $formMailInfo->get('email')->get('email')->isClicked();
            $isPreview = $formMailInfo->has('email') && $formMailInfo->get('email')->get('preview')->isClicked();
            if ($isEmail || $isPreview) {
                $messages = [];
                $message = $mailHelper->getMailForMembers('Commande AMAP Hommes de terre');
                if (null !== $message) {
                    $message
                        ->setBody(
                            $this->renderView('emails/infocommande.html.twig', [
                                'message' => $message,
                                'baskets' => $openModels,
                                'extra' => $request->request->has('extra') ? $request->request->get('extra') : '',
                            ]),
                            'text/html'
                        )
                        ->addPart(
                            $this->renderView('emails/infocommande.txt.twig', [
                                'message' => $message,
                                'baskets' => $openModels,
                                'extra' => $request->request->has('extra') ? $request->request->get('extra') : '',
                            ]),
                            'text/plain'
                        );
                    $messages[] = $message;
                }
                if (!$isPreview) {
                    $mailHelper->sendMessages($messages);
                }
            }
            $options['formMailInfo'] = $formMailInfo->createView();
            $options['messages'] = $isPreview ? $messages : null;
        }

        return $this->render('basket/models.html.twig', $options);
    }

    /**
     * @Route(
     *     "/admin/basket/model/{id}",
     *     name="basket_model",
     *     requirements={"id"="\d+"},
     *     defaults={"id"=0}
     * )
     */
    public function modelEditAction(Request $request, BasketManager $basketManager, ProductManager $productManager, EntityManagerInterface $entityManager, Basket $basket = null)
    {
        $producers = $entityManager->getRepository(User::class)->findByDeleveries();
        $isNew = $basket ? false : true;
        $basket = $basket ?? $basketManager->createModel();
        $models = array_diff($entityManager->getRepository(Basket::class)->findByFrozenAndModel(0), [$basket]);

        $products = $productManager->getProductsFromBaskets($models, -1);
        $products = $productManager->getProductsFromBaskets([$basket], -1, $products);
        $productManager->orderProducts($products);

        $form = $this->createForm(ModelType::class, $basket, [
            'disabled' => $basket->isFrozen(),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $previousProducts = $basketManager->getSelectedProductsList($basket);
            foreach ($form->get('productQuantityCollection') as $key => $child) {
                $productQuantity = $child->getData();
                $productQuantity->setQuantity($child->get('active')->getData() ? 1 : 0);
                $entityManager->persist($productQuantity);
            }
            $basketManager->changeModel($basket, $previousProducts);
            $entityManager->persist($basket);
            $entityManager->flush();
            $this->addFlash('success', $isNew ? 'Le modèle a été créé.' : 'Le modèle a été mis à jour.');

            return $this->forward('App\Controller\BasketController::modelListingAction');
        }

        return $this->render('basket/model.html.twig', [
            'title' => 'Mise à jour modèle de panier',
            'form' => $form->createView(),
            'models' => $models,
            'products' => $products,
            'producers' => $producers,
        ]);
    }

    /**
     * @Route(
     *     "/admin/syntheses/basket",
     *     name="basket_syntheses",
     * )
     */
    public function synthesesAction(Request $request, MailHelper $mailHelper, BasketManager $basketManager, CreditManager $creditManager, EntityManagerInterface $entityManager, Pdf $knpSnappy, ParameterBagInterface $parameterBag)
    {
        $type = $request->request->has('syntheses') ? $request->request->get('syntheses')['type'] : null;
        $form = $this->createForm(SynthesesType::class, null, [
            'type' => $type,
        ]);
        $form->handleRequest($request);
        $type = $form->has('type') ? $form->get('type')->getData() : '';
        if ($form->isSubmitted() && $form->isValid()) {
            $isEmail = $form->has('email') && $form->get('email')->get('email')->isClicked();
            $isPdf = $form->has('pdf') && $form->get('pdf')->isClicked();
            $isCredit = $form->has('submitCredit') && $form->get('submitCredit')->isClicked();
            $isPreview = $form->has('email') && $form->get('email')->get('preview')->isClicked();
            list($tables, $parameters) = $basketManager->generateSyntheses($form);
            if ($isCredit) {
                $creditManager->generateCredit(
                    $form->has('date') && null != $form->get('date')->getData() ? $form->get('date')->getData() : $form->get('start')->getData(),
                    $form->has('date') && null != $form->get('date')->getData() ? clone $form->get('date')->getData() : $form->get('end')->getData(),
                    $form->has('product') ? $form->get('product')->getData() : null,
                    $form->has('member') ? $form->get('member')->getData() : null,
                    $form->has('quantity') ? $form->get('quantity')->getData() : null
                );
                $entityManager->flush();

                return $this->forward('App\Controller\CreditController::creditListingAction', ['role' => 'admin']);
            }
            if ($isEmail || $isPreview) {
                $messages = [];
                $subject = 'AMAP Hommes de terre '.SynthesesType::LABELS[$type];
                foreach ($tables as $tableName => $table) {
                    $message = $mailHelper->getMailForList($subject, [$parameters[$tableName]]);
                    if (null !== $message) {
                        $message
                            ->setBody(
                                $this->renderView('basket/synthesis.html.twig', [
                                    'message' => $message,
                                    'extra' => $form->get('email')->get('extra')->getData(),
                                    'tableName' => $tableName,
                                    'table' => $table,
                                    'parameters' => $parameters[$tableName],
                                    'form' => $form->createView(),
                                    'type' => $type,
                                ]),
                                'text/html'
                            )
                            ->addPart(
                                $this->renderView('basket/synthesis.txt.twig', [
                                    'message' => $message,
                                    'extra' => $form->get('email')->get('extra')->getData(),
                                    'tableName' => $tableName,
                                    'table' => $table,
                                    'parameters' => $parameters[$tableName],
                                    'form' => $form->createView(),
                                ]),
                                'text/plain'
                            );
                        $messages[] = $message;
                    }
                }
                if (!$isPreview) {
                    $mailHelper->sendMessages($messages);
                }
            }
        }

        $html = $this->renderView('basket/syntheses.html.twig', [
            'title' => 'Extraction',
            'tables' => $tables ?? [],
            'parameters' => $parameters ?? [],
            'form' => $form->createView(),
            'type' => $type,
            'isPdf' => $isPdf,
            'messages' => $isPreview ? $messages : null,
        ]);
        if ($isPdf) {
            $css = $form->has('css') ? $form->get('css')->getData() : 'pdf-color-page-break';
            $filename = null !== $type ? SynthesesType::FILES[$type] : 'extraction';
            if ($form->has('start') && $form->get('start')->getData()) {
                $filename .= '_'.$form->get('start')->getData()->format('dmY');
            }
            if ($form->has('end') && $form->get('end')->getData()) {
                $filename .= '_'.$form->get('end')->getData()->format('dmY');
            }

            return new PdfResponse(
                $knpSnappy->getOutputFromHtml($html, ['user-style-sheet' => $parameterBag->get('kernel.project_dir').'/public/assets/css/'.$css.'.css']),
                $filename.'.pdf'
            );
        }

        return new Response($html);
    }

    /**
     * @Route(
     *     "/admin/basket/frozen/{id}",
     *     name="basket_frozen",
     * )
     */
    public function frozenAction(EntityManagerInterface $entityManager, BasketManager $basketManager, Basket $basket)
    {
        $frozen = !$basket->isFrozen();
        $basket->setFrozen($frozen);
        $basketManager->changeBasket($basket);
        $entityManager->flush();
        $this->addFlash('success', $frozen ? 'Le modèle de panier a été fermé.' : 'Le modèle de panier a été réouvert.');

        return $this->forward('App\Controller\BasketController::modelListingAction');
    }

    /**
     * @Route(
     *     "/admin/basket/delete/{id}",
     *     name="basket_delete",
     * )
     */
    public function deleteAction(EntityManagerInterface $entityManager, BasketManager $basketManager, Basket $basket)
    {
        $basketManager->setDeleted($basket);
        $entityManager->flush();
        $this->addFlash('success', 'L\'entrée du modèle de panier a été supprimée.');

        return $this->forward('App\Controller\BasketController::modelListingAction');
    }
}

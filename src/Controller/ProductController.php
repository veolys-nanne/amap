<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Entity\User;
use App\EntityManager\ProductManager;
use App\Helper\MailHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route(
     *     "/{role}/product/index",
     *     name="product_index",
     *     requirements={"role"="admin|referent|producer"}
     * )
     */
    public function productListingAction(Request $request, EntityManagerInterface $entityManager, MailHelper $mailHelper, string $role)
    {
        $roles = $this->getUser()->getRoles();
        $products = [];
        $options = [
            'role' => $role,
            'title' => 'Avoirs',
        ];
        if (in_array('ROLE_ADMIN', $roles) || in_array('ROLE_REFERENT', $roles)) {
            $producers = $entityManager->getRepository(User::class)->findByRole('ROLE_PRODUCER', $this->getUser());
            $products = $entityManager->getRepository(Product::class)->findByProducers($producers);
        } elseif (in_array('ROLE_PRODUCER', $roles)) {
            $products = $entityManager->getRepository(Product::class)->findByProducers([$this->getUser()]);
            foreach ($products as $product) {
                $mailsParameters[$product->getId()] = [
                    'list' => [$product->getProducer()->getParent()],
                    'subject' => $product->isActive() ? 'Demande de désactivation de produit AMAP hommes de terre' : 'Demande d\'activation de produit AMAP hommes de terre',
                    'template' => $product->isActive() ? 'emails/deactivateproduct' : 'emails/activateproduct',
                    'mailOptions' => [
                        'product' => $product,
                        'role' => in_array('ROLE_ADMIN', $product->getProducer()->getParent()->getRoles()) ? 'admin' : 'referent',
                    ],
                ];
            }
            $options = array_merge($options, $mailHelper->createMailForm($request, $mailsParameters));
            $options['mailsParameters'] = $mailsParameters;
        }
        $options['products'] = $products;

        return $this->render('product/index.html.twig', $options);
    }

    /**
     * @Route(
     *     "/{role}/product/form/{id}",
     *     name="product_form",
     *     requirements={"role"="admin|referent|producer", "id"="\d+"},
     *     defaults={"id"=0}
     * )
     */
    public function productEditAction(Request $request, EntityManagerInterface $entityManager, ProductManager $productManager, string $role, Product $product = null)
    {
        $isNew = $product ? false : true;
        $product = $product ?? $productManager->createProduct();
        $form = $this->createForm(ProductType::class, $product, [
            'user' => $this->getUser(),
        ]);
        $previousThumbnailCollection = [];
        if (null !== $product->getPortfolio() && null !== $product->getPortfolio()->getThumbnailCollection()) {
            $previousThumbnailCollection = $product->getPortfolio()->getThumbnailCollection()->toArray();
        }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($isNew) {
                $entityManager->persist($product);
                $productManager->changeProductActivity($product);
                $product->setOrder(($entityManager->getRepository(Product::class)->findMaxOrder($product->getProducer()) ?? 0) + 1);
            }
            $productManager->changeThumbnailCollection($product, $previousThumbnailCollection);
            $entityManager->flush();
            $this->addFlash('success', $isNew ? 'Le produit a été créé.' : 'Le produit a été mis à jour.');

            return $this->forward('App\Controller\ProductController::productListingAction', ['role' => $role]);
        }

        return $this->render('product/form.html.twig', [
            'form' => $form->createView(),
            'title' => $isNew ? 'Création produit' : 'Mise à jour produit',
        ]);
    }

    /**
     * @Route(
     *     "/{role}/product/active/{id}",
     *     name="product_active",
     *     requirements={"role"="admin|referent"}
     * )
     */
    public function activeAction(EntityManagerInterface $entityManager, ProductManager $productManager, string $role, Product $product)
    {
        $active = !$product->IsActive();

        if ($active && !$product->getProducer()->isActive()) {
            $this->addFlash('info', 'Le produit ne peut être activé si le producteur est désactivé.');
        } else {
            $product->setActive($active);
            $entityManager->persist($product);
            $productManager->changeProductActivity($product);
            $entityManager->flush();
            $this->addFlash('success', $active ? 'Le produit a été activé.' : 'Le produit a été désactivé.');
        }

        return $this->forward('App\Controller\ProductController::productListingAction', [
            'role' => $role,
        ]);
    }

    /**
     * @Route(
     *     "/{role}/product/move",
     *     name="product_move",
     *     requirements={
     *      "role"="admin|referent|producer",
     *     },
     * )
     */
    public function moveAction(Request $request, EntityManagerInterface $entityManager, string $role)
    {
        $moved = false;
        if ($request->request->has('moves')) {
            foreach ($request->request->get('moves') as $move) {
                list($originalProducerOrder, $originalProductOrder) = explode('-', $move[0]);
                $originalProducerOrder -= 100;
                $originalProductOrder -= 1000;
                list($nextProducerOrder, $nextProductOrder) = explode('-', $move[1]);
                $nextProducerOrder -= 100;
                $nextProductOrder -= 1000;
                if ($nextProducerOrder == $originalProducerOrder) {
                    $product = $entityManager->getRepository(Product::class)->findOneByOrders($originalProducerOrder, $originalProductOrder);
                    $product->setOrder($nextProductOrder);
                    $moved = true;
                }
            }
        }
        if ($moved) {
            $entityManager->flush();
            $this->addFlash('success', 'L\'ordre du produit a été modifié.');
        } else {
            $this->addFlash('info', 'Ce changement d\'ordre n\'est pas valide.');
        }

        return $this->forward('App\Controller\ProductController::productListingAction', [
            'role' => $role,
        ]);

        return new Response();
    }

    /**
     * @Route(
     *     "/admin/product/delete/{id}",
     *     name="product_delete",
     * )
     */
    public function deleteAction(EntityManagerInterface $entityManager, ProductManager $productManager, Product $product)
    {
        $productManager->setDeleted($product);
        $entityManager->flush();
        $this->addFlash('success', 'Le produit a été supprimé.');

        return $this->forward('App\Controller\ProductController::productListingAction', ['role' => 'admin']);
    }
}

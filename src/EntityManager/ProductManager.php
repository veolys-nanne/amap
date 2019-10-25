<?php

namespace App\EntityManager;

use App\Entity\Basket;
use App\Entity\Product;
use App\Entity\ProductQuantity;
use App\Entity\Thumbnail;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProductManager
{
    protected $entityManager;
    protected $tokenStorage;

    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function createProduct()
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $product = new Product();
        $product->setActive(false);
        if (in_array('ROLE_PRODUCER', $user->getRoles())) {
            $product->setProducer($user);
        }

        return $product;
    }

    public function changeProductActivity(Product $product)
    {
        if ($product->isActive()) {
            $this->activateProduct($product);
        } else {
            $this->deactivateProduct($product);
        }
    }

    public function changeThumbnailCollection(Product $product, array $previousThumbnailCollection)
    {
        $portfolio = $product->getPortfolio();
        $thumbnailCollection = new ArrayCollection();
        $nextIds = [];
        if (null !== $portfolio) {
            $thumbnailCollection = $portfolio->getThumbnailCollection();
            $nextIds = array_map(
                function (Thumbnail $thumbnail) {
                    return $thumbnail->getId();
                },
                $thumbnailCollection->toArray()
            );
        }
        foreach ($previousThumbnailCollection as $thumbnail) {
            if (!in_array($thumbnail->getId(), $nextIds)) {
                $this->entityManager->remove($thumbnail);
            }
        }
        foreach ($thumbnailCollection as $thumbnail) {
            if (null == $thumbnail->getId()) {
                $thumbnail->setPortfolio($portfolio);
                $this->entityManager->persist($thumbnail);
            }
        }

        if (0 == count($thumbnailCollection) && null !== $portfolio) {
            $product->setPortfolio(null);
            $this->entityManager->remove($portfolio);
        }
    }

    public function orderProducts(array &$products)
    {
        usort($products, function (Product $productA, Product $productB) {
            return $this->orderProduct($productA, $productB);
        });
    }

    public function orderProduct(Product $productA, Product $productB)
    {
        $producerA = $productA->getProducer();
        $producerB = $productB->getProducer();

        $value = 1;
        if ($producerA->getOrder() < $producerB->getOrder()) {
            $value = -1;
        } elseif ($producerA->getOrder() == $producerB->getOrder()) {
            if ($producerA->getLastname() < $producerB->getLastname()) {
                $value = -1;
            } elseif ($producerA->getLastname() == $producerB->getLastname()) {
                if ($productA->getOrder() < $productB->getOrder()) {
                    $value = -1;
                } elseif ($productA->getOrder() == $productB->getOrder()) {
                    if ($productA->getName() < $productB->getName()) {
                        $value = -1;
                    }
                }
            }
        }

        return $value;
    }

    public function getProductsFromBaskets(array $baskets, int $quantity = 0, array $products = [])
    {
        foreach ($baskets as $basket) {
            foreach ($basket->getProductQuantityCollection() as $productQuantity) {
                if ($productQuantity->getQuantity() > $quantity && !in_array($productQuantity->getProduct(), $products)) {
                    $products[] = $productQuantity->getProduct();
                }
            }
        }

        return $products;
    }

    public function setDeleted(Product $product)
    {
        $product->setDeleted(true);
        $product->setActive(false);
        $this->changeProductActivity($product);
    }

    protected function activateProduct(Product $product)
    {
        $baskets = $this->entityManager->getRepository(Basket::class)->findByFrozenAndModel(0);
        foreach ($baskets as $basket) {
            if (!$basket->hasProduct($product)) {
                $productQuantity = new ProductQuantity();
                $productQuantity->setProduct($product);
                $productQuantity->setBasket($basket);
                $productQuantity->setQuantity(0);
                $basket->addProductQuantity($productQuantity);
                $this->entityManager->persist($productQuantity);
                $this->entityManager->persist($basket);
            }
        }
    }

    protected function deactivateProduct(Product $product)
    {
        $baskets = $this->entityManager->getRepository(Basket::class)->findByFrozen(0);
        foreach ($baskets as $basket) {
            foreach ($basket->getProductQuantityCollection() as $productQuantity) {
                if ($productQuantity->getProduct() == $product) {
                    $this->entityManager->remove($productQuantity);
                }
            }
        }
    }
}

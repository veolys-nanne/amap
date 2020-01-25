<?php

namespace App\EntityManager;

use App\Entity\Basket;
use App\Entity\Credit;
use App\Entity\CreditBasketAmount;
use App\Entity\Product;
use App\Entity\ProductQuantity;
use App\Entity\User;
use App\Form\SynthesesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BasketManager
{
    protected $entityManager;
    protected $tokenStorage;
    protected $userManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        TokenStorageInterface $tokenStorage,
        UserManager $userManager
    ) {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->userManager = $userManager;
    }

    public function changeBasket(Basket $basket)
    {
        $this->entityManager->getRepository(ProductQuantity::class)->updateByBasket($basket);

        if (null == $basket->getParent()) {
            if ($basket->isFrozen()) {
                $this->addCredit($basket);
            } else {
                $this->entityManager->getRepository(CreditBasketAmount::class)->deleteByBasket($basket);
            }
            $this->entityManager->getRepository(Basket::class)->updateFrozenByParent($basket->isFrozen(), $basket);
        }
    }

    public function addCredit(Basket $basket)
    {
        $invoices = [];
        foreach ($this->entityManager->getRepository(Basket::class)->getInvoiceByBasket($basket) as $invoice) {
            $invoices[$invoice['member_id']][$invoice['producer_id']][$invoice['basket_id']] = $invoice['invoice'];
        }
        $credits = [];
        foreach ($this->entityManager->getRepository(Credit::class)->findByAmount() as $credit) {
            $credits[$credit['member_id']][$credit['producer_id']][$credit['credit_id']] = $credit['amount'];
        }
        if (count($credits) > 0) {
            $creditBasketAmounts = [];
            foreach ($credits as $membre => $credit) {
                foreach ($credit as $producer => $credit) {
                    foreach ($credit as $credit => $amount) {
                        if (isset($invoices[$membre][$producer])) {
                            foreach ($invoices[$membre][$producer] as $basket => $invoice) {
                                $nextInvoice = max($invoice - $amount, 0);
                                $withdrawal = $invoice - $nextInvoice;
                                $invoices[$membre][$producer][$basket] = $nextInvoice;
                                $credits[$membre][$producer][$credit] -= $withdrawal;
                                $creditBasketAmounts[$credit] = [
                                    'credit' => $credit,
                                    'basket' => $basket,
                                    'amount' => $withdrawal,
                                ];
                            }
                        }
                    }
                }
            }
            $credits = array_column(
                array_map(function ($credit) {
                    return [
                        'id' => key($credit),
                        'currentAmount' => current($credit),
                    ];
                }, call_user_func_array('array_merge', $credits)),
                null,
                'id'
            );
            $this->entityManager->getRepository(Credit::class)->updateAmount(array_intersect_key($credits, $creditBasketAmounts));
            $this->entityManager->getRepository(CreditBasketAmount::class)->insert($creditBasketAmounts);
        }
    }

    public function removeCredit(Basket $basket)
    {
        $creditBasketAmounts = $this->entityManager->getRepository(CreditBasketAmount::class)->findByBasket($basket);
        foreach ($creditBasketAmounts as $creditBasketAmount) {
            $credit = $creditBasketAmount->getCredit();
            $credit->setCurrentAmount($credit->getCurrentAmount() + $creditBasketAmount->getAmount());
            $this->entityManager->persist($credit);
            $this->entityManager->remove($creditBasketAmount);
        }
    }

    public function getSelectedProductsList(Basket $basket)
    {
        return array_map(
            function (ProductQuantity $productQuantity) {
                return $productQuantity->getProduct();
            },
            array_filter($basket->getProductQuantityCollection()->toArray(), function (ProductQuantity $productQuantity) {
                return 1 == $productQuantity->getQuantity();
            })
        );
    }

    public function changeModel(Basket $basket, array $previousProducts)
    {
        $nextProducts = $this->getSelectedProductsList($basket);
        $addedProducts = array_diff($nextProducts, $previousProducts);
        $removedProducts = array_diff($previousProducts, $nextProducts);
        $baskets = $this->entityManager->getRepository(Basket::class)->findByParent($basket);
        foreach ($baskets as $basket) {
            foreach ($basket->getProductQuantityCollection() as $productQuantity) {
                if (in_array($productQuantity->getProduct(), $removedProducts)) {
                    $this->entityManager->remove($productQuantity);
                }
            }
            foreach ($addedProducts as $product) {
                $productQuantity = new ProductQuantity();
                $productQuantity->setBasket($basket);
                $productQuantity->setProduct($product);
                $productQuantity->setQuantity(0);
                $this->entityManager->persist($productQuantity);
                $basket->addProductQuantity($productQuantity);
            }
            $this->entityManager->persist($basket);
        }
    }

    public function createBasket(?Basket $modelBasket): Basket
    {
        $basket = new Basket();
        $basket->setUser($this->tokenStorage->getToken()->getUser());
        $basket->setParent($modelBasket);
        $basket->setDate($modelBasket->getDate());
        $productQuantityCollection = [];
        foreach ($modelBasket->getProductQuantityCollection() as $modelProductQuantity) {
            if ($modelProductQuantity->getQuantity()) {
                $productQuantity = new ProductQuantity();
                $productQuantity->setBasket($basket);
                $productQuantity->setProduct($modelProductQuantity->getProduct());
                $productQuantity->setQuantity(0);
                $this->entityManager->persist($productQuantity);
                array_push($productQuantityCollection, $productQuantity);
            }
        }
        $basket->setProductQuantityCollection($productQuantityCollection);

        return $basket;
    }

    public function createModel(): Basket
    {
        $basket = new Basket();
        $basket->setUser($this->tokenStorage->getToken()->getUser());
        $products = $this->entityManager->getRepository(Product::class)->findActive();
        $productQuantityCollection = [];
        foreach ($products as $product) {
            $productQuantity = new ProductQuantity();
            $productQuantity->setBasket($basket);
            $productQuantity->setProduct($product);
            $productQuantity->setQuantity(1);
            $this->entityManager->persist($productQuantity);
            array_push($productQuantityCollection, $productQuantity);
        }
        $basket->setProductQuantityCollection($productQuantityCollection);

        return $basket;
    }

    public function generateSyntheses(FormInterface $form, User $user): array
    {
        $roles = $user->getRoles();
        $producers = $this->userManager->getProducers($user);
        $extra = [];
        $type = $form->get('type')->getData();
        $start = $form->get('start')->getData();
        $end = $form->get('end')->getData();
        $needCreditGeneration = (in_array('ROLE_ADMIN', $roles) || in_array('ROLE_REFERENT', $roles)) && (SynthesesType::PRODUCT_BY_MEMBER == $type || SynthesesType::INVOICE_BY_PRODUCER == $type);
        if (SynthesesType::INVOICE_BY_PRODUCER_BY_MEMBER == $type) {
            $credits = $this->entityManager->getRepository(Basket::class)->findCreditByDateForProducer($start, $end, $producers);
            foreach ($credits as $credit) {
                $extra[$credit['id']]['Avoirs'] = $extra[$credit['id']]['Avoirs'] ?? [];
                $extra[$credit['id']]['Avoirs'][] = $credit['value'].' (bénéficiaire: '.$credit['name'].', objet: '.$credit['object'].', montant initial: '.$credit['totalAmount'].', montant restant: '.$credit['currentAmount'].')';
            }
        } elseif (SynthesesType::INVOICE_BY_MEMBER == $type) {
            $credits = $this->entityManager->getRepository(Basket::class)->findCreditByDateForMember($start, $end, $producers);
            foreach ($credits as $credit) {
                $extra[$credit['id']]['Avoirs'] = $extra[$credit['id']]['Avoirs'] ?? [];
                $extra[$credit['id']]['Avoirs'][] = $credit['value'].' (producteur: '.$credit['name'].', objet: '.$credit['object'].', montant initial: '.$credit['totalAmount'].', montant restant: '.$credit['currentAmount'].')';
            }
        }
        $items = $this->entityManager->getRepository(Basket::class)->getSyntheses($start, $end, $type, $producers);
        foreach ($items as &$item) {
            if (isset($item['column']) && isset($item['value'])) {
                $item[$item['column']] = $item['value'];
            }
        }
        $columns = $this->entityManager->getRepository(Basket::class)->getColumns($start, $end, $type);
        $tables = [];
        $parameters = [];
        foreach ($items as $item) {
            $table = $item['table'];
            $tables[$table] = $tables[$table] ?? [];
            $localTable = &$tables[$table];
            $parameters[$table] = $parameters[$table] ?? [];
            $localParameters = &$parameters[$table];
            if (isset($item['tbody'])) {
                $tbody = $item['tbody'];
                $tables[$table][$tbody] = $tables[$table][$tbody] ?? [];
                $localTable = &$tables[$table][$tbody];
                $parameters[$table][$tbody] = $parameters[$table][$tbody] ?? [];
                $localParameters = &$parameters[$table][$tbody];
            }
            $line = $item['line'];
            $id = $item['id'];
            $localTable[$line] = $localTable[$line] ?? array_fill_keys($columns, '');
            $localParameters[$line] = $localParameters[$line] ?? ['color' => $item['color'] ?? 'transparent'];
            if ($needCreditGeneration) {
                $localParameters[$line]['formValues'] = json_encode([
                    'generate_credit[product]' => $item['credit_product'],
                    'generate_credit[date]' => null,
                    'generate_credit[member]' => $item['credit_member'] ?? null,
                    'generate_credit[quantity]' => null,
                ]);
                $localParameters[$line]['credit_text'] = $item['credit_line_text'] ?? '';
            }
            foreach ($columns as $column) {
                if (isset($item[$column])) {
                    $localTable[$line][$column] = $item[$column];
                    if ($needCreditGeneration) {
                        $localParameters[$line][$column]['formValues'] = json_encode([
                            'generate_credit[product]' => $item['credit_product'],
                            'generate_credit[date]' => $item['credit_date'],
                            'generate_credit[member]' => $item['credit_member'] ?? null,
                            'generate_credit[quantity]' => $item['credit_quantity'] ?? null,
                        ]);
                        $localParameters[$line][$column]['subForm'] = isset($item['credit_quantity']) ? '.quantity' : '';
                        $localParameters[$line][$column]['credit_text'] = $item['credit_column_text'] ?? '';
                    }
                }
            }
            if (isset($extra[$id])) {
                $localParameters['extra'] = $extra[$id];
            }
            if (isset($item['broadcastList'])) {
                $localParameters['broadcastList'] = $item['broadcastList'];
            }
            if (isset($item['email'])) {
                $localParameters['email'] = $item['email'];
            }
        }

        return [$tables, $parameters];
    }

    public function setDeleted(Basket $basket)
    {
        $basket->setDeleted(true);
        $baskets = $this->entityManager->getRepository(Basket::class)->findByParentAndFrozen($basket, 0);
        foreach ($baskets as $subBasket) {
            $subBasket->setDeleted(true);
        }
    }
}

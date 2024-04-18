<?php

namespace App\Manager;

use App\Entity\Order;
use App\Factory\OrderFactory;
use App\Repository\OrderRepository;
use App\Storage\CartSessionStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Cart Manager for managing cart on the application
 */
class CartManager
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly OrderFactory $orderFactory,
        private readonly CartSessionStorage $cartSessionStorage,
        private readonly Security $security,
        private readonly OrderRepository $orderRepo,
    ) {
    }

    /**
     * Get the current Cart
     *
     * @return Order
     */
    public function getCurrentCart(): Order
    {
        // On récupère le panier en session
        $cart = $this->cartSessionStorage->getCart();

        // On récupère l'utilisateur connecté
        $user = $this->security->getUser();

        // On vérifie si on a récupéré un panier en session 
        if (!$cart) {
            // On vérifie si l'utilisateur est connecté
            if ($user) {
                $cart = $this->orderRepo->findLatestCartByUser($user);
            }
        } elseif ($cart->getUser() === null && $user) {
            $oldCart = $this->orderRepo->findLatestCartByUser($user);

            if ($oldCart) {
                $cart = $this->mergeCarts($cart, $oldCart);
            }

            $cart->setUser($user);
        }

        return $cart ?? $this->orderFactory->create();
    }

    /**
     * Save a cart in DB and Session
     *
     * @param Order $cart
     * @return void
     */
    public function save(Order $cart): void
    {
        // On enregistre le panier en BDD
        $this->em->persist($cart);
        $this->em->flush();

        // On enregistre le panier en session
        $this->cartSessionStorage->setCart($cart);
    }

    /**
     * Merge 2 cart in one
     *
     * @param Order $cartSession Cart of session
     * @param Order $lastCart Cart of Database
     * @return Order
     */
    private function mergeCarts(Order $cartSession, Order $lastCart): Order
    {
        // On boucle sur les orderItems du cart de la BDD
        foreach ($lastCart->getItems() as $orderItem) {
            $cartSession->addItem($orderItem);
        }

        // On supprime le cart de la BDD
        $this->em->remove($lastCart);
        $this->em->flush();

        return $cartSession;
    }
}

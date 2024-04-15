<?php

namespace App\Storage;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class to mananing cart in SESSION on application
 */
class CartSessionStorage
{
    public const CART_SESSION_KEY = 'cart_id';

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly OrderRepository $orderRepo,
    ) {
    }

    /**
     * Get the current cart in session
     *
     * @return ?Order
     */
    public function getCart(): ?Order
    {
        return $this->orderRepo->findOneBy([
            'id' => $this->getCartId(),
            'status' => Order::STATUS_CART,
        ]);
    }

    /**
     * set cart in the session
     *
     * @param Order $cart
     * @return void
     */
    public function setCart(Order $cart): void
    {
        $this->getSession()->set(self::CART_SESSION_KEY, $cart->getId());
    }

    /**
     * Get the current Session object
     *
     * @return SessionInterface
     */
    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }

    /**
     * Get the cart id from session
     *
     * @return integer|null
     */
    private function getCartId(): ?int
    {
        return $this->getSession()->get(self::CART_SESSION_KEY);
    }
}

<?php

namespace App\Factory;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Produit;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Factory class for Order to create Order object
 */
class OrderFactory
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    /**
     * Create Order object
     *
     * @return Order
     */
    public function create(): Order
    {
        // On créé la commande et on définit le status à Panier
        $order = (new Order)
            ->setStatus(Order::STATUS_CART);

        // On vérifie si l'utilisateur est connecté
        if ($this->security->getUser()) {
            // Si l'utilisateur est connecté, on définit l'utilisateur de la commande
            $order->setUser(
                $this->security->getUser()
            );
        }

        return $order;
    }

    /**
     * Create OrderItem object
     *
     * @param Produit $produit
     * @return OrderItem
     */
    public function createItem(Produit $produit): OrderItem
    {
        return (new OrderItem)
            ->setProduct($produit)
            ->setQuantity(1);
    }
}

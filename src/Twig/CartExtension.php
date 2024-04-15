<?php

namespace App\Twig;

use App\Manager\CartManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CartExtension extends AbstractExtension
{
    public function __construct(
        private readonly CartManager $cartManager,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getCartNumber', [$this, 'cart']),
        ];
    }

    public function cart(): int
    {
        // On récupère le panier
        $cart = $this->cartManager->getCurrentCart();

        $number = 0;

        // On boucle sur les OrderItems de la commande
        foreach ($cart->getItems() as $orderItem) {
            $number += $orderItem->getQuantity();
        }

        return $number;
    }
}

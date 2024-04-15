<?php

namespace App\Twig\Components;

use App\Entity\Order;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class CartTotal
{
    public Order $cart;
    public bool $iscart = false;
}

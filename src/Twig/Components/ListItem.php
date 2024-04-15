<?php

namespace App\Twig\Components;

use App\Entity\Order;
use Symfony\Component\Form\FormView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class ListItem
{
    public FormView|Order $order;
    public bool $iscart = false;
}

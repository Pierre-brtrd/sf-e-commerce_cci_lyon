<?php

namespace App\Twig\Components;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\FormView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class CheckoutAddressForm
{
    public FormView $form;
    public Collection $addresses;
}

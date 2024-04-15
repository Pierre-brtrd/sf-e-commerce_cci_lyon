<?php

namespace App\Twig\Components;

use Doctrine\Common\Collections\Collection;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Categories
{
    public Collection $categories;
}

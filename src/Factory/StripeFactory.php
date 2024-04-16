<?php

namespace App\Factory;

use Stripe\Stripe;

/**
 * Classe de gestion des communications avec l'api de STRIPE
 */
class StripeFactory
{
    public function __construct(
        private readonly string $stripeSecretKey
    ) {
        Stripe::setApiKey($this->stripeSecretKey);
        Stripe::setApiVersion('2020-08-27');
    }
}

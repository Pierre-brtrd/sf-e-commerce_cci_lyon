<?php

namespace App\Event;

use Stripe\ApiResponse;
use Stripe\Event;
use Symfony\Contracts\EventDispatcher\Event as BaseEvent;

/**
 * Classe qui représente un évènement Stripe
 */
class StripeEvent extends BaseEvent
{
    public function __construct(
        private readonly Event $event,
    ) {
    }

    /**
     * Cette méthode va permettre de récupérer le nom de l'Évènement transmis par Stripe
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->event->type;
    }

    /**
     * Permet de récupérer l'objet transmis par stripe
     *
     * @return ApiResponse
     */
    public function getResource(): ApiResponse
    {
        return $this->event->data->object;
    }
}

<?php

namespace App\Form\EventListener;

use App\Entity\Order;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\ClickableInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ClearCartListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [FormEvents::POST_SUBMIT => 'postSubmit'];
    }

    public function postSubmit(FormEvent $event): void
    {
        // On récupère le formulaire
        $form = $event->getForm();

        // On récupère l'order
        $order = $form->getData();

        if (!$order instanceof Order) {
            return;
        }

        // On récupère le bouton Clear
        /** @var ClickableInterface $clearBtn */
        $clearBtn = $form->get('clear');

        // On vérifie si on a pas cliqué dessus
        if (!$clearBtn->isClicked()) {
            return;
        }

        // On supprime tout les orderItem de la commande
        $order->removeItems();
    }
}

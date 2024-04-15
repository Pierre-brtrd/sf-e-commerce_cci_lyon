<?php

namespace App\Form\EventListener;

use App\Entity\Order;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\ClickableInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class RemoveItemListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [FormEvents::POST_SUBMIT => 'postSubmit'];
    }

    public function postSubmit(FormEvent $event): void
    {
        // On récupère le formulaire
        $form = $event->getForm();

        // On récupère la commande
        $order = $form->getData();

        // On vérifie qu'on a bien un objet Order
        if (!$order instanceof Order) {
            return;
        }

        // On boucle sur les items du formulaire
        foreach ($form->get('items') as $item) {
            /** @var ClickableInterface $removeBtn */
            $removeBtn = $item->get('remove');

            // On vérifie si on a cliqué sur le bouton
            if ($removeBtn->isClicked()) {
                $order->removeItem($item->getData());

                return;
            }
        }
    }
}

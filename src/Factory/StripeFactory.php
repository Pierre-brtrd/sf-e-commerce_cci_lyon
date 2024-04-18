<?php

namespace App\Factory;

use App\Entity\OrderItem;
use App\Entity\Payment;
use App\Event\StripeEvent;
use Stripe\Checkout\Session;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;
use Symfony\Component\HttpFoundation\JsonResponse;
use Webmozart\Assert\Assert;

/**
 * Classe de gestion des communications avec l'api de STRIPE
 */
class StripeFactory
{
    public function __construct(
        private readonly string $stripeSecretKey,
        private readonly string $webhookSecret,
    ) {
        Stripe::setApiKey($this->stripeSecretKey);
        Stripe::setApiVersion('2020-08-27');
    }

    /**
     * Permet de créer un objet Session stripe pour préparer un paiement sur Stripe Checkout
     *
     * @param Payment $payment
     * @param string $successUrl
     * @param string $cancelUrl
     * @return Session
     */
    public function createPaymentIntent(Payment $payment, string $successUrl, string $cancelUrl): Session
    {
        // On récupérer la commande reliée au paiement
        $order = $payment->getOrderRef();

        // On vérifie que la commande existe
        Assert::notNull($order, 'Order cannot be null');

        return Session::create([
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'customer_email' => $order->getUser()->getEmail(),
            'metadata' => [
                'order_id' => $order->getId(),
                'payment_id' => $payment->getId(),
            ],
            'payment_intent_data' => [
                'metadata' => [
                    'order_id' => $order->getId(),
                    'payment_id' => $payment->getId(),
                ]
            ],
            'line_items' => array_map(function (OrderItem $orderItem): array {
                return [
                    'quantity' => $orderItem->getQuantity(),
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $orderItem->getQuantity() . ' x '  . $orderItem->getProduct()->getTitle(),
                            'description' => $orderItem->getProduct()->getShortDescription(),
                            'images' => [
                                'https://picsum.photos/300/200'
                            ]
                        ],
                        'unit_amount' => bcmul($orderItem->getPriceTTC(), 100),
                    ]
                ];
            }, $order->getItems()->toArray()),
        ]);
    }

    /**
     * Permet de décoder les requête envoyées par stripe et de gérer les évènements
     *
     * @param string $stripeSignature clé publique Stripe
     * @param mixed $body contenu de la requête de Stripe
     * @return JsonResponse
     */
    public function handleRequest(string $stripeSignature, mixed $body): JsonResponse
    {
        // On vérifie que le body n'est pas null
        if (!$body) {
            return new JsonResponse([
                'status' => 'Error',
                'message' => 'Impossible de récupérer le contenu de la requête'
            ], 400);
        }

        $event = $this->getEvent($body, $stripeSignature);

        if ($event instanceof JsonResponse) {
            return $event;
        }

        $event = new StripeEvent($event);

        return new JsonResponse([
            'status' => 'Succès',
            'message' => 'Évènement bien reçu'
        ], 200);
    }

    /**
     * Récupère la requête chiffrée avec la clé publique Stripe et doit décoder la requête pour renvoyer
     * un objet de type \Stripe\Event
     *
     * @param mixed $body
     * @param string $stripeSignature
     * @return Event|JsonResponse
     */
    private function getEvent(mixed $body, string $stripeSignature): Event|JsonResponse
    {
        try {
            $event = Webhook::constructEvent($body, $stripeSignature, $this->webhookSecret);
        } catch (\UnexpectedValueException $e) {
            return new JsonResponse([
                'status' => 'Error',
                'message' => 'Valeur inatendue pour la création de l\'évènement Stripe :' . $e->getMessage()
            ], 400);
        } catch (SignatureVerificationException $e) {
            return new JsonResponse([
                'status' => 'Error',
                'message' => 'Erreur lors de la vérification des tokens: ' . $e->getMessage()
            ]);
        }

        return $event;
    }
}

<?php

namespace App\EventListener;

use App\Entity\Order;
use App\Entity\Payment;
use App\Event\StripeEvent;
use App\Repository\OrderRepository;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Stripe\ApiResource;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'payment_intent.created', method: 'onPaymentCreated')]
#[AsEventListener(event: 'payment_intent.succeeded', method: 'onPaymentSucceeded')]
#[AsEventListener(event: 'payment_intent.payment_failed', method: 'onPaymentFailed')]
#[AsEventListener(event: 'checkout.session.completed', method: 'onSessionCompleted')]
class StripeEventListener
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly OrderRepository $orderRepo,
        private readonly PaymentRepository $paymentRepo,
    ) {
    }

    public function onPaymentCreated(StripeEvent $event): void
    {
        $data = $this->verifyResource($event->getResource());

        $data['order']->setStatus(Order::STATUS_NEW);
        $data['payment']->setStatus(Payment::STATUS_AWAITING_PAYMENT);

        $this->em->flush();
    }

    public function onPaymentSucceeded(StripeEvent $event): void
    {
        $data = $this->verifyResource($event->getResource());

        $data['order']->setStatus(Order::STATUS_PAID);
        $data['payment']->setStatus(Payment::STATUS_PAID);

        $this->em->flush();
    }

    public function onPaymentFailed(StripeEvent $event): void
    {
        $data = $this->verifyResource($event->getResource());

        $data['order']->setStatus(Order::STATUS_PAYMENT_FAILED);
        $data['payment']->setStatus(Payment::STATUS_REFUSED);

        $this->em->flush();
    }

    public function onSessionCompleted(StripeEvent $event): void
    {
        $data = $this->verifyResource($event->getResource());

        if ($data['payment']->getStatus() !== Payment::STATUS_PAID) {
            $data['payment']->setStatus(Payment::STATUS_REFUSED);
            $this->em->flush();
        } elseif ($data['order']->getStatus() !== Order::STATUS_PAID) {
            $data['order']->setStatus(Order::STATUS_CANCELED);
            $this->em->flush();
        }
    }

    private function verifyResource(ApiResource $resource): array
    {
        if (!$resource) {
            throw new InvalidArgumentException("Resource not found");
        }

        $order = $this->orderRepo->find($resource->metadata->order_id);
        $payment = $this->paymentRepo->find($resource->metadata->payment_id);

        if (!$order || !$payment) {
            throw new InvalidArgumentException('Can\'t find order or payment with metadata informations');
        }

        return [
            'order' => $order,
            'payment' => $payment
        ];
    }
}

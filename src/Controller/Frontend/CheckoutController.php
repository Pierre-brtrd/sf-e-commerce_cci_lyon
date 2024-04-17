<?php

namespace App\Controller\Frontend;

use App\Entity\Address;
use App\Entity\Payment;
use App\Entity\User;
use App\Factory\StripeFactory;
use App\Form\AddressType;
use App\Form\PaymentType;
use App\Manager\CartManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/checkout', name: 'app.checkout')]
class CheckoutController extends AbstractController
{
    public function __construct(
        private readonly CartManager $cartManager,
        private readonly EntityManagerInterface $em,
        private readonly StripeFactory $stripeFactory,
    ) {
    }

    #[Route('/adresse', name: '.address', methods: ['GET', 'POST'])]
    public function address(Request $request): Response
    {
        /** @var User $user **/
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour passer à l\'étape suivante');

            return $this->redirectToRoute('app.cart.index');
        }

        $cart = $this->cartManager->getCurrentCart();

        if (!$cart || count($cart->getItems()) < 1) {
            $this->addFlash('error', 'Vous devez avoir au minimum un produit dans votre panier');

            return $this->redirectToRoute('app.cart.index');
        }

        if ($user->getDefaultAddress()) {
            $address = clone $user->getDefaultAddress();
        } elseif ($user->getAddresses()->count() > 0) {
            $address = clone $user->getAddresses()->first();
        } else {
            $address = new Address();
        }

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$user->hasAddress($address)) {
                $address->setUser($user);

                $this->em->persist($address);
                $this->em->flush();
            }

            return $this->redirectToRoute('app.checkout.recap');
        }

        return $this->render('Frontend/Checkout/address.html.twig', [
            'form' => $form,
            'cart' => $cart,
        ]);
    }

    #[Route('/recap', name: '.recap', methods: ['GET', 'POST'])]
    public function recap(Request $request): Response|RedirectResponse
    {
        /** @var User $user **/
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour passer à l\'étape suivante');

            return $this->redirectToRoute('app.cart.index');
        }

        $cart = $this->cartManager->getCurrentCart();

        if (!$cart || count($cart->getItems()) < 1) {
            $this->addFlash('error', 'Vous devez avoir au minimum un produit dans votre panier');

            return $this->redirectToRoute('app.cart.index');
        }

        $payment = (new Payment)
            ->setUser($user)
            ->setOrderRef($cart);

        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $payment->setStatus(Payment::STATUS_NEW);

            $this->em->persist($payment);
            $this->em->flush();

            $stripeSession = $this->stripeFactory->createPaymentIntent(
                $payment,
                $this->generateUrl('app.checkout.success', [], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->generateUrl('app.checkout.cancel', [], UrlGeneratorInterface::ABSOLUTE_URL)
            );

            return $this->redirect($stripeSession->url);
        }

        return $this->render('Frontend/Checkout/recap.html.twig', [
            'cart' => $cart,
            'form' => $form,
        ]);
    }

    #[Route('/success', name: '.success', methods: ['GET'])]
    public function success(): Response
    {
        return new Response('Paiement Ok');
    }

    #[Route('/cancel', name: '.cancel', methods: ['GET'])]
    public function cancel(): Response
    {
        return new Response('Paiement pas ok');
    }
}

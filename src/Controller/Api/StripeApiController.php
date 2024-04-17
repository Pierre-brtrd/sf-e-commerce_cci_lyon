<?php

namespace App\Controller\Api;

use App\Factory\StripeFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/payment/stripe', name: 'api.payment.stripe')]
class StripeApiController extends AbstractController
{
    #[Route('/notify', name: '.notify', methods: ['POST'])]
    public function notify(Request $request, StripeFactory $stripeFactory): JsonResponse
    {
        // On récupère la clé signé dans la requête
        $stripeSignature = $request->headers->get('stripe-signature');

        // On vérifie qu'on a récupéré la clé signé
        if (!$stripeSignature) {
            // Sinon on renvoie un message en JSON avec l'erreur
            return new JsonResponse([
                'status' => 'Error',
                'message' => 'Impossible de récupérer la clé signé de stripe'
            ], 400);
        }

        $response = $stripeFactory->handleRequest(
            $stripeSignature,
            $request->getContent()
        );

        return $response;
    }
}

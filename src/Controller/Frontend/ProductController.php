<?php

namespace App\Controller\Frontend;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/produits', name: 'app.produits')]
class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProduitRepository $productRepo,
    ) {
    }

    #[Route('', name: '.index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = $request->query->get('page', 1);

        return $this->render('Frontend/Produits/index.html.twig', [
            'produits' => $this->productRepo->findListShop($page),
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductsRepository $productsRepository): Response
    {
        $products = $productsRepository->findAll();

        $productBestSeller = $productsRepository->findByIsBestSeller(1);

        $productSpecialOffer = $productsRepository->findByIsSpecialOffer(1);

        $productNewArrival = $productsRepository->findByIsNewArrival(1);

        $productFeatured = $productsRepository->findByIsFeatured(1);
        
        return $this->render('home/index.html.twig', [
            'products' => $products,
            'productBestSeller' => $productBestSeller,
            'productSpecialOffer' => $productSpecialOffer,
            'productNewArrival' => $productNewArrival,
            'productFeatured' => $productFeatured,
        ]);
    }

    #[Route('/product/{slug}', name: 'app_product_details')]
    public function show(?Products $product): Response
    {
        if (!$product) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/single_product.html.twig', [
            'product' => $product,
        ]);
    }
}

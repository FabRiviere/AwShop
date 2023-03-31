<?php

namespace App\Controller\Cart;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }    
    
    #[Route('/cart', name: 'app_cart')]
    public function index(): Response
    {
        $cart = $this->cartService->getFullCart();

        if (!isset($cart['products'])) {
            return $this->redirectToRoute('app_home');
        }
        
        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function addToCart($id): Response
    {
        $this->cartService->addToCart($id);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/delete/{id}', name: 'app_cart_delete')]
    public function deleteFromCart($id): Response
    {
        $this->cartService->deleteFromCart($id);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/delete-all/{id}', name: 'app_cart_delete_all')]
    public function deleteAllToCart($id): Response
    {
        $this->cartService->deleteAllToCart($id);
        return $this->redirectToRoute('app_cart');
    }
}

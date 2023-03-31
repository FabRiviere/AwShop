<?php

namespace App\Service;

use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private $requestStack;
    private $repoProduct;
    private $tva = 0.2;

    public function __construct(RequestStack $requestStack, ProductsRepository $repoProduct)
    {
        $this->requestStack = $requestStack;
        $this->repoProduct = $repoProduct;
    }

    //; Adding a product to cart 
    public function addToCart($id)
    {
        $cart = $this->getCart(); //: we get the cart
        if (isset($cart[$id])) {  //: We check if the product is already present in the cart
            $cart[$id]++; //: product already in the cart => we increase the quantity
        } else {
            $cart[$id] = 1; //: product is not in the cart => we add it
        }
        $this->updateCart($cart);
    }

    //; Removing a product unit from the cart 
    public function deleteFromCart($id)
    {
        $cart = $this->getCart();

        if (isset($cart[$id])) {
            if ($cart[$id] > 1) { //: if the product exists more than once in the cart, its value is decremented
                $cart[$id]--;
            } else {
                unset($cart[$id]); //: if the product only exists once in the cart, it is simply removed
            }
        $this->updateCart($cart);
        }
    }

    //; Deletion of a product from the cart, regardless of its quantity 
    public function deleteAllToCart($id)
    {
        $cart = $this->getCart();

        if (isset($cart[$id])) {
            unset($cart[$id]);
            $this->updateCart($cart); //: update session
        }
    }

    //; Delete entire cart 
    public function deleteCart() {
        $this->updateCart([]);
    }

    //; Update of the cart and creation of another session to display the items of the cart (header icon) 
    public function updateCart($cart)
    {
        $session = $this->requestStack->getSession();
        $session->set('cart', $cart);

        $session->set('cartData', $this->getFullCart());
    }

    //; we get the cart
    public function getCart()
    {
        $session = $this->requestStack->getSession();
        return $session->get('cart', []);
    }

    //; Retrieve the products in the cart 
    public function getFullCart()
    {
        $cart = $this->getCart();

        $fullCart = [];
        $quantity_cart = 0;
        $subTotal = 0;

        foreach ($cart as $id => $quantity) {
            $product = $this->repoProduct->find($id);

            if ($product) {
                if ($quantity > $product->getQuantity()) { //: check whether the product is in sufficient quantity
                    $quantity = $product->getQuantity(); //: if insufficient quantity, we change the qty requested by the qty in stock
                    $cart[$id] = $quantity;
                    $this->updateCart($cart);
                }

                $fullCart['products'][] = ['quantity' => $quantity, 'product' => $product];
                $quantity_cart += $quantity;
                $subTotal += $quantity * $product->getPrice()/100;
            } else {
                $this->deleteFromCart($id); //: $id (identifiant) incorrect
            }
        }
        $fullCart['data'] = [
            'quantity_cart' => $quantity_cart,
            'subTotalHT' => $subTotal,
            'Taxe' => round($subTotal * $this->tva, 2),
            'subTotalTTC' => round(($subTotal + ($subTotal * $this->tva)), 2)
        ];

        return $fullCart;
    }

}

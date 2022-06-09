<?php

namespace App\Controller;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="app_cart")
     */
    public function index(CartService $cs): Response
    {
        return $this->render('cart/index.html.twig', [
            'items' => $cs->getFilledCart(),
            'total' => $cs->getTotal()
        ]);
    }

    /**
     * @Route("/cart/add/{id}/{route}", name="cart_add")
     */
    public function add($id, $route, CartService $cs)
    {
        $cs->add($id);
        return $this->redirectToRoute($route);
    }

    /**
     * @Route("/cart/remove/{id}", name="cart_remove")
     */
    public function remove($id, CartService $cs)
    {
        $cs->remove($id);
        return $this->redirectToRoute('app_cart');
    }

    /**
     * @Route("/cart/decrement/{id}", name="cart_decrement")
     */
    public function decrement($id, CartService $cs)
    {
        $cs->decrement($id);
        return $this->redirectToRoute('app_cart');
    }

    /**
     * @Route("/cart/empty", name="cart_empty")
     */
    public function empty(CartService $cs)
    {
        $cs->empty();
        return $this->redirectToRoute('app_cart');
    }
}

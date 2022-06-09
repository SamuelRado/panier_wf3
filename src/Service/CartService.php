<?php

namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private $rs;
    private $repo;

    public function __construct(RequestStack $rs, ProductRepository $repo)
    {
        $this->rs = $rs;
        $this->repo = $repo;
    }

    public function add($id)
    {
        $session = $this->rs->getSession();
        // getSession() permet de créer ou récupérer la session de l'utilisateur

        $cart = $session->get('cart', []);
        // je recupère l'attr de session 'cart' s'il existe ou un tableau vide

        // si le produit existe déjà, j'incrémente sa qté
        if (!empty($cart[$id]))
            $cart[$id]++;
        else
            $cart[$id] = 1;
        // dans le tableau $cart, à l'indice $id, je donne la valeur 1

        $session->set('cart', $cart);
        // je sauvegarde l'état du panier en session à l'attr de session 'cart'
    }

    public function remove($id)
    {
        $session = $this->rs->getSession();
        $cart = $session->get('cart', []);

        if (!empty($cart[$id]))
            unset($cart[$id]);

        $session->set('cart', $cart);
    }

    public function decrement($id)
    {
        $session = $this->rs->getSession();
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            if ($cart[$id] > 1)
                $cart[$id]--;
            else
                unset($cart[$id]);
        }
        $session->set('cart', $cart);
    }

    public function empty()
    {
        $session = $this->rs->getSession();
        // $cart = $session->get('cart', []);

        // $session->remove('cart');
        // $session->clear();
        $session->set('cart', []);
    }

    public function getFilledCart()
    {
        $session = $this->rs->getSession();
        $cart = $session->get('cart', []);

        $cartWithData = [];
        // $cartWithData est un tableau qui contiendra des objets Product et leurs qtés

        // pour chaque $id dans $cart, on ajoute une case à $cartWithData
        // chaque case de $cartWithData est un tableau contenant une case 'product' et une case 'quantity'

        foreach ($cart as $id => $quantity) {
            $cartWithData[] = [
                'product' => $this->repo->find($id),
                'quantity' => $quantity
            ];
        }
        return $cartWithData;
    }

    public function getTotal()
    {
        $total = 0;

        foreach ($this->getFilledCart() as $item) {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $total += $totalItem;
        }
        return $total;
    }
}
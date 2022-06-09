<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CheckoutController extends AbstractController
{
    /**
     * @Route("/checkout", name="app_checkout")
     */
    public function index(): Response
    {
        return $this->render('checkout/index.html.twig');
    }

    /**
     * @Route("/checkout/session", name="checkout_session")
     */
    public function checkout_session($stripeSK)
    {
        \Stripe\Stripe::setApiKey($stripeSK);
        // permet d'indiquer notre clef secrète pour les futurs appels à l'API de Stripe

        $session = \Stripe\Checkout\Session::create([
            'line_items' => [   // line_items est un tableau qui contient un produit par case
                [   // ce tableau représente 1 produit
                    'price_data' => [   // ce tableau représente les données du prix (devise, le prix unitaire, etc)
                        'currency' => 'eur',
                        'unit_amount' => 2000,
                        'product_data' => [ // ce tableau représente les données d'un produit (nom, description, etc)
                            'name' => 'T-shirt',
                            'description' => "Un t-shirt simple"
                        ],
                    ],
                    'quantity' => 1,
                ],
                [   // ce tableau représente 1 produit
                    'price_data' => [   // ce tableau représente les données du prix (devise, le prix unitaire, etc)
                        'currency' => 'eur',
                        'unit_amount' => 4000,
                        'product_data' => [ // ce tableau représente les données d'un produit (nom, description, etc)
                            'name' => 'Pantalon',
                        ],
                    ],
                    'quantity' => 2,
                ],
            ],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('checkout_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            // url en cas de succès du paiement
            // generateUrl permet de générer un URL vers la route indiquée. ABSOLUTE_URL permet de générer l'url absolue vers cette route
            'cancel_url' => $this->generateUrl('checkout_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
        // dd($session);
        return $this->redirect($session->url, 303);
        // redirige vers le lien pour le paiement avec le code 303 (code HTTP qui signifie redirection)
    }

    /**
     * @Route("/checkout/success", name="checkout_success")
     */
    public function checkout_success()
    {
        return $this->render('checkout/success.html.twig');
    }

    /**
     * @Route("/checkout/cancel", name="checkout_cancel")
     */
    public function checkout_cancel()
    {
        return $this->render('checkout/cancel.html.twig');
    }
}

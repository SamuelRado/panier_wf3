<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

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
        // cette méthode permet de créer 2 items dans le line_items lors de la création de la session

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
     * @Route("/checkout_simple", name="checkout_simple")
     */
    public function checkout_simple($stripeSK)
    {
        /*
            Cette méthode permet de créer un Product en amont, créer un Price pour ce Product puis les envoyer
            à la session lors du checkout
        */

        \Stripe\Stripe::setApiKey($stripeSK);

        $stripe = new \Stripe\StripeClient($stripeSK);
        // $stripe représente notre compte

        $chaussettes = $stripe->products->create([
            'name' => 'Paire de chaussettes'
        ]);

        // la méthode create() permet de créer un objet Product sur notre dashboard

        // dd($chaussettes);

        $chaussettes_price = $stripe->prices->create([
            'currency' => 'eur',
            'unit_amount' => 500,
            'product' => $chaussettes->id   // je passe l'id du Product crée en amont
        ]);

        // dd($chaussettes_price);

        $session = \Stripe\Checkout\Session::create([
            'line_items' => [
                [
                    'price' => $chaussettes_price->id,
                    'quantity' => 3
                ],
            ],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('checkout_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('checkout_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
        return $this->redirect($session->url, 303);
    }

    /**
     * @Route("/checkout_recuperation", name="checkout_recuperation")
     */
    public function checkout_recuperation($stripeSK)
    {
        // vous devez écrire la fonction qui permet de récupérer le Product sur votre dashboard et l'envoyer à la session checkout
        // créez un bon de réduction et afficher le champ lors du checkout (15% de réduction, code à renseigner lors du checkout)

        \Stripe\Stripe::setApiKey($stripeSK);
        $stripe = new \Stripe\StripeClient($stripeSK);

        $audi = $stripe->prices->retrieve("price_1LA87YCiWS5MUDgAVAeX0Vy4");
        // retrieve() est une méthode permettant de récupérer un Price via son id

        $session = \Stripe\Checkout\Session::create([
            'line_items' => [
                [
                    'price' => $audi->id,
                    'quantity' => 1
                ],
            ],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('checkout_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('checkout_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'allow_promotion_codes' => 'true'
        ]);
        return $this->redirect($session->url, 303);
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

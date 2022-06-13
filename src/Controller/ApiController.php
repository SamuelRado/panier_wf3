<?php

namespace App\Controller;

use App\Form\SearchDptType;
use App\Service\ApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @Route("/api", name="app_api")
     */
    public function index(ApiService $as, Request $request): Response
    {
        $form = $this->createForm(SearchDptType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
            $communes = $as->getCommunesParDpt($form->get('dpt')->getData());
        else
            $communes = null;

        return $this->renderForm('api/index.html.twig', [
            'communes' => $communes,
            'form' => $form
        ]);

        // ajouter un formulaire : un champ où taper un code de département
        // afficher les villes de ce département et leurs populations respectives dans un tableau
        // afficher une erreur propre dans le cas où on tape un code de dpt erroné
    }
}


/*
    Méthodes HTTP
    Endpoints
    Headers

    Header (en-tête de la réponse, métadonnées) : code de réponse (200, 404...), type de contenu (json, html...), date, serveur
    Body (corps de la réponse)

    HTTP : protocole de communication
    GET : demande d'une représentation sur la ressource spécifiée
    POST : envoi d'une entité vers la ressource spécifiée
    HEAD : demande la même chose que GET sans le corps de la réponse
    DELETE : supprime une ressource
    PUT : remplace une ressource
    et d'autres

    Endpoint : URL avec laquelle communiquer pour effectuer une requête

    Code de réponses HTTP

    1xx : information
    2xx : succès
    3xx : redirection
    4xx : erreur client http
    5xx : erreur serveur


    Multi-threading : créer des threads (fils d'exécution) qui vont exécuter du code chacun de leurs côtés

    Prog asynchrone : lorsque du code n'attend pas une réponse pour continuer à s'exécuter
    Prog synchrone : lorsque qu'un code attend une action pour continuer à s'exécuter

    Listener : écoute un event en particulier en parallèle du reste de l'exécution du code
*/
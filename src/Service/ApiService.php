<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getCommunesParDpt($dpt)
    {
        $response = $this->client->request(
            "GET",
            "https://geo.api.gouv.fr/departements/$dpt/communes"
        );
        // dump($response->getStatusCode());   // renvoie le code http après exécution de la requête
        // dump($response->getHeaders());      // renvoie l'en-tête de la réponse
        // dump($response->getContent());      // renvoie le corps de la réponse sous forme de string
        if ($response->getStatusCode() == 200)
            return $response->toArray();
        else
            return -1;
    }
}

<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event) // le nom de la méthode est on + le nom de l'event
    {
        /*
            (TRY)   je teste le code
            {
                code qui peut merder
            }
            (CATCH) j'attrape l'erreur lancée par ce code

            (THROW) je renvoie un message à l'utilisateur
        */

        $exception = $event->getThrowable();    // je récupère l'exception lancée par l'event
        $message = sprintf(
            "Une erreur est survenue : %s. Code : %s",
            $exception->getMessage(),
            $exception->getCode()
        );

        // je crée une réponse à renvoyer à l'utilisateur
        $response = new Response();
        $response->setContent($message);

        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR); // erreur 500

        $event->setResponse($response);
    }
}

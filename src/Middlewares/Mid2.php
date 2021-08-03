<?php
namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AfterMiddleware
{
    /**
     * Example middleware invokable class
     *
     * @param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);
        // $response->getBody()->write(' AFTER');
        $response = $response->withHeader('Content-type', 'application/json');

        //Devolver respuesta (string) a formato JSON valido
        /*
        $response = $handler->handle($request);
        $existingContent = (string) $response->getBody();

        $response = new Response();
        // $response->getBody()->write("Before" . $existingContent);
        $response->getBody()->write($existingContent);

        */
        return $response;
    }
}

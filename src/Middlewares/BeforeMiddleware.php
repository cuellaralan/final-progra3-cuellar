<?php
namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class BeforeMiddleware
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
        $existingContent = (string) $response->getBody();

        $response = new Response();
        // $response->getBody()->write("Before" . $existingContent);
        $response->getBody()->write($existingContent);
        /**
         * VALIDAR JWT
         * getHeader('mi_token)
         */
        
        // if (   $response->getBody()->write($existingContent);    
        // } else {
        //     $!true) {
        //  response->getBody()->write('NO autorizado ');
        // }
        
        // $response->getBody()->write('BEFORE ' . $existingContent);

        return $response;
    }
}

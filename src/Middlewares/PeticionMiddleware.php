<?php
namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use App\Models\Funciones;
use App\Models\Peticion;

class PeticionMiddleware
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
        // var_dump($request);
        //["REQUEST_METHOD"]
        $ruta = $request->getUri()->getPath();
        $fecha = date('Y-m-d');
        $hora = time();
        $peticion = new Peticion($fecha, $hora, $ruta);
        $rta = Funciones::GuardarSerializa($peticion, '../src/Utils/Files/peticiones.txt', 'w');
        // var_dump($metodo);
        // $path = $request->uri ["path"];
        $existingContent = (string) $response->getBody();
        $response = new Response();
        // $response->getBody()->write("Before" . $existingContent);
        $response->getBody()->write($existingContent);

        return $response;

    }
}

<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Alumno;

class AlumnosController
{ // va a manejar todas las rutas de alumno
    public function getAll(Request $request, Response $response, $args)
    {
        $rta = json_encode(Alumno::all());
        $response->getBody()->write("hola estoy en routes.php \n");
        $response->getBody()->write($rta);
        return $response;
    }

    public function add(Request $request, Response $response, $args)
    {
        $alumno = new Alumno;
        $alumno->alumno = "Eloquent";
        $alumno->legajo = 152466;
        $alumno->localidad = 2;
        $alumno->cuatrimestre = 3;


        $rta = json_encode(array("ok" => $alumno->save()));
        // $response->getBody()->write("hola estoy en routes.php \n");
        $response->getBody()->write($rta);
        return $response;
    }
    public function logup(Request $request, Response $response, $args)
    {
        //obtengo parametros
        $datos = $request->getQueryParams();

        //valido parametros
        var_dump($datos);
        if(empty($datos) || !isset($datos["nombre"]) || !isset($datos["email"]) )
        {
            $response->getBody()->write("Datos erroneos, Reingrese!");
            return $response;
        }

        //valido claves en BD
        $alumnos = Alumno::all();
        foreach ($alumnos as $key => $value) {
            $value->alumno = $datos["alumno"];
        }
        //creo alumno
        // $alumno = new Alumno;
        // $alumno->alumno = "Eloquent";
        // $alumno->legajo = 152466;
        // $alumno->localidad = 2;
        // $alumno->cuatrimestre = 3;


        // $rta = json_encode(array("ok" => $alumno->save()));

        // $response->getBody()->write("hola estoy en routes.php \n");
        
        
        
        // $response->getBody()->write("ruta cheta");
        return $response;
    }
    public function addImage(Request $request, Response $response, $args)
    {
        // $alumno = new Alumno;
        // $alumno->alumno = "Eloquent";
        // $alumno->legajo = 152466;
        // $alumno->localidad = 2;
        // $alumno->cuatrimestre = 3;


        // $rta = json_encode(array("ok" => $alumno->save()));

        // $response->getBody()->write("hola estoy en routes.php \n");
        $response->getBody()->write("ruta cheta de imagen");
        return $response;
    }
}
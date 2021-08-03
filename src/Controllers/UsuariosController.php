<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Usuario;
use App\Models\Cripto;
use App\Models\Evento;
use App\Models\Funciones;
use DateTime;
use \Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Event;

class UsuariosController
{ // va a manejar todas las rutas de usuario
    public function getAll(Request $request, Response $response, $args)
    {
        $rta = json_encode(Usuario::all());
        // $response->getBody()->write("hola estoy en routes.php \n");
        $response->getBody()->write($rta);
        return $response;
    }
    public function saveEvent(Request $request, Response $response, $args)
    {
        $rta = array();
        $rta ['status'] = "Nok";
        $rta ['response'] = "";
        //formato de JWT
        $llave = "pro3-parcial";
        $payload = array(
                "iss" => "http://example.org",
                "aud" => "http://example.com",
                "iat" => 1356999524,
                "nbf" => 1357000000,
                "email" => 'mail@mail.com',
                "tipo" => 'no registrado'
        );
        //obtengo parametros
        $datos = $request->getParsedBody();
        $token = $request->getHeader("token")[0];
        //valido parametros
        // var_dump($datos);
        if(!empty($datos) && isset($datos["fecha"]) && isset($datos["descripcion"]) &&
        isset($token)  )
        {
            if($datos["fecha"] != "" && $datos["descripcion"] != "" && $token != "" )
            {
                $verifica = true;
                try {
                    // $token = $datos["token"];
                    // var_dump($token);
                    $decoded = JWT::decode($token, $llave, array('HS256'));
                    if ($decoded->tipo != 'user') {
                        $verifica = false;
                        $rta ['response'] = "Tipo de usuario no debe ser ADMIN";
                    }
                } catch (\Throwable $th) {
                    //throw $th;
                    $verifica = false;
                    $rta ['response'] = "Token invalido!";
                }

                if(!funciones::validateDate($datos["fecha"]))
                {
                    $verifica = false;
                    $rta ['response'] = "Formato fecha invalido!";
                    
                }
                
                if($verifica)
                {
                    $rta ['status'] = "ok";
                    //busco id usuario
                    $user = Usuario::where('email', $decoded->email)
                    ->first();
                    //guardo evento
                    $evento = new Evento;
                    $evento->fecha = $datos["fecha"];
                    $evento->descripcion = $datos["descripcion"];
                    $evento->usuario_id = $user->id;
                    $rta ['response'] = $evento->save();

                }
                
                $response->getBody()->write(json_encode($rta));
                return $response;
            }
            $rta ['status'] = "Nok";
            $rta ['response'] = "Datos erroneos, Reingrese!";
            $response->getBody()->write(json_encode($rta));
            return $response;
        }
        else
        {
            $rta ['response'] = "Datos erroneos, reingrese!!";
        }
    
        $response->getBody()->write($rta);
        return $response;
    }
    public function modifyEvent(Request $request, Response $response, array $args)
    {
        $rta = array();
        $rta ['status'] = "Nok";
        $rta ['response'] = "";
        //formato de JWT
        $id_modifica = $args["id"];
        $llave = "pro3-parcial";
        //obtengo parametros
        // $datos = $request->getParsedBody();
        $token = $request->getHeader("token")[0];
        // var_dump($token);
        //valido parametros
        // var_dump($datos);
        if(isset($token) && isset($id_modifica))
        {
            //o array empty
            if($token != "" )
            {
                $verifica = true;
                try {
                    // $token = $datos["token"];
                    // var_dump($token);
                    $decoded = JWT::decode($token, $llave, array('HS256'));
                    if ($decoded->tipo != 'user') {
                        $verifica = false;
                        $rta ['response'] = "Tipo de usuario no debe ser ADMIN";
                    }
                } catch (\Throwable $th) {
                    //throw $th;
                    $verifica = false;
                    $rta ['response'] = "Token invalido!";
                } 
                if($verifica)
                {
                    //parametros para guardar foto
                    $rta['response'] = "no existe id de evento";
                    
                    $evento = Evento::where('id', $id_modifica)
                    ->first();
                    if(isset($evento))
                    {
                        $evento->fecha = Date("Y-m-d H:i:s");
                        $rta['status'] = "ok";
                        $rta['response'] = $evento->save();
                    }
                }
                
                $response->getBody()->write(json_encode($rta));
                return $response;
            }
            else{
                $rta ['response'] = "Datos erroneos, Reingrese!";
            }
        }
        else
        {
            $rta ['response'] = "Datos erroneos, reingrese!!";
        }
    
        $response->getBody()->write($rta);
        return $response;
    }


    public function listEvents(Request $request, Response $response, $args)
    {
        //respuesta
        $rta = array();
        $rta ['status'] = "Nok";
        $rta ['response'] = "";
        //formato de JWT
        $llave = "pro3-parcial";
        //obtengo parametros
        $datos = $request->getQueryParams();
        //valido parametros
        // var_dump($datos);
        if(!empty($datos) && isset($datos["token"])  )
        {
            if($datos["token"] != "" )
            {
                $verifica = true;
                try {
                    $token = $datos["token"];
                    // var_dump($token);
                    $decoded = JWT::decode($token, $llave, array('HS256'));
                } catch (\Throwable $th) {
                    //throw $th;
                    $verifica = false;
                    $rta ['response'] = "Token invalido!";
                }      
                if($verifica)
                {
                    $user = Usuario::where('email', $decoded->email)
                    ->first();
                    // ->get();
                    //busco eventos dependiendo tipo de usuario
                    // var_dump($decoded);
                    // var_dump($user);
                    /* 
                    guardar datos del usuario en vez de IDuser

                    */
                    if($user->tipo == 'user')
                    {
                        $usEventos = Evento::where('idUser', $user->id)
                        ->orderByDesc('fecha')
                        ->get();
                        if(empty($usEventos))
                        {
                            $rta ['response'] =  "No hay eventos para el usuario";
                        }
                        else
                        {
                            $rta ['response'] =  $usEventos;
                        }
                    }
                    else
                    {
                        $eventos = Evento::all();
                        // var_dump($eventos);
                        if(empty($eventos) || sizeof($eventos) == 0)
                        {
                            $rta ['response'] =  "No hay eventos para mostrar";

                        }
                        else
                        {
                            // $data = User::select('users.nameUser', 'categories.nameCategory')
                            // ->join('categories', 'users.idUser', '=', 'categories.user_id')
                            // ->get();
                            // echo("no esta vacio");
                            $rta ['status'] = "OK";
                            $rta ['response'] = Evento::select('eventos.*', 'usuarios.id')
                            ->join('usuarios', 'usuarios.id', '=', 'eventos.usuario_id')
                            ->orderBy('fecha', 'DESC')
                            ->get();
                        }
                    }
                }
            }
            else{
                $rta ['response'] = "Datos erroneos, Reingrese!";
            }
        }
        else
        {
            $rta ['response'] = "Datos erroneos, Reingrese!";
        }
    
        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function viewLogs(Request $request, Response $response, $args)
    {
        //respuesta
        $rta = array();
        $rta ['status'] = "Nok";
        $rta ['response'] = "";
        //formato de JWT
        $llave = "pro3-parcial";
        //obtengo parametros
        $datos = $request->getQueryParams();
        //valido parametros
        // var_dump($datos);
        if(!empty($datos) && isset($datos["token"])  )
        {
            if($datos["token"] != "" )
            {
                $verifica = true;
                try {
                    $token = $datos["token"];
                    $decoded = JWT::decode($token, $llave, array('HS256'));
                } catch (\Throwable $th) {
                    //throw $th;
                    $verifica = false;
                    $rta ['response'] = "Token invalido!";
                }      
                if($verifica)
                {
                    //busco usuario de la sesion
                    $user = Usuario::where('email', $decoded->email)
                    ->first();
                    //busco eventos dependiendo tipo de usuario
                    if($user->tipo == 'user')
                    {
                        $rta ['response'] =  "usuario restringido!";
                    }
                    else
                    {
                        $listaLog = Funciones::ListarSerializa('../src/Utils/Files/peticiones.txt');
                        if(empty($listaLog))
                        {
                            $rta ['response'] = "No existen registros de peticiones";
                        }
                        else{
                            $rta ['status'] = "ok";
                            $rta ['response'] = $listaLog;
                        }
                    }
                }
            }
            else{
                $rta ['response'] = "Datos erroneos, Reingrese!";
            }
        }
        else
        {
            $rta ['response'] = "Datos erroneos, Reingrese!";
        }
    
        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function login(Request $request, Response $response, $ar)
    {
        $rta = array();
        $rta ['status'] = "ok";
        $rta ['response'] = "";
        //obtengo parametros
        // var_dump($request);
        // $datos = getallheaders();
        $datos = $request->getParsedBody();
        $token = $request->getHeader("token");
        // var_dump($datos);

        //formato de JWT
        $llave = "pro3-parcial";
        $payload = array(
                "iss" => "http://example.org",
                "aud" => "http://example.com",
                "iat" => 1356999524,
                "nbf" => 1357000000,
                "email" => 'mail@mail.com',
                "tipo" => 'no registrado'
        );

        //valido parametros
        // var_dump($datos);
        if(empty($datos) || (!isset($datos["tipo"]) && !isset($datos["email"])) ||
        !isset($datos["clave"]) )
        {
            $rta ['status'] = "Nok";
            $rta ['response'] = "Datos erroneos, Reingrese!";
            $response->getBody()->write(json_encode($rta));
            return $response;
        }
        else
        {
            //valido existencia
            $usuarios = Usuario::all();
            
            $rta ['status'] = "Nok";
            $rta ['response'] = "email o Clave invalida";
            foreach ($usuarios as $key => $value) 
            {
                if( $value->email == $datos["email"] && $value->clave == $datos["clave"])
                {
                    $rta ['status'] = "ok";
                    $payload["email"] = $value->email;
                    $payload["tipo"] = $value->tipo;
                    $jwt = JWT::encode($payload, $llave);
                    $rta ['response'] = $jwt;
                    $response->getBody()->write(json_encode($rta));
                    return $response;
                }
            }
        }
            $response->getBody()->write(json_encode($rta));
            return $response;
        }

    

    public function add(Request $request, Response $response, $args)
    {
        $user = new Usuario;
        $user->usuario = "Eloquent";
        $user->legajo = 152466;
        $user->localidad = 2;
        $user->cuatrimestre = 3;


        $rta = json_encode(array("ok" => $user->save()));
        // $response->getBody()->write("hola estoy en routes.php \n");
        $response->getBody()->write($rta);
        return $response;
    }
    public function logup(Request $request, Response $response, $args)
    {
        $rta = array();
        $rta ['status'] = "ok";
        $rta ['response'] = "";
        //obtengo parametros
        $datos = $request->getQueryParams();

        //valido parametros
        // var_dump($datos);
        if(empty($datos) || !isset($datos["nombre"]) || !isset($datos["email"]) ||
        !isset($datos["clave"]) || !isset($datos["tipo"]) || ($datos["tipo"] != "admin" &&
        $datos["tipo"] != "user") || strlen( $datos["clave"]) < 4 )
        {
            $rta ['status'] = "Nok";
            $rta ['response'] = "Datos erroneos, Reingrese!";
            $response->getBody()->write(json_encode($rta));
            return $response;
        }

        //valido claves en BD
        $users = Usuario::all();
        foreach ($users as $key => $value) {
            if($value->email == $datos["email"])
            {
                // var_dump($value);
                $rta ['status'] = "Nok";
                $rta ['response'] = "El usuario ya existe";
                $response->getBody()->write(json_encode($rta));
                return $response;
            }
        }
        //creo usuario
        $usuario = new Usuario;
        $usuario->email = $datos["email"];
        $usuario->nombre = $datos["nombre"];
        $usuario->clave = $datos["clave"];
        $usuario->tipo = $datos["tipo"];


        $rta ['response'] = $usuario->save();

        $response->getBody()->write(json_encode($rta));
        
        
        
        // $response->getBody()->write("ruta cheta");
        return $response;
    }
    public function addImage(Request $request, Response $response, array $args)
    {
        // $rta = json_encode(array("ok" => $usuario->save()));

        // $files = $request->getUploadedFiles();
        $rta = array();
        $rta ['status'] = "Nok";
        $rta ['response'] = "";
        //formato de JWT
        $llave = "pro3-parcial";
        //obtengo parametros
        $datos = $request->getParsedBody();
        var_dump($datos);
        //valido parametros
        // var_dump($datos);
        if(!empty($datos) && isset($datos["foto"]) && isset($datos["token"])  )
        {
            //o array empty
            if($datos["foto"] != "" && $datos["token"] != "" )
            {
                $verifica = true;
                try {
                    $token = $datos["token"];
                    var_dump($token);
                    $decoded = JWT::decode($token, $llave, array('HS256'));
                    if ($decoded->tipo != 'user') {
                        $verifica = false;
                        $rta ['response'] = "Tipo de usuario no debe ser ADMIN";
                    }
                } catch (\Throwable $th) {
                    //throw $th;
                    $verifica = false;
                    $rta ['response'] = "Token invalido!";
                } 
                if($verifica)
                {
                    $foto = $datos['foto'];
                    //obtengo path foto y guardo
                    //parametros para guardar foto
                    $fotoName = $foto['name'];
                    $path = $foto['tmp_name'];
                    $destino = "/../src/Utils/Images";
                    $user = Usuario::where('email', $decoded->email)
                    ->get();
                    $destiny = funciones::GuardaTemp($path, $destino, $fotoName, $user->email); 
                    if($destino != $destiny)
                    {
                        $rta ['status'] = "ok";
                        $user->foto = $destiny;
                        $rta ['response'] = $user->save();
                    }
                    else
                    {
                        $response["data"] = 'error al subir imagen de usuario';
                    }
                }
                
                $response->getBody()->write(json_encode($rta));
                return $response;
            }
            else{
                $rta ['response'] = "Datos erroneos, Reingrese!";
            }
        }
        else
        {
            $rta ['response'] = "Datos erroneos, reingrese!!";
        }
    
        $response->getBody()->write($rta);
        return $response;
    
    }

    public function saveCripto(Request $request, Response $response, $args)
    {
        $rta = array();
        $rta ['status'] = "Nok";
        $rta ['response'] = "";
        //formato de JWT
        $llave = "pro3-parcial";
        $files = $request->getUploadedFiles();
        //obtengo parametros
        $datos = $request->getParsedBody();
        $token = $request->getHeader("token")[0];
        //valido parametros
        if(!empty($datos) && isset($datos["nombre"]) && isset($datos["precio"]) &&
        isset($token)  && isset($datos["nacionalidad"]) && !empty($files) )
        {
            if($datos["nombre"] != "" && $datos["precio"]  && $datos["nacionalidad"] != "" && $token != "" )
            {
                $verifica = true;
                try {
                    // $token = $datos["token"];
                    // var_dump($token);
                    $decoded = JWT::decode($token, $llave, array('HS256'));
                    if ($decoded->tipo != 'admin') {
                        $verifica = false;
                        $rta ['response'] = "Tipo de usuario no debe ser USER";
                    }
                } catch (\Throwable $th) {
                    //throw $th;
                    $verifica = false;
                    $rta ['response'] = "Token invalido!";
                }
                
                if($verifica)
                {
                    //busco id usuario
                    $cripto = Cripto::where('nombre', $datos["nombre"])
                    ->first();
                    //guardo evento
                    if(isset($cripto->nombre))
                    {
                        // var_dump($datos);
                        $rta ['response'] = "cripto no en base";
                    }
                    else
                    {
                        // var_dump($datos);
                        
                        // echo("voy a guardar");
                        // var_dump($files["foto"]);
                        // $foto = $files['foto'];
                        // //obtengo path foto y guardo
                        // //parametros para guardar foto
                        // var_dump($foto);
                        // $fotoName = $foto["name"];
                        // $path = $foto["file"];
                        // echo $fotoName;
                        // echo $path;
                        // $destino = "/../src/Utils/Images";
                        // $destiny = funciones::GuardaTemp($path, $destino, $fotoName, $datos["nombre"]); 
                        // if($destino != $destiny)
                        // {
                            // $rta ['status'] = "ok";
                            // $user->foto = $destiny;
                            // $rta ['response'] = $user->save();
                            $rta['status'] = "ok";
                            $cripto = new Cripto;
                            $cripto->precio = $datos["precio"];
                            $cripto->nombre = $datos["nombre"];
                            $cripto->foto = "../Utils/fotito.jpg";
                            $cripto->nacionalidad = $datos["nacionalidad"];
                            $rta['response'] = $cripto->save();
                        // }
                        // else
                        // {
                        //     $rta["response"] = 'error al subir imagen de cripto';
                        // }
                    }

                }
            }
            $rta ['response'] = "Datos erroneos, Reingrese!";
        }
        else
        {
            $rta ['response'] = "Datos erroneos, reingrese!!";
        }
    
        $response->getBody()->write(json_encode($rta));
        return $response;
    }
}
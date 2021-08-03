<?php
namespace Config;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\AlumnosController;
use App\Controllers\UsuariosController;
use App\Controllers\CriptoController;
return function ($app)
{
    

    $app->group('/alumnos', function(RouteCollectorProxy $group){
        $group->get('[/]', AlumnosController::class.':getAll');
        $group->get('/:id', AlumnosController::class.':getAll');
        $group->post('[/]', AlumnosController::class.':add');
        $group->put('/:id', AlumnosController::class.':getAll');
        $group->delete('/:id', AlumnosController::class.':getAll');
    });

    $app->group('/user', function(RouteCollectorProxy $group){
        // $group->post('/users/{imagen}', UsuariosController::class.':addImage');   
        $group->post('[/:email/:tipo/:clave]', UsuariosController::class.':login');
        // $group->post('[/:clave/:email/:nombre]', UsuariosController::class.':login');
    });

    $app->group('/login', function(RouteCollectorProxy $group){
        // $group->post('[/:email/:nombre/:clave/:tipo]', UsuariosController::class.':logup');
        $group->post('[/:clave/:email/:nombre]', UsuariosController::class.':login');
    });

    $app->group('/cripto', function(RouteCollectorProxy $group){
        // $group->post('/alta[/:fecha/:descripcion]', CriptoController::class.':saveCripto');
        $group->post('/alta[/:precio/:nombre/:foto/:nacionalidad]', CriptoController::class.':saveCripto');
        $group->get('[/]', CriptoController::class.':listCriptos');
        $group->get('/{id}', CriptoController::class.':traerCripto');
        // $group->put('/{id}', UsuariosController::class.':modifyEvent');
    });

    $app->group('/logs', function(RouteCollectorProxy $group){
        // $group->post('[/:email/:nombre/:clave/:tipo]', UsuariosController::class.':logup');
        $group->get('[/:token]', UsuariosController::class.':viewLogs');
    });

};
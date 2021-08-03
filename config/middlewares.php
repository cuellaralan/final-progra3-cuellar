<?php
namespace Config;
use Slim\App;
use App\Middleware\BeforeMiddleware;
// use App\Middleware\AlumnoValidateMiddleware;
use App\Middleware\AfterMiddleware;
use App\Middleware\PeticionMiddleware;

return function(App $app)
{
    $app->addBodyParsingMiddleware();

    $app->add(new BeforeMiddleware());
    // $app->add(new AlumnoValidateMiddleware());
    // $app->add(BeforeMiddleware::class);
    $app->add(new PeticionMiddleware());
    $app->add(new AfterMiddleware());
};

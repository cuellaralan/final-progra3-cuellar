<?php
require_once __DIR__ . "/../vendor/autoload.php";
use Slim\Factory\AppFactory;
use Config\DataBase;
//interface de error handler
use Psr\Http\Message\ServerRequestInterface;

new DataBase();

$app = AppFactory::create();
// $app->setBasePath("/progra3/final-prueba/public");
$app->setBasePath("/progra3/final-progra3-cuellar/public");

$app->addRoutingMiddleware();

// Define Custom Error Handler
$customErrorHandler = function (
    ServerRequestInterface $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails
) use ($app) {
    // $logger->error($exception->getMessage());

    $payload = ['error' => $exception->getMessage()];

    $response = $app->getResponseFactory()->createResponse();
    $response->getBody()->write(
        json_encode($payload, JSON_UNESCAPED_UNICODE)
    );

    return $response;
};

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler($customErrorHandler);

/*Registrar rutas */
(require_once __DIR__ .'/../config/routes.php')($app);

/*Registrar Middlewares */
(require_once __DIR__ .'/../config/middlewares.php')($app);

return $app;




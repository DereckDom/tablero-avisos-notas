<?php
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Slim\Middleware\ErrorMiddleware;
use Slim\Middleware\BodyParsingMiddleware;

$app = AppFactory::create();

// Middleware para procesar JSON
$app->addBodyParsingMiddleware();

// Middleware para mostrar errores detallados
$app->addErrorMiddleware(true, true, true);

// Cargar rutas
(require __DIR__ . '/../src/routes.php')($app);

// Ejecutar aplicación
$app->run();

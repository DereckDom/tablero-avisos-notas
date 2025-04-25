<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    $app->get('/', function (Request $request, Response $response) {
        $archivo = '/var/www/html/public/app.html';
        if (!file_exists($archivo)) {
            $response->getBody()->write("Archivo app.html no encontrado");
            return $response->withStatus(500);
        }
        $contenido = file_get_contents($archivo);
        $response->getBody()->write($contenido);
        return $response;
    });

    $app->group('/notas', function (RouteCollectorProxy $group) {

        $group->get('', function (Request $request, Response $response) {
            $archivo = '/var/www/html/data/datos.json';
            $datos = file_exists($archivo) ? json_decode(file_get_contents($archivo), true) : [];
            $response->getBody()->write(json_encode($datos));
            return $response->withHeader('Content-Type', 'application/json');
        });

        $group->post('', function (Request $request, Response $response) {
            $archivo = '/var/www/html/data/datos.json';
            $datos = file_exists($archivo) ? json_decode(file_get_contents($archivo), true) : [];
            $body = $request->getParsedBody();

            $nuevaNota = [
                'id' => count($datos) ? max(array_column($datos, 'id')) + 1 : 1,
                'tipo' => $body['tipo'] ?? 'nota',
                'mensaje' => $body['mensaje'] ?? ''
            ];

            $datos[] = $nuevaNota;
            file_put_contents($archivo, json_encode($datos, JSON_PRETTY_PRINT));
            $response->getBody()->write(json_encode($nuevaNota));
            return $response->withHeader('Content-Type', 'application/json');
        });

        $group->delete('/{id}', function (Request $request, Response $response, array $args) {
            $archivo = __DIR__ . '/../data/datos.json';
            $datos = file_exists($archivo) ? json_decode(file_get_contents($archivo), true) : [];
            $id = (int)$args['id'];
            $encontrada = false;
        
            $nuevosDatos = array_filter($datos, function ($n) use ($id, &$encontrada) {
                if ($n['id'] === $id) {
                    $encontrada = true;
                    return false;
                }
                return true;
            });
        
            file_put_contents($archivo, json_encode(array_values($nuevosDatos), JSON_PRETTY_PRINT));
        
            if ($encontrada) {
                $response->getBody()->write('Nota eliminada');
                return $response->withStatus(200)->withHeader('Content-Type', 'text/plain');
            } else {
                $response->getBody()->write('Nota no encontrada');
                return $response->withStatus(404)->withHeader('Content-Type', 'text/plain');
            }
        });
        

        $group->put('/{id}', function (Request $request, Response $response, array $args) {
            $archivo = __DIR__ . '/../data/datos.json';
            $datos = file_exists($archivo) ? json_decode(file_get_contents($archivo), true) : [];
            $id = (int)$args['id'];
            $body = $request->getParsedBody();
            $actualizado = false;
        
            foreach ($datos as &$nota) {
                if ($nota['id'] === $id) {
                    $nota['mensaje'] = $body['mensaje'] ?? $nota['mensaje'];
                    $actualizado = true;
                    break;
                }
            }
        
            file_put_contents($archivo, json_encode($datos, JSON_PRETTY_PRINT));
        
            if ($actualizado) {
                $response->getBody()->write('Nota actualizada');
                return $response->withStatus(200)->withHeader('Content-Type', 'text/plain');
            } else {
                $response->getBody()->write('Nota no encontrada');
                return $response->withStatus(404)->withHeader('Content-Type', 'text/plain');
            }
        });
        
    });
};

<?php
    namespace App\Controllers;
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Slim\Routing\RouteCollectorProxy;
    
    $app->group('/api', function (RouteCollectorProxy $api) {
        $api->group('/producto', function(RouteCollectorProxy $producto) {
            $producto->get('/read[/{id}]', Producto::class . ':read'); //El id es opcional, si no se pasa, se traen todos los productos
            $producto->post('', Producto::class . ':create');
            $producto->put('/{id}', Producto::class . ':update'); //El id es obligatorio, se pasa por la url
            $producto->delete('/{id}', Producto::class . ':delete'); //El id es obligatorio, se pasa por la url
            $producto->get('/filtrar', Producto::class . ':filtrar'); 
        });
    });

    //Artefacto
    $app->group('/api', function (RouteCollectorProxy $api) {
        $api->group('/artefacto', function(RouteCollectorProxy $artefacto) {
            $artefacto->get('/read[/{id}]', Artefacto::class . ':read'); //El id es opcional, si no se pasa, se traen todos los productos
            $artefacto->post('', Artefacto::class . ':create');
            $artefacto->put('/{id}', Artefacto::class . ':update'); //El id es obligatorio, se pasa por la url
            $artefacto->delete('/{id}', Artefacto::class . ':delete'); //El id es obligatorio, se pasa por la url
            $artefacto->get('/filtrar', Artefacto::class . ':filtrar'); 
        });
    });
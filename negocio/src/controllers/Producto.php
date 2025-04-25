<?php
    namespace App\Controllers;

    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Container\ContainerInterface;

    use PDO;

    class Producto{
        protected $container;

        //Constructor
        public function __construct(ContainerInterface $c){
            $this->container = $c;
        }

        public function read(Request $request, Response $response, $args){
            
            $res = ["datos" => "leídos"];
            status = 200; //Contamos la cantidad de filas que devuelve la consulta
            
            $response->getBody()->write(json_encode($res)); //Retorna la respuesta en formato JSON
            
            return $response
                ->withHeader('Content-Type', 'application/json') //Retorna el tipo de contenido en formato JSON
                ->withStatus($status); //Retorna el status de la consulta, 200 si se encuentra el producto, 204 si no se encuentra
        }

        public function create(Request $request, Response $response, $args){
            
            $body = json_decode($request->getBody()); //Obtenemos el body de la consulta y lo decodificamos a un array
          
            $res = $body;
            
            

            $status = 200; //Contamos la cantidad de filas que devuelve la consulta

            return $response
                ->withHeader('Content-Type', 'application/json') //Retorna el tipo de contenido en formato JSON
                ->withStatus($status); //Retorna el status de la consulta, 200 si se encuentra el producto, 204 si no se encuentra
        }
        
    }
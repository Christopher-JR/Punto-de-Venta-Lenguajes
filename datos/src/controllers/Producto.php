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
            
            $sql = "SELECT * FROM productos "; //Consulta SQL para obtener el producto por id, dejar el espacio en blanco
            
            if(isset($args["id"])){
                $sql .= "WHERE id = :id "; //Si existe el id, lo agregamos a la consulta, dejamos el espacio en blanco para que no de error
                
            }

            $sql .= "LIMIT 0, 5"; //Limitamos la consulta a 5 productos, si no se pasa el id, se traen todos los productos

            $con = $this->container->get('base_datos'); //base_datos es el parametro que se le pasa al contenedor en conexion.php

            $query = $con->prepare($sql); //Preparamos la consulta, Evitamos una injección sql con prepare. Acá usamos PDO

            if(isset($args["id"])){
                $query->execute(["id" => $args["id"]]); //Ejecutamos la consulta
            } else {
                $query->execute(); //Ejecutamos la consulta sin el id
            }

            $res = $query->fetchAll();

            $status = $query->rowCount() > 0 ? 200 : 204; //Contamos la cantidad de filas que devuelve la consulta

            $query = null; //Liberamos la consulta para que no consuma memoria
            $con = null; //Cerramos la conexión a la base de datos
            
            $response->getBody()->write(json_encode($res)); //Retorna la respuesta en formato JSON
            
            return $response
                ->withHeader('Content-Type', 'application/json') //Retorna el tipo de contenido en formato JSON
                ->withStatus($status); //Retorna el status de la consulta, 200 si se encuentra el producto, 204 si no se encuentra
        }

        public function create(Request $request, Response $response, $args){
            $body = json_decode($request->getBody()); //Obtenemos el body de la consulta y lo decodificamos a un array
          
            $res = $body;
            
            $response->getBody()->write(json_encode($res)); //Retorna la respuesta en formato JSON

            $status = 200; //Contamos la cantidad de filas que devuelve la consulta

            return $response
                ->withHeader('Content-Type', 'application/json') //Retorna el tipo de contenido en formato JSON
                ->withStatus($status); //Retorna el status de la consulta, 200 si se encuentra el producto, 204 si no se encuentra
        }
        
    }
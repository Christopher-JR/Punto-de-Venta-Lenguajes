<?php
    namespace App\Controllers;

    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Container\ContainerInterface;

    use PDO;

    class Artefacto{
        protected $container;

        //Constructor
        public function __construct(ContainerInterface $c){
            $this->container = $c;
        }

        public function read(Request $request, Response $response, $args){
            
            $sql = "SELECT * FROM artefacto "; //Consulta SQL para obtener el artefacto por id, dejar el espacio en blanco
            
            if(isset($args["id"])){
                $sql .= "WHERE id = :id "; //Si existe el id, lo agregamos a la consulta, dejamos el espacio en blanco para que no de error
                
            }

            $sql .= "LIMIT 0, 5"; //Limitamos la consulta a 5 artefactos, si no se pasa el id, se traen todos los artefactos

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

        public function create(Request $request, Response $response, $args) {
            $body = json_decode($request->getBody());
            $campos = "";
            $params = "";
    
            foreach ($body as $key => $value) {
                $campos .= $key . ", ";
                $params .= ":" . $key . ", ";
            }
    
            $campos = substr($campos, 0, -2); 
            $params = substr($params, 0, -2); 
    
            $sql = "INSERT INTO artefacto($campos) VALUES ($params);";
    
            $con = $this->container->get('base_datos');
            $con->beginTransaction();
            $query = $con->prepare($sql);
    
            foreach($body as $key => $value){
                $TIPO = gettype($value)=="integer" ? PDO::PARAM_INT : PDO::PARAM_STR;
                $query->bindValue($key, $value, $TIPO);
            }
    
            try {
                $query->execute();
                $con->commit();
                $status = 201;
            } catch (PDOException $e) {
                $status = $e->getCode() == 23000 ? 409 : 500;
                $con->rollback();
            }
    
            $query = null; 
            $con = null; 
    
            return $response->withStatus($status);
        }

        public function update(Request $request, Response $response, $args){
            
            $body = json_decode($request->getBody()); //Obtenemos el body de la consulta y lo decodificamos a un array
            $campos = ""; //En campos almacenamos la cadena sql de los campos
            $id = $args["id"]; //Obtenemos el id del producto a actualizar

            //Si el body tiene el id, lo eliminamos para no actualizarlo
            if(isset($body->id)){
                unset($body->id); 
            }

            //Si el body tiene el id, lo eliminamos para no actualizarlo
            if(isset($body->codigo_producto)){
                unset($body->codigo_producto); 
            }

            //Creamos la consulta sql para insertar el producto, con los campos y valores del body
            $sql = "UPDATE artefacto SET "; 
            
            //Agregamos los campos del body a la consulta, separados por comas
            foreach ($body as $key => $value) {
                $sql .= "$key = :$key, "; 
            };

            $sql = substr($sql, 0, -2); //Eliminamos la última coma, el espacio en blanco y agregamos la paréntesis de cierre de la consulta

            $sql .= " WHERE id = :id;"; //Agregamos el id a la consulta para actualizar el producto
            
            $con = $this->container->get('base_datos'); //base_datos es el parametro que se le pasa al contenedor en conexion.php
            $query = $con->prepare($sql); //Preparamos la consulta, Evitamos una injección sql con prepare. Acá usamos PDO
            
            
            foreach ($body as $key => $value) {
                $TIPO = gettype($value) == 'integer' ? PDO::PARAM_INT : PDO::PARAM_STR; //Verificamos el tipo de dato del valor, si es entero o string
                $value = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS); //Sanitizamos el valor para evitar inyecciones sql
                $query->bindValue($key, $value, $TIPO); //Agregamos los valores del body a la consulta, separados por comas
            }

            $query->bindValue("id", $id, PDO::PARAM_INT); //Agregamos el id a la consulta para actualizar el producto ya que fua eliminado del body

            $query->execute(); //Ejecutamos la consulta            
            $status = $query->rowCount() > 0 ? 200 : 204;
            $query = null; //Liberamos la consulta para que no consuma memoria
            $con = null; //Cerramos la conexión a la base de datos
            
            return $response->withStatus($status); //Retorna el status de la consulta, 201 si se creo el producto, 409 si conflicto
        }

        public function delete(Request $request, Response $response, $args){
                        
            $sql = "DELETE FROM artefacto WHERE id = :id;"; //Creamos la consulta sql para eliminar el producto
            
            $con = $this->container->get('base_datos'); //base_datos es el parametro que se le pasa al contenedor en conexion.php
            $query = $con->prepare($sql); //Preparamos la consulta, Evitamos una injección sql con prepare. Acá usamos PDO
            
            $query->bindValue("id", $args["id"], PDO::PARAM_INT); //Agregamos el id a la consulta para eliminar el producto

            $query->execute(); //Ejecutamos la consulta            
            $status = $query->rowCount() > 0 ? 200 : 404;
            $query = null; //Liberamos la consulta para que no consuma memoria
            $con = null; //Cerramos la conexión a la base de datos
            
            return $response->withStatus($status); //Retorna el status de la consulta, 200 si se elimino el producto, 204 si no se elimino
        }
        
        public function filtrar(Request $request, Response $response, $args){

            $datos = $request->getQueryParams(); //Obtenemos los datos de la consulta y los decodificamos a un array
           
            $sql = "SELECT * FROM artefacto WHERE "; //Consulta SQL para obtener el producto por id, dejar el espacio en blanco
            
            //Recorremos el array de datos
            foreach($datos as $key => $value){ 
                $sql .= "$key LIKE :$key AND "; 
            }
            $sql = rtrim($sql, 'AND ') . ";"; //Eliminamos la última coma, el espacio en blanco y agregamos la paréntesis de cierre de la consulta
            
            //$sql .= "LIMIT 0, 5"; //Limitamos la consulta a 5 productos, si no se pasa el id, se traen todos los productos

            $con = $this->container->get('base_datos'); //base_datos es el parametro que se le pasa al contenedor en conexion.php

            $query = $con->prepare($sql); //Preparamos la consulta, Evitamos una injección sql con prepare. Acá usamos PDO

            foreach($datos as $key => $value){ //Recorremos el array de datos
               $query->bindValue(":$key", "%$value%", PDO::PARAM_STR); //Agregamos los valores del body a la consulta, separados por comas
            }

            $query->execute(); //Ejecutamos la consulta sin el id


            $res = $query->fetchAll();
            $status = $query->rowCount() > 0 ? 200 : 204; //Contamos la cantidad de filas que devuelve la consulta
            $query = null; //Liberamos la consulta para que no consuma memoria
            $con = null; //Cerramos la conexión a la base de datos
            
            $response->getBody()->write(json_encode($res)); //Retorna la respuesta en formato JSON
            
            return $response
                ->withHeader('Content-Type', 'application/json') //Retorna el tipo de contenido en formato JSON
                ->withStatus($status); //Retorna el status de la consulta, 200 si se encuentra el producto, 204 si no se encuentra
        }
    }
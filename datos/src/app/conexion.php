<?php
    use Psr\Container\ContainerInterface;

    $container->set('base_datos', function(ContainerInterface $c){
        $config = $c->get('config_bd'); // Obtenemos la configuracion de la BD hecha en el config.php para poder hacer la conexión
        // Creamos la conexión a la BD
        $opc = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ];
        $dsn = "mysql:host=$config->host;dbname=$config->db;charset=$config->charset";

        try {
            $con = new PDO($dsn, $config->user, $config->passw, $opc);
            //die('Conectado a la base de datos');
        } catch (PDOException $e) {
            print('Error'.$e->getMessage().'<br>');
            die("Error conectando a la base de datos"); //Quitar esta línea en producción
        }
        return $con;
    });
<?php
    // Configuracion para poder hacer la conxeión a la BD
    // Dentro del container Inyección de dependencias
    $container->set('config_bd', function(){
        return(object) [
            "host" => "db",
            "db" => "ventas",
            "user" => "root",
            "passw" => "12345",
            "charset" => "utf8mb4"
        ];
    });
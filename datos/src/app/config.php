<?php
    // Configuracion para poder hacer la conxeión a la BD
    // Dentro del container Inyección de dependencias     ventas
    $container->set('config_bd', function(){
        return(object) [
            "host" => "db",
            "db" => "taller",
            "user" => "root",
            "passw" => "12345",
            "charset" => "utf8mb4"
        ];
    });
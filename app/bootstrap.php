<?php
    
    require_once "config/config.php";
    require_once "helpers/url_helper.php";

    // require_once "libraries/Controller.php";
    // require_once "libraries/Core.php";
    // require_once "libraries/Database.php";
    
    // Autoload Libraries (Class Name must match File Name)
    spl_autoload_register(function($className) {
        require_once "libraries/" . $className . ".php";
    });
?>
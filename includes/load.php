<?php 
    /**
     * Define Root Directory for 'inc' includes
     */
        define("INC_ROOT_DIR",realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);      

    /**
     * This will be required in every script.
     * This will contain all the necessary scripts
     */
     require_once INC_ROOT_DIR.'script'.DIRECTORY_SEPARATOR.'constant.php';
     require_once INC_ROOT_DIR.'script'.DS.'session.php';
     require_once INC_ROOT_DIR.'script'.DS.'def_time.php';
     require_once INC_ROOT_DIR.'script'.DS.'config.php';
     require_once INC_ROOT_DIR.'script'.DS.'function.php';
     require_once INC_ROOT_DIR.'script'.DS.'auth.php';
     require_once INC_ROOT_DIR.'db'.DS.'db.php';
     require_once INC_ROOT_DIR.'db'.DS.'db_connect.php';
         
?>
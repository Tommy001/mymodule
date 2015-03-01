<?php
namespace Tommy001\Mymodule;

define('ANAX_INSTALL_PATH', realpath(__DIR__ . '/../vendor/anax/mvc') . '/');
define('ANAX_APP_PATH',     ANAX_INSTALL_PATH . 'app/');

include __DIR__ . "/../autoloader.php";
include __DIR__ . "/../vendor/anax/mvc/app/config/autoloader.php";

function is_uploaded_file($filename) {
//Check only if file exists
return file_exists($filename);
}

function move_uploaded_file($filename, $destination){
    //Copy file
    return copy($filename, $destination);
}



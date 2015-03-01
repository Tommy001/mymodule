<?php 
/**
 * This is a Anax pagecontroller.
 *
 */

// Get environment & autoloader and the $app-object.
require __DIR__.'/config_with_app.php'; 

$di->set('UploadController', function() use ($di) {
    $upload = new Tommy001\Mymodule\UploadController();
    $upload->setDI($di);
    return $upload;
});

// Set the title of the page
$app->theme->setVariable('title', "My Module");
    
$app->router->add('', function() use ($app) {

    $app->theme->setTitle("Mymodule");
    $app->views->add('mymodule/uploadform',[]);    
    
});

    

// Render the response using theme engine.
$app->router->handle();
$app->theme->render();

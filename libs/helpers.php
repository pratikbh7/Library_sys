<?php
//PATH is not defined in the instance of ajax submission
define( 'PATH', '/var/www/html');
spl_autoload_register( function($class){
    $namespace = str_replace("\\","/",__NAMESPACE__);
    $classname = str_replace("\\","/",$class);
    $class = PATH . '/' . ( empty($namespace) ? "" : $namespace . "/" ) . "{$classname}.class.php";
    include($class);
});
function is_post_request(): bool{
    return strtoupper($_SERVER['REQUEST_METHOD']) === 'POST';
}

function is_get_request(): bool{
    return strtoupper($_SERVER['REQUEST_METHOD']) === 'GET';
}

function redirect_to( $url ){
   header( 'Location:' . $url );
   exit;
}

function test(){
    echo "gg";
}
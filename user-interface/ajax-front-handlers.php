<?php
//ajax form submission
require_once  '/var/www/html/libs/helpers.php';
use includes\Database;
use includes\Authorization;
$init = Database::get_db_instance();
$link = $init->db_link();
if( is_post_request() ){
    $post_data = $_POST;
    $auth = new Authorization($link);
    if( isset($post_data['login_input'])){
        $authorize = $auth->login_user($post_data['login_input']);
        $response = new stdClass();
        if( $authorize === true ){
            $response->status = "authorized"; 
            echo json_encode($response);
        }
        else{
            $response->status = "unauthorized";
            $response->errors = $authorize;
            echo json_encode($response);
        }
    } 
    else if( isset($post_data['register_input'])){
        $auth->register_user($post_data['register_input']);
    }
    else{
        die( 'Invalid server request' );
    }
}
?>
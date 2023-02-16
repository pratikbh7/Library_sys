<?php
//ajax form submission
require_once  '/var/www/html/libs/helpers.php';
//only post requests
if( is_get_request() ){
    view('youshallnotpass');
    exit;
}

if( is_post_request() ){
    $post_data = $_POST;
    $response = new stdClass();
    if( isset($post_data['login_input'])){
        $authorize = $authorization->login_user($post_data['login_input']);
        if( $authorize['errors'] === false ){
            session_start();
            session_regenerate_id();
            $test = HelperClass::log_admin_in( $authorize );
            $response->test = $test;
            $response->status = "authorized"; 
            echo json_encode($response);
        }
        else if( $authorize['errors'] === true ){
            $response->status = "unauthorized";
            unset($authorize['errors']);
            $response->errors = $authorize;
            echo json_encode($response);
        }
    } 
    else if( isset($post_data['installation_data'])){
        $authorize = $authorization->register_user($post_data['insatallation_data']);
        if( $authorize['errors'] === false ){
            HelperClass::register_admin_data( $authorize['username'], $authorize['password'], 1, 'LBADMIN' );
            $response->status = "success";
            echo json_encode($response);
        }
        else if( $authorize['errors'] === true ){
            $response->status = "failure";
            $response->errors = $authorize;
            echo json_encode($response);
        }
    }
    else{
        die( 'Invalid server request' );
    }
}
?>
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
            log_admin_in( $authorize );
            $response->status = "authorized"; 
            echo json_encode($response);
        }
        else if( $authorize['errors'] === true ){
            $response->status = "unauthorized";
            $response->errors = $authorize;
            echo json_encode($response);
        }
    } 
    else if( isset($post_data['installation_data'])){
        if( register_user_data( $post_data['installation_data']['username'], $post_data['installation_data']['password'], 1 )){
            $response->status = "success";
            echo json_encode($response);
        }
        else{
            $response->status = "failure";
            echo json_encode($response);
        }
    }
    else{
        die( 'Invalid server request' );
    }
}
?>
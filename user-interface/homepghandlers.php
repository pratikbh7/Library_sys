<?php
require_once '/var/www/html/libs/helpers.php';
if( is_post_request()){
    if( is_admin_logged_in() && is_admin() ){
        $post_data = $_POST['action'];
        $callback = strtolower($post_data);
        $allowed_calls = [ 'list', 'add', 'issue', 'return', 'delete'];
        if( in_array( $callback, $allowed_calls )){
            $post_data = $post_data === 'list' ? $post_data . '_books' : $post_data . '_book';
            if( is_callable(array($homepage_ajax, $post_data))){
                $response = new stdClass();
                $perform_action = $homepage_ajax->$post_data;
                if( $perform_action['errors'] === false  ){
                    $response->status="success";
                }
                else if( $perform_action['errors'] === true){
                    $response->status = "failure";
                    $response->error = $perform_action['the_error'];
                }
            }   
        }
    }
}
else if( is_get_request()){
    require_once '/var/www/html/components/youshallnotpass.php';
    exit;
}
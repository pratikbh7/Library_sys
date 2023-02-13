<?php
require_once '/var/www/html/libs/helpers.php';
if( is_post_request()){
    session_start();
    if( is_admin_logged_in() && is_admin() ){
        $post_action = $_POST['action'];
        $callback = strtolower($post_action);
        $dbupdate_calls = [ 'add', 'delete', 'issue', 'return' ];
        $response = new stdClass();
        if( in_array( $callback, $dbupdate_calls)){
            $post_data = $_POST['data'];
            $callback = $callback . '_book';
            foreach( $post_data as $key => $value){
                $post_data[$key] = filter_var($value, FILTER_SANITIZE_STRING );
            }
            if( is_callable(array($homepage_ajax, $callback))){
                if( $callback === 'add_book' ){
                    if( $homepage_ajax->duplication_check($post_data)){
                        if( $homepage_ajax->$callback($post_data)){
                            $status = "success";
                        }
                        else{
                            $status = "failure";
                        }
                    }
                    else{
                        $response->message = "exists";
                    }
                }
            }
            $response->status = $status;
            echo json_encode($response);
        }
        else{
            $response->status = "invalid call";
            echo json_encode($response);
        }  
    }
}
else if( is_get_request()){
    require_once '/var/www/html/components/youshallnotpass.php';
    exit;
}
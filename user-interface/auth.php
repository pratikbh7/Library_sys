<?php
class authorization{

    private $db_instance;

    public function __construct(){
        $this->db_instance = Database :: get_db_instance();   
    }

    public function add_admin( $data ){

    }

    public function admin_login( $data ){

    }

    public function db_error(){

    }
}
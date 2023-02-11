<?php
namespace includes;

class Authorization{

    private $post_data;

    private $link;

    private $user_col = false;

    private $validation_errors =[
        'required' => 'The %s is required',
        'min' => 'The %s must have at least %s characters',
        'max' => 'The %s must have at most %s characters',
        'between' => 'The %s must have between %d and %d characters',
        'alphanumeric' => 'The %s should have only letters and numbers',
        'unique' => 'The %s already exists',
        'valid_user'  => 'The %s is invalid',
        'valid_pass'  => 'The %s is invalid'
    ];

    public function __construct( $link ){
        $this->link = $link;
    }

    //this is for adding new users through the admin page
    public function register_user($post_data){
        $fields = [ 'username' => 'string | required | unique: init_library_user, Username | between: 3,25 | alphanumeric',
                    'password' => 'string | required' ];
        [ $inputs, $errors ] = $this->sanitize_validate( $post_data, $fields );
        if( $errors ){
            return $errors;
        }
        else{
            return true;
        }
    }

    public function login_user($post_data){
        $fields = [ 'username' => 'string | required | valid_user: init_library_user, Username',
                    'password' => 'string | required | valid_pass: init_library_user, Password' ];
        [ $inputs, $errors ] = $this->sanitize_validate( $post_data, $fields );
        if( $errors ){
            $errors[ 'errors' ] = true;
            return $errors;
        }
        else{
            $inputs[ 'userid' ] = $this->user_col['id'];
            $inputs[ 'errors' ] = false;
            return $inputs;
        }
    }

    private function sanitize_validate( $data, $fields){
        $inputs = array();
        $errors = array();
        foreach( $data as $key => $value){
            $inputs[] = filter_var( $value, FILTER_SANITIZE_STRING ); //sanitization
            $get_options = array_map( 'trim', explode(" | ", $fields[$key]) );
            foreach( $get_options as $option ){
                $params= [];
                if( strpos($option, ':')){
                    [ $option, $params ] = array_map( 'trim', explode(":", $option) );
                    $params = array_map( 'trim', explode(',', $params));
                }
                $rule = 'is_' . $option;
                if( is_callable(array($this, $rule ))){
                    $validate = $this->$rule($data, $key, ...$params);
                    if( !$validate ){
                        $errors[] = sprintf($this->validation_errors[$option],$key,...$params);
                    }
                }
            }
        }
        return [ $inputs, $errors ];
    }

    public function is_required(array $data, string $field): bool{
        return isset($data[$field]) && trim($data[$field]) !== '';
    }

    public function is_min(array $data, string $field, int $min): bool{
        if (!isset($data[$field])) {
            return true;
        }

        return mb_strlen($data[$field]) >= $min;
    }

    public function is_max(array $data, string $field, int $max): bool{
        if (!isset($data[$field])) {
            return true;
        }

        return mb_strlen($data[$field]) <= $max;
    }

    public function is_between(array $data, string $field, int $min, int $max): bool{
        if (!isset($data[$field])) {
            return true;
        }

        $len = mb_strlen($data[$field]);
        return $len >= $min && $len <= $max;
    }

    public function is_alphanumeric(array $data, string $field): bool{
        if (!isset($data[$field])) {
            return true;
        }

        return ctype_alnum($data[$field]);
    }

    public function is_unique(array $data, string $field, string $table, string $column): bool{
        if (!isset($data[$field])) {
            return true;
        }

        $sql = "SELECT $column FROM $table WHERE $column = :value";

        $stmt = $this->link->prepare($sql);
        $stmt->bindValue(":value", $data[$field]);

        $stmt->execute();

        return $stmt->fetchColumn() === false;
    }

    public function is_valid_user( array $data, string $field, string $table, string $column): bool{
        if (!isset($data[$field])) {
            return true;
        }

        $query = "SELECT $column FROM $table WHERE $column = :value";
        $stmt = $this->link->prepare($query);
        $stmt->bindValue(":value", $data[$field], \PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->fetchColumn() === true ) {
            $this->user_col = $stmt->fetch(\PDO::FETCH_ASSOC);
            return true;
        }
        else{
            $this->user_col = false;
            return false;
        }
    }

    public function is_valid_pass( array $data, string $field, string $table, string $column ): bool{
        if (!isset($data[$field]) || ($this->user_col === false )) {  //if username is invalid don't bother checking for password
            return true;
        }
        return password_verify($data[$field], $this->user_col[$column]);
    }
}
<?php
//PATH is not defined in the instance of ajax submission
define( 'PATH', '/var/www/html');
defined('VERSION') OR define('VERSION', '1.0.0');

spl_autoload_register( function($class){
    $namespace = str_replace("\\","/",__NAMESPACE__);
    $classname = str_replace("\\","/",$class);
    $class = PATH . '/' . ( empty($namespace) ? "" : $namespace . "/" ) . "{$classname}.class.php";
    include($class);
});
use includes\Database;
use includes\Authorization;
use includes\Adminajax;

$init_db = Database::get_db_instance();
$authorization = new Authorization(db_link());
$homepage_ajax = new Adminajax(db_link());

function db_link(){
    static $link;
    if( !$link ){
        $link = Database::$link;
    }
    return $link;
}

function admin_user_exists( $link ){
    $query = "SELECT `ADMIN` FROM init_library_user WHERE `ADMIN` = :admin_value";
    $admin = "LBADMIN";
    $stmt = db_link()->prepare($query);
    $stmt->bindValue(":admin_value", $admin, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchColumn();
}

function is_admin(){
    $id = $_SESSION['userdata']['userid'];
    $name = $_SESSION['userdata']['username'];
    $admin = "LBADMIN";
    $query = "SELECT * FROM init_library_user WHERE Username = :user AND id = :id AND `ADMIN` = :admin_value";
    $stmt = db_link()->prepare($query);
    $stmt->bindValue(":user", $name, PDO::PARAM_STR);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->bindValue(":admin_value", $admin, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchColumn();
}

function register_admin_data( string $username, string $password, int $privileges, string $admin = "" ){
    $query = "INSERT INTO init_library_user(Username, `Password`, privileges, `ADMIN`) VALUES(:username, :pass, :privileges, :adm)";
    $stmt = db_link()->prepare($query);
    $stmt->bindValue(":username", $username, PDO::PARAM_STR);
    $stmt->bindValue(":pass", password_hash($password, PASSWORD_BCRYPT), PDO::PARAM_STR);
    $stmt->bindValue(":privileges", $privileges, PDO::PARAM_INT);
    $stmt->bindValue(":adm", $admin, PDO::PARAM_STR);
    return $stmt->execute();
}

function is_admin_logged_in():bool{
    if( isset($_SESSION['userdata']['username']) ){
        return true;
    }

    $token = filter_input( INPUT_COOKIE, 'remember_admin', FILTER_SANITIZE_STRING );

    if( $token && token_validity($token) ){
        $admin = find_admin_by_token($token);
        //populate session variables and log admin in
        if( $admin){
            return log_admin_in($admin);
        }
    }
    return false;
}

function log_admin_out(){
    if( is_user_logged_in()){
        delete_admin_token($_SESSION['userdata']['userid']);
        if (isset($_COOKIE['remember_admin'])) {
            unset($_COOKIE['remember_admin']);
            setcookie('remember_admin', null, -1);
        }
        session_unset();
        session_destroy();
        session_write_close();
        redirect_to('index.nginx-debian.php');
    }
}

function log_admin_in( $admin ){
    if( !isset( $_SESSION['userdata']['username'] ) && !isset( $_SESSION['userdata']['username'] ) ){
        remember_admin($admin['userid']);
        $_SESSION['userdata']['username'] = $admin['username'];
        $_SESSION['userdata']['userid'] = $admin['userid'];
        return true;
    }
    return false;
}

function current_admin(){
    if( is_user_logged_in()){
        return $_SESSION['userdata']['username'];
    }
    return null;
}

function generate_tokens(): array{
    $selector = bin2hex(random_bytes(16));
    $validator = bin2hex(random_bytes(32));

    return [$selector, $validator, $selector . ':' . $validator];
}

function parse_token(string $token): ?array{
    $parts = explode(':', $token);

    if ($parts && count($parts) == 2) {
        return [$parts[0], $parts[1]];
    }
    return null;
}

function insert_admin_token(int $admin_id, string $selector, string $hashed_validator, string $expiry): bool{
    $sql = 'INSERT INTO init_admin_token(admin_id, selector, hashed_validator, expiry)
            VALUES(:admin_id, :selector, :hashed_validator, :expiry)';

    $statement = db_link()->prepare($sql);
    $statement->bindValue(':admin_id', $admin_id);
    $statement->bindValue(':selector', $selector);
    $statement->bindValue(':hashed_validator', $hashed_validator);
    $statement->bindValue(':expiry', $expiry);

    return $statement->execute();
}

function find_admin_token_by_selector(string $selector){

    $sql = 'SELECT id, selector, hashed_validator, admin_id, expiry
                FROM init_admin_token
                WHERE selector = :selector AND
                    expiry >= now()
                LIMIT 1';

    $statement = db_link()->prepare($sql);
    $statement->bindValue(':selector', $selector);

    $statement->execute();

    return $statement->fetch(PDO::FETCH_ASSOC);
}

function delete_admin_token(int $admin_id): bool{
    $sql = 'DELETE FROM init_admin_token WHERE admin_id = :admin_id';
    $statement = db_link() -> prepare($sql);
    $statement->bindValue(':admin_id', $admin_id);

    return $statement->execute();
}

function find_admin_by_token(string $token){
    $tokens = parse_token($token);

    if (!$tokens) {
        return null;
    }

    $sql = 'SELECT init_library_user.id, Username
            FROM init_library_user
            INNER JOIN admin_tokens ON admin_id = init_library_user.id
            WHERE selector = :selector AND
                expiry > now()
            LIMIT 1';

    $statement = db_link()->prepare($sql);
    $statement->bindValue(':selector', $tokens[0]);
    $statement->execute();

    return $statement->fetch(PDO::FETCH_ASSOC);
}

function token_validity(string $token): bool { // parse the token to get the selector and validator [$selector, $validator] = parse_token($token);
    $tokens = find_admin_token_by_selector($selector);
    if (!$tokens) {
        return false;
    }
    return password_verify($validator, $tokens['hashed_validator']);
}

function remember_admin(int $admin_id, int $day = 30){
    [$selector, $validator, $token] = generate_tokens();

    // remove all existing token associated with the admin id
    delete_admin_token($admin_id);

    // set expiration date
    $expired_seconds = time() + 60 * 60 * 24 * $day;

    // insert a token to the database
    $hash_validator = password_hash($validator, PASSWORD_DEFAULT);
    $expiry = date('Y-m-d H:i:s', $expired_seconds, '/'); //for availability in the entire domain

    if (insert_admin_token($admin_id, $selector, $hash_validator, $expiry)) {
        setcookie('remember_admin', $token, $expired_seconds);
    }
}

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

function view( $file ){
    require_once PATH . '/components/' . $file . '.php';
} 
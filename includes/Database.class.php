<?php
namespace includes;

class Database{

    private $creds;

    private static $instance; //singleton lb_database object

    public static $link; //single connection

    private $tables = array( 'init_library_user' => 
    "(`id`           INT(11)      NOT NULL AUTO_INCREMENT,
      `Username`     VARCHAR(128) NOT NULL,
      `Password`     VARCHAR(256)  NOT NULL,
      `privileges`   INT(11)      NOT NULL DEFAULT '1',
      `ADMIN`        VARCHAR(50),  
      PRIMARY KEY (`id`),
      INDEX username (`Username`)
    )",
    'init_library_books' => 
    "(`id`             INT(11)       NOT NULL AUTO_INCREMENT,
      `Title`          VARCHAR(128)  NOT NULL,
      `Author`         VARCHAR(128)  NOT NULL,
      `Release Year`   VARCHAR(50)       NOT NULL,
      `Status`         INT(11)       NOT NULL DEFAULT '0',
      `Burrower`       VARCHAR(128),
      `Burrowed Date`  DATE,
      `Returned Date`  DATE,        
      PRIMARY KEY (`id`),
      INDEX book ( `Title`, `Author`, `Release Year` ),
      INDEX author ( `Author` ),
      INDEX year ( `Release Year` ),
      INDEX burrower( `Burrower` )
    )",
    'init_admin_token' => "(
        `id`               INT AUTO_INCREMENT PRIMARY KEY,
        `selector`         VARCHAR(255) NOT NULL,
        `hashed_validator` VARCHAR(255) NOT NULL,
        `admin_id`         INT      NOT NULL,
        `expiry`           DATETIME NOT NULL,
        CONSTRAINT fk_user_id
            FOREIGN KEY (admin_id)
                REFERENCES `init_library_user` (id) ON DELETE CASCADE
    )",
    'init_book_count' => "(
        `id`               INT AUTO_INCREMENT PRIMARY KEY,
        `Total_count`      INT(11) NOT NULL DEFAULT 0,
        `issue_count`      INT(11) NOT NULL DEFAULT 0
    )");

    public function __construct(){
        self::$link = $this->db_link();
        $this->init_tables(self::$link);
    }

    public static function get_db_instance(){
        if(!self::$instance){
            self::$instance = new Database();
        }
        return self::$instance;
    }

    private function db_link(): \PDO {
        if (!self::$link) {
            $config = parse_ini_file('/var/librarydb.ini', true);   
            $creds = $config['creds'];
            self::$link = new \PDO(sprintf("mysql:host=%s;dbname=%s;charset=UTF8", 'localhost', 'library' ),
                            $creds['user'],
                            $creds['pass'],
                            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_PERSISTENT => true]); //error_handling, persistent connection im not familiar with but trying to get the hang of it
            ( self::$link ) && $this->init_tables(self::$link);
            return self::$link;
        }
        return self::$link;
    }

    private function init_tables( $link ){
        foreach( $this->tables as $key => $value ){
            $query = 'CREATE TABLE IF NOT EXISTS `' . $key. '`' . $value;
            $stmt = $link->prepare($query);
            $stmt->execute(); 
        }
        $query = "SELECT * FROM init_book_count";
        $stmt = $link->prepare($query);
        $stmt->execute();
        if(empty($stmt->fetchAll())){
            $query = "INSERT INTO init_book_count(Total_count, issue_count)
                      VALUES(0,0)";
            $stmt = $link->prepare($query);
            $stmt->execute();
        }
    }
    
}
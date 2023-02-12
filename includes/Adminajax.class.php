<?php
namespace includes;

class Adminajax{
    
    private $link;

    private $table;

    public function __construct( $link ){
        $this->link = $link;
        $this->table = 'init_library_books';
    }

    public function add_book( $data ){
        $query = "INSERT INTO init_library_books( Author, `Release Year`, `Status`, Burrower, `Burrowed Date`, `Returned Date`) 
                  VALUES(:author, :release_year, :book_status, :burrower, :burrowd, :returned)";
        $stmt = $this->link->prepare($sql);
        $stmt->bindValue(':author',$data['author'],\PDO::PARAM_STR);
        $stmt->bindValue(':release_year',$data['rel_year'],\PDO::PARAM_STR);
        $stmt->bindValue(':book_status',$data['status'],\PDO::PARAM_STR);
        $stmt->bindValue(':burrowd',$data['b_date'],\PDO::PARAM_STR);
        $stmt->bindValue(':returned',$data['r_date'],\PDO::PARAM_STR);
        $stmt->bindValue('"burrower',$data['burrower'],\PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function delete_book( $data ){
        $query = "DELETE FROM $this->table WHERE Author=:author AND `Release Year` = :r_year AND `Status` = :book_status AND 
                    Burrower = :burrower AND `Burrowed Date` = :burrowed AND `Returned Date` = :returned";
        $stmt = $this->link->prepare($sql);
        $stmt->bindValue(':author',$data['author'],\PDO::PARAM_STR);
        $stmt->bindValue(':release_year',$data['rel_year'],\PDO::PARAM_STR);
        $stmt->bindValue(':book_status',$data['status'],\PDO::PARAM_STR);
        $stmt->bindValue(':burrowd',$data['b_date'],\PDO::PARAM_STR);
        $stmt->bindValue(':returned',$data['r_date'],\PDO::PARAM_STR);
        $stmt->bindValue('"burrower',$data['burrower'],\PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function issue_book( $data ){
        $query = "ALTER TABLE $this->table WHERE Author=:author AND `Release Year` = :r_year AND `Status` = :book_status AND 
                    Burrower = :burrower AND `Burrowed Date` = :burrowed AND `Returned Date` = :returned MODIFY COLUMN `Status` = :new_status";
        $stmt = $this->link->prepare($sql);
        $stmt->bindValue(':author',$data['author'],\PDO::PARAM_STR);
        $stmt->bindValue(':release_year',$data['rel_year'],\PDO::PARAM_STR);
        $stmt->bindValue(':book_status',$data['status'],\PDO::PARAM_STR);
        $stmt->bindValue(':burrowd',$data['b_date'],\PDO::PARAM_STR);
        $stmt->bindValue(':returned',$data['r_date'],\PDO::PARAM_STR);
        $stmt->bindValue('burrower',$data['burrower'],\PDO::PARAM_STR);
        $stmt->bindValue(':new_status',$data['new_status'],\PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function return_book( $data ){
        $query = "ALTER TABLE $this->table WHERE Author=:author AND `Release Year` = :r_year AND `Status` = :book_status AND 
                    Burrower = :burrower AND `Burrowed Date` = :burrowed AND `Returned Date` = :returned MODIFY COLUMN `Status` = :new_status";
        $stmt = $this->link->prepare($sql);
        $stmt->bindValue(':author',$data['author'],\PDO::PARAM_STR);
        $stmt->bindValue(':release_year',$data['rel_year'],\PDO::PARAM_STR);
        $stmt->bindValue(':book_status',$data['status'],\PDO::PARAM_STR);
        $stmt->bindValue(':burrowd',$data['b_date'],\PDO::PARAM_STR);
        $stmt->bindValue(':returned',$data['r_date'],\PDO::PARAM_STR);
        $stmt->bindValue('burrower',$data['burrower'],\PDO::PARAM_STR);
        $stmt->bindValue(':new_status',$data['new_status'],\PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function list_books(){

    }

    public function get_book_count(){

    }
}
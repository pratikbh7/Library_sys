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
        $query = "INSERT INTO init_library_books(Title, Author, `Release Year`) 
                  VALUES(:title, :author, :release_year)";
        $stmt = $this->link->prepare($query);
        $stmt->bindValue(':title',$data['title'],\PDO::PARAM_STR);
        $stmt->bindValue(':author',$data['author'],\PDO::PARAM_STR);
        $stmt->bindValue(':release_year',$data['release_year'],\PDO::PARAM_STR);
        if($stmt->execute()){
            $query = "UPDATE init_book_count SET Total_count = Total_count + 1";
            $stmt = $this->link->prepare($query);
            return $stmt->execute();
        }
        return false;
    }

    public function delete_book( $data ){
        $query = "DELETE FROM $this->table WHERE Title = `:title` AND Author=`:author` AND `Release Year` = :r_year";
        $stmt = $this->link->prepare($query);
        $stmt->bindValue(':title',$data['title'],\PDO::PARAM_STR);
        $stmt->bindValue(':author',$data['author'],\PDO::PARAM_STR);
        $stmt->bindValue(':r_year',$data['r_year'],\PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function issue_book( $data ){
        $query = "UPDATE $this->table SET Burrower = :burrower, `Burrowed Date` = :burrowed, COLUMN `Status` = :new_status 
                  WHERE Title=:title AND Author=:author AND `Release Year` = :r_year ";
        $stmt = $this->link->prepare($query);
        $stmt->bindValue(':title',$data['title'],\PDO::PARAM_STR);
        $stmt->bindValue(':author',$data['author'],\PDO::PARAM_STR);
        $stmt->bindValue(':r_year',$data['r_year'],\PDO::PARAM_STR);
        $stmt->bindValue(':book_status',$data['status'],\PDO::PARAM_STR);
        $stmt->bindValue(':burrowd',$data['b_date'],\PDO::PARAM_STR);
        $stmt->bindValue('burrower',$data['burrower'],\PDO::PARAM_STR);
        $stmt->bindValue(':new_status',$data['new_status'],\PDO::PARAM_STR);
        if($stmt->execute()){
            $query = "UPDATE init_book_count SET issue_count = issue_count + 1";
            $stmt = $this->link->prepare($query);
            return $stmt->execute();
        }
        return false;
    }

    public function return_book( $data ){
        $query = "UPDATE $this->table SET Burrower = :burrower , `Burrowed Date` = :burrowed, `Status` = :new_status
                  WHERE Title = :title Author=:author AND `Release Year` = :r_year ";
        $stmt = $this->link->prepare($query);
        $stmt->bindValue(':title',$data['title'],\PDO::PARAM_STR);
        $stmt->bindValue(':author',$data['author'],\PDO::PARAM_STR);
        $stmt->bindValue(':r_year',$data['r_year'],\PDO::PARAM_STR);
        $stmt->bindValue(':book_status',$data['status'],\PDO::PARAM_STR);
        $stmt->bindValue(':burrowd',$data['b_date'],\PDO::PARAM_STR);
        $stmt->bindValue('burrower',$data['burrower'],\PDO::PARAM_STR);
        $stmt->bindValue(':new_status',$data['new_status'],\PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function list_books($start){
        $query = "SELECT * FROM $this->table  LIMIT $start, 15";
        $stmt = $this->link->prepare($query);
        if($stmt->execute()){
            return $stmt->fetchAll();
        }
        return false;
    }

    public function issued_books($start){
        $query = "SELECT * FROM $this->table  WHERE STATUS = 1 LIMIT $start, 15";
        $stmt = $this->link->prepare($query);
        if($stmt->execute()){
            return $stmt->fetchAll();
        }
        return false;
    }

    public function get_book_count(){
        $query = "SELECT * FROM init_book_count LIMIT 1";
        $stmt = $this->link->prepare($query);
        if($stmt->execute()){
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function duplication_check( $data ){
        $query = "SELECT Title FROM $this->table 
                  WHERE Title = :title AND Author = :author AND `Release Year` = :release_year";
        $stmt = $this->link->prepare($query);
        $stmt->bindValue(':title',$data['title'],\PDO::PARAM_STR);
        $stmt->bindValue(':author',$data['author'],\PDO::PARAM_STR);
        $stmt->bindValue(':release_year',$data['release_year'],\PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() === false;
    }
}
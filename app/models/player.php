<?php

class PlayerModel extends BaseModel{
    public static function login($username, $password){
        $query = DB::connection()->prepare('SELECT id FROM Player WHERE username = :un AND password = :pw;');
        $query->execute(array('un' => $username, 'pw' => $password));
        $row = $query->fetch();
        
        if($row){
            return $row['id'];
        }
        return 0;
    }
    
    public static function findById($id){
        $query = DB::connection()->prepare('SELECT id, username FROM Player Where id = :id;');
        $query->execute(array('id' => $id));
        $row = $query->fetch();
        
        if($row){
            return new Player($row);
        }
        return null;
    }
}

class Player{
    public $id, $username;
    
    public function __construct($row) {
        $this->id = $row['id'];
        $this->username = $row['username'];
    }
}
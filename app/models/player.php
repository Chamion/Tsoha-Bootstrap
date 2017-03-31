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
    
    public static function findByName($username){
        $query = DB::connection()->prepare('SELECT id, username FROM Player Where username = :username;');
        $query->execute(array('username' => $username));
        $row = $query->fetch();
        
        if($row){
            return new Player($row);
        }
        return null;
    }
    
    public static function nameAvailable($username) {
        $query = DB::connection()->prepare('SELECT id FROM Player Where username = :username;');
        $query->execute(array('username' => $username));
        $row = $query->fetch();
        
        if($row){
            return false;
        }
        return true;
    }
    
    public static function addPlayer($username, $password){
        $query = DB::connection()->prepare('INSERT INTO Player (username, password) VALUES (:username, :password);');
        $query->execute(array('username' => $username, 'password' => $password));
    }
    
    public static function findByTeam($team){
        $query = DB::connection()->prepare('SELECT Player.id AS id, Player.username AS username FROM Player, Membership WHERE Membership.team = :team AND Membership.player = Player.id AND Membership.accepted;');
        $query->execute(array('team' => $team));
        $rows = $query->fetchAll();
        $members = array();
        
        foreach ($rows as $row) {
            $members[] = new Player($row);
        }
        return $members;
    }
    
    public static function findByInvite($team){
        $query = DB::connection()->prepare('SELECT Player.id AS id, Player.username AS username FROM Player, Membership WHERE Membership.team = :team AND Membership.player = Player.id AND NOT Membership.accepted;');
        $query->execute(array('team' => $team));
        $rows = $query->fetchAll();
        $members = array();
        
        foreach ($rows as $row) {
            $members[] = new Player($row);
        }
        return $members;
    }
}

class Player{
    public $id, $username;
    
    public function __construct($row) {
        $this->id = $row['id'];
        $this->username = $row['username'];
    }
}
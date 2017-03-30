<?php

class TeamModel extends BaseModel{
    public static function addTeam($leader, $name){
        $query = DB::connection()->prepare('INSERT INTO Team(leader, group_name) VALUES (:leader, :name);');
        $query->execute(array('leader' => $leader, 'name' => $name));
        $query2 = DB::connection()->prepare('SELECT id FROM Team ORDER BY id DESC LIMIT 1;');
        $query2->execute();
        $row2 = $query2->fetch();
        $group = $row2['id'];
        $query3 = DB::connection()->prepare('INSERT INTO Membership (player, team) VALUES (:leader, :group);');
        $query3->execute(array('leader' => $leader, 'group' => $group));
    }
    
    public static function findByMember($member){
        $query = DB::connection()->prepare('SELECT Team.id AS id, Team.group_name AS name FROM Team, Membership WHERE Membership.team = Team.id AND Membership.player = :member;');
        $query->execute(array('member' => $member));
        $rows = $query->fetchAll();
        
        $teams = array();
        foreach ($rows as $row){
            $teams[] = new ListTeam($row['id'], $row['name']);
        }
        return $teams;
    }
    
    public static function findById($id){
        $query = DB::connection()->prepare('SELECT * FROM Team WHERE id = :id;');
        $query->execute(array('id' => $id));
        $row = $query->fetch();
        
        if($row){
            return new FullTeam($row['id'], $row['leader'], $row['group_name'], $row['closed']);
        }
        return null;
    }
    
    public static function severMembership($player, $team){
        $query = DB::connection()->prepare('DELETE FROM Membership WHERE player = :player AND team = :team;');
        $query->execute(array('player' => $player, 'team' => $team));
    }
}

class ListTeam {
    public $id, $name;
    
    public function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }
}

class FullTeam {
    public $id, $leader, $name, $closed;
    
    public function __construct($id, $leader, $name, $closed) {
        $this->id = $id;
        $this->leader = $leader;
        $this->name = $name;
        $this->closed = $closed;
    }
}
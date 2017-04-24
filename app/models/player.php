<?php

class PlayerModel extends BaseModel {

    public $errors;

    public function __construct($username, $password) {
        parent::__construct();
        $this->validators = array('validateUsername', 'validatePassword');
        $this->username = $username;
        $this->password = $password;
        $this->errors = $this->errors();
    }

    public static function login($username, $password) {
        $query = DB::connection()->prepare('SELECT id FROM Player WHERE username = :un AND password = :pw;');
        $query->execute(array('un' => $username, 'pw' => $password));
        $row = $query->fetch();

        if ($row) {
            return $row['id'];
        }
        return 0;
    }

    public static function findById($id) {
        $query = DB::connection()->prepare('SELECT id, username FROM Player Where id = :id;');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
            return new Player($row);
        }
        return null;
    }

    public static function findByName($username) {
        $query = DB::connection()->prepare('SELECT id, username FROM Player Where username = :username;');
        $query->execute(array('username' => $username));
        $row = $query->fetch();

        if ($row) {
            return new Player($row);
        }
        return null;
    }

    public static function nameAvailable($username) {
        $query = DB::connection()->prepare('SELECT id FROM Player Where username = :username;');
        $query->execute(array('username' => $username));
        $row = $query->fetch();

        if ($row) {
            return false;
        }
        return true;
    }

    public static function addPlayer($username, $password) {
        $query = DB::connection()->prepare('INSERT INTO Player (username, password) VALUES (:username, :password);');
        $query->execute(array('username' => $username, 'password' => $password));
    }

    public static function findByTeam($team, $page) {
        $query = DB::connection()->prepare('SELECT Player.id AS id, Player.username AS username FROM Player, Membership WHERE Membership.team = :team AND Membership.player = Player.id AND Membership.accepted LIMIT 10 OFFSET :offset;');
        $query->execute(array('team' => $team, 'offset' => ($page - 1) * 10));
        $rows = $query->fetchAll();
        $members = array();

        foreach ($rows as $row) {
            $members[] = new Player($row);
        }
        return $members;
    }

    public static function findByInvite($team, $page) {
        $query = DB::connection()->prepare('SELECT Player.id AS id, Player.username AS username FROM Player, Membership WHERE Membership.team = :team AND Membership.player = Player.id AND NOT Membership.accepted LIMIT 10 OFFSET :offset;');
        $query->execute(array('team' => $team, 'offset' => ($page - 1) * 10));
        $rows = $query->fetchAll();
        $members = array();

        foreach ($rows as $row) {
            $members[] = new Player($row);
        }
        return $members;
    }

    public function validateUsername() {
        $errors = array();
        $errors = self::validate_string($this->username, $errors);
        if (strlen($this->username) > 32) {
            $errors[] = 'Username must be 32 characters or shorter.';
        }
        if (!self::nameAvailable($this->username)) {
            $errors[] = 'Username is already in use.';
        }
        return $errors;
    }

    public function validatePassword() {
        $errors = array();
        $errors = self::validate_string($this->password, $errors);
        if (strlen($this->password) > 50) {
            $errors[] = 'Password must be 50 characters or shorter.';
        }
        return $errors;
    }

    public static function countPagesByTeam($team) {
        $query = DB::connection()->prepare('SELECT COUNT(*) AS count FROM Player, Membership WHERE Membership.team = :team AND Membership.player = Player.id AND Membership.accepted;');
        $query->execute(array('team' => $team));
        $row = $query->fetch();
        if (!$row) {
            return 1;
        }
        $pages = (int) ceil($row['count'] / 10);
        if ($pages <= 0) {
            return 1;
        }
        return $pages;
    }

    public static function countPagesByInvite($team) {
        $query = DB::connection()->prepare('SELECT COUNT(*) AS count FROM Player, Membership WHERE Membership.team = :team AND Membership.player = Player.id AND NOT Membership.accepted;');
        $query->execute(array('team' => $team));
        $row = $query->fetch();
        if (!$row) {
            return 1;
        }
        $pages = (int) ceil($row['count'] / 10);
        if ($pages <= 0) {
            return 1;
        }
        return $pages;
    }

}

class Player {

    public $id, $username;

    public function __construct($row) {
        $this->id = $row['id'];
        $this->username = $row['username'];
    }

}

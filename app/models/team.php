<?php

class TeamModel extends BaseModel {

    public function __construct($groupName) {
        parent::__construct();
        $this->validators = array('validateGroupName');
        $this->groupName = $groupName;
        $this->errors = $this->errors();
    }

    public static function addTeam($leader, $name) {
        $query = DB::connection()->prepare('INSERT INTO Team(leader, group_name) VALUES (:leader, :name);');
        $query->execute(array('leader' => $leader, 'name' => $name));
        $query2 = DB::connection()->prepare('SELECT id FROM Team ORDER BY id DESC LIMIT 1;');
        $query2->execute();
        $row2 = $query2->fetch();
        $group = $row2['id'];
        $query3 = DB::connection()->prepare('INSERT INTO Membership (player, team, accepted) VALUES (:leader, :group, true);');
        $query3->execute(array('leader' => $leader, 'group' => $group));
    }

    public static function findByMember($member, $page) {
        $query = DB::connection()->prepare('SELECT Team.id AS id, Team.group_name AS name FROM Team, Membership WHERE Membership.team = Team.id AND Membership.player = :member AND Membership.accepted LIMIT 10 OFFSET :offset;');
        $query->execute(array('member' => $member, 'offset' => ($page - 1) * 10));
        $rows = $query->fetchAll();

        $teams = array();
        foreach ($rows as $row) {
            $teams[] = new ListTeam($row['id'], $row['name']);
        }
        return $teams;
    }

    public static function findAllByMember($member) {
        $query = DB::connection()->prepare('SELECT Team.id AS id, Team.group_name AS name FROM Team, Membership WHERE Membership.team = Team.id AND Membership.player = :member AND Membership.accepted;');
        $query->execute(array('member' => $member));
        $rows = $query->fetchAll();

        $teams = array();
        foreach ($rows as $row) {
            $teams[] = new ListTeam($row['id'], $row['name']);
        }
        return $teams;
    }

    public static function findInvites($member, $page) {
        $query = DB::connection()->prepare('SELECT Team.id AS id, Team.group_name AS name FROM Team, Membership WHERE Membership.team = Team.id AND Membership.player = :member AND NOT Membership.accepted LIMIT 10 OFFSET :offset;');
        $query->execute(array('member' => $member, 'offset' => ($page - 1) * 10));
        $rows = $query->fetchAll();

        $teams = array();
        foreach ($rows as $row) {
            $teams[] = new ListTeam($row['id'], $row['name']);
        }
        return $teams;
    }

    public static function findById($id) {
        $query = DB::connection()->prepare('SELECT * FROM Team WHERE id = :id;');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
            return new FullTeam($row['id'], $row['leader'], $row['group_name'], $row['closed']);
        }
        return null;
    }

    public static function severMembership($player, $team) {
        $query = DB::connection()->prepare('DELETE FROM Membership WHERE player = :player AND team = :team;');
        $query->execute(array('player' => $player, 'team' => $team));
    }

    public static function invite($team, $player) {
        $query = DB::connection()->prepare('SELECT player FROM Membership WHERE player = :player AND team = :team;');
        $query->execute(array('team' => $team, 'player' => $player));
        $row = $query->fetch();
        if ($row) {
            return;
        }
        $query2 = DB::connection()->prepare('INSERT INTO Membership (player, team) VALUES (:player, :team);');
        $query2->execute(array('team' => $team, 'player' => $player));
    }

    public static function join($player, $team) {
        $query = DB::connection()->prepare('UPDATE Membership SET accepted = true WHERE player = :player AND team = :team;');
        $query->execute(array('player' => $player, 'team' => $team));
    }

    public static function setClosed($team, $value) {
        $query = DB::connection()->prepare('UPDATE Team SET closed = :value WHERE id = :team;');
        $params = array('team' => $team);
        if ($value) {
            $params['value'] = 'true';
        } else {
            $params['value'] = 'false';
        }
        $query->execute($params);
    }

    public function validateGroupName() {
        $errors = array();
        if (strlen($this->groupName) > 32) {
            $errors[] = 'Group name must be 32 characters or shorter.';
        }
        return $this->validate_string($this->groupName, $errors);
    }

    public static function countPagesByMember($member) {
        $query = DB::connection()->prepare('SELECT COUNT(*) as count FROM Team, Membership WHERE Membership.team = Team.id AND Membership.player = :member AND Membership.accepted;');
        $query->execute(array('member' => $member));
        $row = $query->fetch();
        if (!$row) {
            return 1;
        }
        $count = $row['count'];
        $pages = (int) ceil($count / 10);
        if ($pages <= 0) {
            $pages = 1;
        }
        return $pages;
    }

    public static function countPagesInvites($member) {
        $query = DB::connection()->prepare('SELECT COUNT(*) AS count FROM Team, Membership WHERE Membership.team = Team.id AND Membership.player = :member AND NOT Membership.accepted');
        $query->execute(array('member' => $member));
        $row = $query->fetch();
        if (!$row) {
            return 1;
        }
        $count = $row['count'];
        $pages = (int) ceil($count / 10);
        if ($pages <= 0) {
            $pages = 1;
        }
        return $pages;
    }

    public static function joinOpen($player, $team) {
        if (!ctype_digit($team)) {
            return 'Input must be a positive integer.';
        }
        if ($team < 1 || $team > 2147483647) {
            return 'Input must be a valid join ID';
        }
        if (self::findById($team) == null) {
            return 'No group found for the specified ID.';
        }
        if (self::isInvited($player, $team)) {
            self::join($player, $team);
            return '';
        }
        if (self::isClosed($team)) {
            return 'The specified group is invite-only.';
        }
        $query = DB::connection()->prepare('INSERT INTO Membership (player, team, accepted) VALUES (:player, :team, true);');
        $query->execute(array('player' => $player, 'team' => $team));
        return '';
    }

    public static function isInvited($player, $team) {
        $query = DB::connection()->prepare('SELECT COUNT(*) as count FROM Membership WHERE player = :player AND team = :team;');
        $query->execute(array('player' => $player, 'team' => $team));
        $row = $query->fetch();
        if ($row['count'] == 0) {
            return false;
        }
        return true;
    }

    public static function isClosed($team) {
        $query = DB::connection()->prepare('SELECT closed FROM Team WHERE id = :team;');
        $query->execute(array('team' => $team));
        $row = $query->fetch();
        if ($row['closed'] == 1) {
            return true;
        }
        return false;
    }

    public static function destroy($team) {
        $memberQuery = DB::connection()->prepare('DELETE FROM Membership WHERE team = :team;');
        $memberQuery->execute(array('team' => $team));
        $teamQuery = DB::connection()->prepare('DELETE FROM Team WHERE id = :team');
        $teamQuery->execute(array('team' => $team));
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

<?php

class GameModel extends BaseModel {

    public static function all() {
        $query = DB::connection()->prepare('SELECT * FROM Game;');
        $query->execute();
        $rows = $query->fetchAll();
        $games = array();

        foreach ($rows as $row) {
            $games[] = new Game($row);
        }

        return $games;
    }

    public static function findByPlayer($player, $page) {
        $query = DB::connection()->prepare('SELECT * FROM Game WHERE Player = :player ORDER BY id DESC LIMIT 10 OFFSET :offset;');
        $query->execute(array('player' => $player, 'offset' => ($page-1)*10));
        $rows = $query->fetchAll();
        $games = array();

        foreach ($rows as $row) {
            $games[] = new Game($row);
        }

        return $games;
    }

    public static function findById($id) {
        $query = DB::connection()->prepare('SELECT * FROM Game WHERE id = :id');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
            return new Game($row);
        }
        return null;
    }

    public static function removeById($id) {
        $query = DB::connection()->prepare('DELETE FROM Game WHERE id = :id;');
        $query->execute(array('id' => $id));
    }

    public static function add($player, $legend, $win, $hero, $opponent) {
        $query = DB::connection()->prepare('INSERT INTO Game (player, legend, win, hero, opponent) VALUES (:player, :legend, :win, :hero, :opponent);');
        $query->execute(array('player' => $player, 'legend' => $legend, 'win' => $win, 'hero' => $hero, 'opponent' => $opponent));
    }

    public static function generalPrivateStats($player) {
        $query = DB::connection()->prepare('SELECT 100*count(CASE WHEN win THEN 1 END)/COUNT(win) AS winrate, COUNT(win) AS sample, Game.hero FROM Game WHERE player = :player GROUP BY Game.hero ORDER BY Game.hero');
        $query->execute(array('player' => $player));
        $rows = $query->fetchAll();

        $stats = array();
        foreach ($rows as $row) {
            $stats[] = new Stats($row['hero'], $row['winrate'], $row['sample']);
        }
        return $stats;
    }

    public static function generalGroupStats($groups) {
        $stringForm = '(';
        foreach ($groups as $group) {
            $stringForm = $stringForm . $group . ',';
        }
        $stringForm = $stringForm . '0)';
        //stringForm upotetaan suoraan statement:tiin, jotta kielletty merkki ',' ei sensuroidu. Hirveä hakkaus, mutta toimii.
        //stringForm ei sisällä käyttäjän kirjoittamaa syötettä, joten injektiovaaraa ei ole.
        $statement = 'SELECT 100*count(CASE WHEN win THEN 1 END)/COUNT(win) AS winrate, COUNT(win) AS sample, Game.hero FROM Game, Membership WHERE Game.player = Membership.player AND Membership.team IN ' . $stringForm . ' AND Membership.accepted GROUP BY Game.hero ORDER BY Game.hero';
        $query = DB::connection()->prepare($statement);
        $query->execute();
        $rows = $query->fetchAll();
        $stats = array();
        foreach ($rows as $row) {
            $stats[] = new Stats($row['hero'], $row['winrate'], $row['sample']);
        }
        return $stats;
    }
    
    public static function matchupPrivateStats($player, $hero) {
        $query = DB::connection()->prepare('SELECT 100*count(CASE WHEN win THEN 1 END)/COUNT(win) AS winrate, COUNT(win) AS sample, Game.opponent FROM Game WHERE player = :player AND Game.hero = :hero GROUP BY Game.opponent ORDER BY Game.opponent');
        $query->execute(array('player' => $player, 'hero' => $hero));
        $rows = $query->fetchAll();

        $stats = array();
        foreach ($rows as $row) {
            $stats[] = new Stats($row['opponent'], $row['winrate'], $row['sample']);
        }
        return $stats;
    }
    
    public static function matchupGroupStats($groups, $hero){
        $stringForm = '(';
        foreach ($groups as $group) {
            $stringForm = $stringForm . $group . ',';
        }
        $stringForm = $stringForm . '0)';
        //stringForm upotetaan suoraan statement:tiin, jotta kielletty merkki ',' ei sensuroidu. Hirveä hakkaus, mutta toimii.
        //stringForm ei sisällä käyttäjän kirjoittamaa syötettä, joten injektiovaaraa ei ole.
        $statement = 'SELECT 100*count(CASE WHEN win THEN 1 END)/COUNT(win) AS winrate, COUNT(win) AS sample, Game.opponent FROM Game, Membership WHERE Game.hero = :hero AND Game.player = Membership.player AND Membership.team IN ' . $stringForm . ' AND Membership.accepted GROUP BY Game.opponent ORDER BY Game.opponent';
        $query = DB::connection()->prepare($statement);
        $query->execute(array('hero' => $hero));
        $rows = $query->fetchAll();
        $stats = array();
        foreach ($rows as $row) {
            $stats[] = new Stats($row['opponent'], $row['winrate'], $row['sample']);
        }
        return $stats;
    }
    
    public static function update($id, $player, $legend, $win, $hero, $opponent) {
        $query = DB::connection()->prepare('UPDATE Game SET player = :player, legend = :legend, win = :win, hero = :hero, opponent = :opponent WHERE id = :id;');
        $query->execute(array('id' => $id, 'player' => $player, 'legend' => $legend, 'win' => $win, 'hero' => $hero, 'opponent' => $opponent));
    }
    
    public static function countPagesByPlayer($player){
        $query = DB::connection()->prepare('SELECT COUNT(*) AS count FROM Game WHERE Player = :player;');
        $query->execute(array('player' => $player));
        $row = $query->fetch();
        if(!$row){
            return 1;
        }
        $count = $row['count'];
        $pages = (int) ceil($count/10);
        if($pages <= 0){
            $pages = 1;
        }
        return $pages;
    }
}

function classNumberToString($number){
    if ($number == 1) {
        return 'Warrior';
    } else if ($number == 2) {
        return 'Shaman';
    } else if ($number == 3) {
        return 'Rogue';
    } else if ($number == 4) {
        return 'Paladin';
    } else if ($number == 5) {
        return 'Hunter';
    } else if ($number == 6) {
        return 'Druid';
    } else if ($number == 7) {
        return 'Warlock';
    } else if ($number == 8) {
        return 'Mage';
    } else if ($number == 9) {
        return 'Priest';
    }
    return '';
}

class Stats {

    public $header, $winrate, $sample;

    public function __construct($header, $winrate, $sample) {
        $this->header = classNumberToString($header);
        $this->winrate = $winrate;
        $this->sample = $sample;
    }

}

class Game {

    public $id, $player, $legend, $win, $hero, $opponent, $date;

    public function __construct($row) {
        $this->collect($row);
    }

    private function collect($row) {
        $this->id = $row['id'];
        $this->player = $row['player'];
        if ($row['legend']) {
            $this->legend = 'true';
        } else {
            $this->legend = 'false';
        }
        if ($row['win']) {
            $this->win = 'win';
        } else {
            $this->win = 'loss';
        }
        $this->hero = classNumberToString($row['hero']);
        $this->opponent = classNumberToString($row['opponent']);
        $this->date = $row['book_date'];
    }

}

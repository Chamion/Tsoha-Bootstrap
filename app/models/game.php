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

    public static function findByPlayer($player) {
        $query = DB::connection()->prepare('SELECT * FROM Game WHERE Player = :player');
        $query->execute(array('player' => $player));
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

}

class Stats {

    public $header, $winrate, $sample;

    public function __construct($header, $winrate, $sample) {
        if ($header == 1) {
            $this->header = 'Warrior';
        } else if ($header == 2) {
            $this->header = 'Shaman';
        } else if ($header == 3) {
            $this->header = 'Rogue';
        } else if ($header == 4) {
            $this->header = 'Paladin';
        } else if ($header == 5) {
            $this->header = 'Hunter';
        } else if ($header == 6) {
            $this->header = 'Druid';
        } else if ($header == 7) {
            $this->header = 'Warlock';
        } else if ($header == 8) {
            $this->header = 'Mage';
        } else if ($header == 9) {
            $this->header = 'Priest';
        }
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
        if ($row['hero'] == 1) {
            $this->hero = 'Warrior';
        } else if ($row['hero'] == 2) {
            $this->hero = 'Shaman';
        } else if ($row['hero'] == 3) {
            $this->hero = 'Rogue';
        } else if ($row['hero'] == 4) {
            $this->hero = 'Paladin';
        } else if ($row['hero'] == 5) {
            $this->hero = 'Hunter';
        } else if ($row['hero'] == 6) {
            $this->hero = 'Druid';
        } else if ($row['hero'] == 7) {
            $this->hero = 'Warlock';
        } else if ($row['hero'] == 8) {
            $this->hero = 'Mage';
        } else if ($row['hero'] == 9) {
            $this->hero = 'Priest';
        }
        if ($row['opponent'] == 1) {
            $this->opponent = 'Warrior';
        } else if ($row['opponent'] == 2) {
            $this->opponent = 'Shaman';
        } else if ($row['opponent'] == 3) {
            $this->opponent = 'Rogue';
        } else if ($row['opponent'] == 4) {
            $this->opponent = 'Paladin';
        } else if ($row['opponent'] == 5) {
            $this->opponent = 'Hunter';
        } else if ($row['opponent'] == 6) {
            $this->opponent = 'Druid';
        } else if ($row['opponent'] == 7) {
            $this->opponent = 'Warlock';
        } else if ($row['opponent'] == 8) {
            $this->opponent = 'Mage';
        } else if ($row['opponent'] == 9) {
            $this->opponent = 'Priest';
        }
        $this->date = $row['book_date'];
    }

}

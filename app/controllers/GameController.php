<?php

class GameController extends BaseController {

    public static function removeList() {
        self::check_logged_in();
        $player = $_SESSION['player'];
        $games = GameModel::findByPlayer($player);
        View::make('suunnitelmat/poista.html', array('games' => $games));
    }

    public static function remove() {
        self::check_logged_in();
        $params = $_POST;
        GameModel::removeById($params['id']);
        Redirect::to('/poista');
    }

    public static function loggingPage() {
        self::check_logged_in();
        if ($_SESSION['gameInput'] == null) {
            $_SESSION['gameInput'] = array(
                'legend' => 1,
                'win' => 1,
                'hero' => 1,
                'opponent' => 1
            );
        }
        View::make('suunnitelmat/kirjaus.html', $_SESSION['gameInput']);
    }

    public static function addResult() {
        self::check_logged_in();
        $player = $_SESSION['player'];
        if (isset($_POST['legend'])) {
            $legend = 1;
        } else {
            $legend = 0;
        }
        if ($_POST['win'] == 'win') {
            $win = 1;
        } else {
            $win = 0;
        }
        GameModel::add($player, $legend, $win, $_POST['hero'], $_POST['opponent']);
        $_SESSION['gameInput'] = array(
            'legend' => $legend,
            'win' => $win,
            'hero' => $_POST['hero'],
            'opponent' => $_POST['opponent']
        );
        Redirect::to('/kirjaus');
    }

    public static function statsRefresh() {
        self::check_logged_in();
        $_SESSION['statsInput'] = array(
            'date' => $_POST['date'],
            'group' => $_POST['group'],
            'class' => $_POST['class']
        );
        if ($_SESSION['statsInput']['group'] == 'custom') {
            $_SESSION['statsInput']['groupIds'] = array();
            foreach (TeamModel::findByMember($_SESSION['player']) as $team) {
                if (isset($_POST['' . $team->id])) {
                    $_SESSION['statsInput']['groupIds'][] = $team->id;
                }
            }
        }
        if ($_SESSION['statsInput']['class'] == 'for') {
            $_SESSION['statsInput']['hero'] = $_POST['hero'];
        }
        Redirect::to('/analyysi');
    }

    public static function stats() {
        self::check_logged_in();
        if ($_SESSION['statsInput'] == null) {
            $_SESSION['statsInput'] = array(
                'date' => 'all',
                'group' => 'all',
                'class' => 'all'
            );
        }
        $player = $_SESSION['player'];
        $allGroups = TeamModel::findByMember($player);
        if ($_SESSION['statsInput']['class'] == 'all') {
            if ($_SESSION['statsInput']['group'] == 'all') {
                $groupIds = array();
                foreach ($allGroups as $group) {
                    $groupIds[] = $group->id;
                }
                $stats = GameModel::generalGroupStats($groupIds);
            } else if ($_SESSION['statsInput']['group'] == 'me') {
                $stats = GameModel::generalPrivateStats($player);
            } else {
                $stats = GameModel::generalGroupStats($_SESSION['statsInput']['groupIds']);
            }
        } else if ($_SESSION['statsInput']['class'] == 'for') {
            if ($_SESSION['statsInput']['group'] == 'all') {
                $groupIds = array();
                foreach ($allGroups as $group) {
                    $groupIds[] = $group->id;
                }
                $stats = GameModel::matchupGroupStats($groupIds, $_SESSION['statsInput']['hero']);
            } else if ($_SESSION['statsInput']['group'] == 'me') {
                $stats = GameModel::matchupPrivateStats($player, $_SESSION['statsInput']['hero']);
            } else {
                $stats = GameModel::matchupGroupStats($_SESSION['statsInput']['groupIds'], $_SESSION['statsInput']['hero']);
            }
        }
        View::make('suunnitelmat/analyysi.html', array('stats' => $stats, 'groups' => $allGroups));
    }

    public static function editInit() {
        self::check_logged_in();
        $game = GameModel::findById($_POST['id']);
        $_SESSION['gameInput'] = array(
            'legend' => $game->legend,
            'win' => $game->win,
            'hero' => $game->hero,
            'opponent' => $game->opponent
        );
        $_SESSION['gameId'] = $_POST['id'];
        Redirect::to('/edit');
    }

    public static function editPage() {
        View::make('suunnitelmat/edit.html', $_SESSION['gameInput']);
    }
    
    public static function editSave() {
        self::check_logged_in();
        $player = $_SESSION['player'];
        if (isset($_POST['legend'])) {
            $legend = 1;
        } else {
            $legend = 0;
        }
        if ($_POST['win'] == 'win') {
            $win = 1;
        } else {
            $win = 0;
        }
        GameModel::update($_SESSION['gameId'], $player, $legend, $win, $_POST['hero'], $_POST['opponent']);
        $_SESSION['gameInput'] = array(
            'legend' => $legend,
            'win' => $win,
            'hero' => $_POST['hero'],
            'opponent' => $_POST['opponent']
        );
        unset($_SESSION['gameId']);
        Redirect::to('/poista');
    }
}
